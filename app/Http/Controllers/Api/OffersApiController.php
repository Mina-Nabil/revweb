<?php

namespace App\Http\Controllers\Api;

use App\Models\Cars\Car;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferRequest;
use App\Models\Subscriptions\Plan;
use App\Models\Users\Buyer;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use App\Notifications\RequestOfferCreated;
use App\Services\PushNotificationsHandler;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OffersApiController extends BaseApiController
{

    function submitNewOffer(Request $request)
    {
        Log::debug($request->all());
        parent::validate($request, [
            "requestID" =>  "required:offers_requests,id",
            "price"     =>  "required|numeric",
            "isLoan"    =>  "required|numeric",
            "downPayment"   =>  "required",
            "startDate"     =>  "required|date",
            "expiryDate"    =>  "required|date",
            "colors"        =>  "required|array",
            "options"        =>  "required|array"
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
            parent::sendResponse(true, "Offers Request Created", (object)["offer" => $newOffer], false);
            $pushService = new PushNotificationsHandler();
            $pushService->sendPushNotification("Offer Submitted", "New offer submitted for " . $offerRequest->car->name, [$offerRequest->buyer->id], "route/to/offer");
        } else {
            parent::sendResponse(false, "Can't create offer");
        }
    }

    function submitOfferRequest(Request $request)
    {
        parent::validate($request, [
            "carID"     => "required:cars,id",
            "colors"    => "nullable|array",
            "options"    => "nullable|array",
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
                Log::debug($seller->SLLR_MAIL);
                /** @var Seller */
                $seller->notify(new RequestOfferCreated("Seller", $seller->id, $car->model->brand->BRND_NAME, $car->model->title, $car->CAR_CATG, $car->id));
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
        $request->validate([
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


    function cancelOffer(Request $request)
    {
        $request->validate([
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
