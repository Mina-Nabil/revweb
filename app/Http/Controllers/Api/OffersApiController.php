<?php

namespace App\Http\Controllers\Api;

use App\Models\Cars\Car;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferDoc;
use App\Models\Offers\OfferExtra;
use App\Models\Offers\OfferRequest;
use App\Models\Subscriptions\Plan;
use App\Models\Users\Buyer;
use App\Models\Users\Notification;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use App\Services\FilesHandler;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OffersApiController extends BaseApiController
{

    function submitNewOffer(Request $request)
    {
        parent::validate($request, [
            "requestID" =>  "required:offers_requests,id",
            "price"     =>  "required|numeric",
            "isLoan"    =>  "required|numeric",
            "downPayment"   =>  "required",
            "startDate"     =>  "required|date",
            "expiryDate"    =>  "required|date",
            "colors"        =>  "required|array",
            "options"        =>  "present|array"
        ]);
        $offerRequest = OfferRequest::findOrFail($request->requestID);
        $seller = $request->user();
        $seller->load('showroom');
        /** @var Showroom */
        $showroom = $seller->showroom;

        if ($showroom == null) {
            abort(403, "No showroom linked to the user");
        }

        $showroom->checkLimit(Plan::OFFERS_LIMIT, true);

        $newOffer = Offer::createOffer($offerRequest, $seller, $request->isLoan, $request->price, $request->downPayment, new DateTime($request->startDate), new DateTime($request->expiryDate), $request->colors, $request->options, $request->comment);
        if ($newOffer != null) {
            $car = $newOffer->car;
            parent::sendResponse(true, "Offers Request Created", (object)["offer" => $newOffer], false);
            $tmpNotf = Notification::newNotification(
                Notification::TYPE_OFFER_CREATED,
                "New Offer Reply!",
                "New offer received for {$car->model->brand->BRND_NAME} {$car->model->MODL_NAME} - {$car->CAR_CATG}",
                $offerRequest->buyer,
                [
                    "model"     =>  $car->model->MODL_NAME,
                    "brand"     =>  $car->model->brand->BRND_NAME
                ]
            );
            $tmpNotf->send();
        } else {
            parent::sendResponse(false, "Can't create offer");
        }
    }

    function submitOfferRequest(Request $request)
    {
        parent::validate($request, [
            "carID"     => "required:cars,id",
            "colors"    => "required|array",
            "options"   =>  "present|array",
            "pymtType"  => "required|in:" . OfferRequest::LOAN_KEY . ',' . OfferRequest::CASH_KEY
        ]);

        /** @var Buyer */
        $buyer = $request->user();
        $newRequest = OfferRequest::createRequest($buyer->id, $request->carID, $request->pymtType, $request->comment, $request->colors, $request->options);
        if ($newRequest != null) {
            parent::sendResponse(true, "Offers Request Created", $newRequest->fresh(), false);
            /** @var Car */
            $car = Car::with('model')->findOrFail($request->carID);
            $sellersSellingCar = Seller::getCarSellers($car->id, $request->colors);
            foreach ($sellersSellingCar as $seller) {
                $tmpNotf = Notification::newNotification(
                    Notification::TYPE_REQUEST_OFFER_CREATED,
                    "New Offer Request",
                    "New offer requested created for {$car->model->brand->BRND_NAME} {$car->model->MODL_NAME} - {$car->CAR_CATG}",
                    $seller,
                    [
                        "model"     =>  $car->model->MODL_NAME,
                        "brand"     =>  $car->model->brand->BRND_NAME
                    ]
                );
                $tmpNotf->send();
            }
        } else {
            parent::sendResponse(false, "Offers Request Failed", null, true, 500);
        }
    }

    function editOfferRequest($req_id, Request $request)
    {
        parent::validate($request, [
            "colors"    => "nullable|array",
            "pymtType"  => "required|in:" . OfferRequest::LOAN_KEY . ',' . OfferRequest::CASH_KEY
        ]);
        /** @var Buyer */
        $buyer = $request->user();
        /** @var OfferRequest */
        $offer_req = OfferRequest::findOrFail($req_id);
        if ($offer_req->owned_by($buyer)) {
            $res =  $offer_req->updateRequest($request->pymtType, $request->comment, $request->colors);
            if ($res) {
                parent::sendResponse(true, "Offers Request Updated", $offer_req->fresh(), false);
            } else {
                parent::sendResponse(false, "Offers Request Update Failed");
            }
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getBuyerRequests(Request $request)
    {
        /** @var Buyer */
        $buyer = $request->user();
        if ($buyer != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["requests" => $buyer->getActiveRequests()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getRequestsHistory(Request $request)
    {
        /** @var Buyer */
        $buyer = $request->user();
        if ($buyer != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["requests" => $buyer->getRequestsHistory()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getBuyerOffers(Request $request)
    {
        /** @var Buyer */
        $buyer = $request->user();
        if ($buyer != null) {
            parent::sendResponse(true, "Offers retrieved", (object)["offers" => $buyer->getActiveOffers()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getBuyerAcceptedOffers(Request $request)
    {
        /** @var Buyer */
        $buyer = $request->user();
        if ($buyer != null) {
            parent::sendResponse(true, "Offers retrieved", (object)["offers" => $buyer->getAcceptedOffers()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getBuyerOffersHistory(Request $request)
    {
        /** @var Buyer */
        $buyer = $request->user();
        if ($buyer != null) {
            parent::sendResponse(true, "Offers retrieved", (object)["offers" => $buyer->getAllOffers()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getOffer($id)
    {
        $offer = Offer::with(Offer::PROFILE_RELS)->findOrFail($id);
        parent::sendResponse(true, "Offer Retrieved", $offer);
    }

    function getOfferDocuments($id)
    {
        $offer = Offer::with('documents')->findOrFail($id);
        parent::sendResponse(true, "Docs Retrieved", (object)[
            "seller_docs"   =>  $offer->documents->where('is_seller', true)->values()->all(),
            "wanted_docs"   =>  $offer->documents->where('is_seller', false)->values()->all()
        ]);
    }

    function getOfferExtras($id)
    {
        $offer = Offer::with('extras')->findOrFail($id);
        parent::sendResponse(true, "Extras Retrieved", $offer->extras);
    }

    function acceptOffer(Request $request)
    {
        parent::validate($request, [
            "offer_id"      =>  "required|exists:offers,id",
            "comment"       =>  "nullable"
        ]);

        $offer = Offer::findOrFail($request->offer_id);
        $res = $offer->acceptOffer($request->comment);
        if ($res) {
            parent::sendResponse(true, "Succeeded", null, false);
            $tmpNotf = Notification::newNotification(
                Notification::TYPE_OFFER_ACCEPTED,
                "Offer Accepted!",
                "{$offer->buyer->BUYR_NAME} has accepted your offer. You can now check his contact details",
                $offer->seller,
                [
                    "model"     =>  $offer->car->model->MODL_NAME,
                    "brand"     =>  $offer->car->model->brand->BRND_NAME
                ]
            );
            $tmpNotf->send();
        } else {
            parent::sendResponse(false, "Failed");
        }
    }

    function cancelRequest($request_id)
    {
        /** @var OfferRequest */
        $request = OfferRequest::findOrFail($request_id);
        /** @var Buyer */
        $buyer = Auth::user();
        if ($request->owned_by($buyer)) {
            parent::sendResponse($request->setAsCancelled(), "N/A");
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getShowroomCompatibleOfferRequests(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        /** @var Showroom */
        $showroom = $seller->showroom;
        if ($showroom != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["requests" => $showroom->getAvailableOfferRequests()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getShowroomPendingOffers(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        /** @var Showroom */
        $showroom = $seller->showroom;
        if ($showroom != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["offers" => $showroom->getPendingOffers()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getShowroomApprovedOffers(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        $showroom = $seller->showroom;
        if ($showroom != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["offers" => $showroom->getApprovedOffers()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function getShowroomExpiredOffers(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        $showroom = $seller->showroom;
        if ($showroom != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["offers" => $showroom->getExpiredOffers()]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function extendOffer(Request $request)
    {
        parent::validate($request, [
            "offerID"   =>  "required"
        ]);
        /** @var Offer */
        $offer = Offer::findOrFail($request->offerID);
        if ($offer->extendOffer(new DateInterval('P2D'))) //add two days
        {
            parent::sendResponse(true, "Offer Extended", (object)["offer" =>  $offer->refresh()]);
        } else      parent::sendResponse(false, "Offer Extension failed");
    }

    function extendAllPendingOffers(Request $request)
    {

        $seller = $request->user();
        $seller->load('showroom');
        /** @var Showroom */
        $showroom = $seller->showroom;
        if ($showroom != null) {
            /** @var Offer[] */
            $offers = $showroom->getPendingOffers();
            $range = new DateInterval('P2D');
            foreach ($offers as $offer) {
                $offer->extendOffer($range);
            }
            parent::sendResponse(true, "Offers Extension succeeded");
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }

    function addDocument(Request $request)
    {
        parent::validate($request, [
            "offer_id"  =>  "required:exists:offers,id",
            "title"     =>  "required",
            "document"  =>  "file|nullable|mimes:jpg,pdf,png",
            "note"      =>  "nullable"
        ]);
        /** @var Offer */
        $offer = Offer::findOrFail($request->offer_id);
        $doc_url = null;
        $filesHandler = new FilesHandler();
        if ($request->hasFile('document')) {
            $doc_url = $filesHandler->uploadFile($request->document, "offers/" . $request->offer_id . "/docs");
        }
        $doc = $offer->addDocument($request->title, $doc_url, $request->note);
        if ($doc) {
            parent::sendResponse(true, "Doc Uploaded", $doc);
        } else {
            parent::sendResponse(false, "Something is wrong");
        }
    }

    function addExtra(Request $request)
    {
        parent::validate($request, [
            "offer_id"  =>  "required:exists:offers,id",
            "title"     =>  "required",
            "image"     =>  "file|nullable|mimes:jpg,pdf,png",
            "price"     =>  "required|numeric",
            "note"      =>  "nullable"
        ]);
        /** @var Offer */
        $offer = Offer::findOrFail($request->offer_id);
        $image_url = null;
        $filesHandler = new FilesHandler();
        if ($request->hasFile('image')) {
            $image_url = $filesHandler->uploadFile($request->document, "offers/" . $request->offer_id . "/extras");
        }
        if ($offer->addExtra($request->title, $request->price, $request->note, $image_url)) {
            parent::sendResponse(true, "Extra Uploaded");
        } else {
            parent::sendResponse(false, "Something is wrong");
        }
    }

    function uploadDocument(Request $request)
    {
        parent::validate($request, [
            "id"        =>  "required|exists:offer_docs",
            "document"  =>  "file|required|mimes:jpg,pdf,png"
        ]);
        /** @var OfferDoc */
        $offerDoc = OfferDoc::with('offer')->findOrFail($request->id);

        $user = Auth::user();
        if (
            (is_a($user, Buyer::class) && $user->id != $offerDoc->offer->OFFR_BUYR_ID)
            || (is_a($user, Seller::class) && $user->id != $offerDoc->offer->OFFR_SLLR_ID)
        ) {
            abort(403, "Unauthorized user");
        }

        if (!$request->hasFile('document'))  parent::sendResponse(false, "No file to upload");

        $doc_url = null;
        $filesHandler = new FilesHandler();
        $doc_url = $filesHandler->uploadFile($request->document, "offers/" . $offerDoc->OFDC_OFFR_ID . "/docs");

        if ($offerDoc->setUrl($doc_url)) {
            parent::sendResponse(true, "Doc Uploaded", $offerDoc->fresh());
        } else {
            $filesHandler->deleteFile($doc_url);
            parent::sendResponse(false, "Something is wrong");
        }
    }

    function deleteDoc($id)
    {
        /** @var OfferDoc */
        $doc = OfferDoc::findOrFail($id);
        if ($doc->delete()) {
            parent::sendResponse(true, "Doc Deleted");
        } else {
            parent::sendResponse(false, "Deletion failed");
        }
    }

    function deleteDocImage($id)
    {
        /** @var OfferDoc */
        $doc = OfferDoc::findOrFail($id);
        if ($doc->deleteImage()) {
            parent::sendResponse(true, "Doc Deleted");
        } else {
            parent::sendResponse(false, "Deletion failed");
        }
    }

    function deleteExtra($id)
    {
        /** @var OfferExtra */
        $extra = OfferExtra::findOrFail($id);
        if ($extra->delete()) {
            parent::sendResponse(true, "Extra Deleted");
        } else {
            parent::sendResponse(false, "Deletion failed");
        }
    }

    function cancelOffer(Request $request)
    {
        parent::validate($request, [
            "offerID"   =>  "required",
            "comment"   =>  "nullable"
        ]);
        /** @var Offer */
        $offer = Offer::findOrFail($request->offerID);
        if ($offer->cancelOffer($request->comment)) //add two days
        {
            parent::sendResponse(true, "Offer Cancelled", (object)["offer" =>  $offer->refresh()]);
        } else      parent::sendResponse(false, "Offer Cancellation failed");
    }
}
