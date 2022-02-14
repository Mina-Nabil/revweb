<?php

namespace App\Http\Controllers\Api;

use App\Models\Cars\Car;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferRequest;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use App\Services\PushNotificationsHandler;
use DateTime;
use Illuminate\Http\Request;

class OffersApiController extends BaseApiController
{

    function submitNewOffer(Request $request)
    {
        parent::validate($request, [
            "requestID" =>  "required:offers_requests,id",
            "price"     =>  "required|numeric",
            "isLoan"    =>  "required",
            "downPayment"   =>  "required",
            "startDate"     =>  "required|date",
            "expiryDate"    =>  "required|date",
            "colors"        =>  "required|array"
        ]);
        $offerRequest = OfferRequest::findOrFail($request->requestID);
        $seller = $request->user();
        $newOffer = Offer::createOffer($offerRequest, $seller, $request->isLoan, $request->price, $request->downPayment, new DateTime($request->startDate), new DateTime($request->expiryDate), $request->colors, $request->comment);
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
            "pymtType"  => "required|in:" . OfferRequest::LOAN_KEY . ',' . OfferRequest::CASH_KEY
        ]);
        $buyer = $request->user();
        $newRequest = OfferRequest::createRequest($buyer->id, $request->carID, $request->pymtType, $request->comment, $request->colors);
        if ($newRequest != null) {
            parent::sendResponse(true, "Offers Request Created", $newRequest->fresh(), false);
            $car = Car::findOrFail($request->carID);
            $pushService = new PushNotificationsHandler();
            $sellersSellingCar = Seller::getCarSellers($car->id, $request->colors);
            $pushService->sendPushNotification("New Offer Request", $car->model->brand->BRND_NAME . " " . $car->model->MODL_NAME . " request submitted", $sellersSellingCar->pluck('id'), "route/to/offer");
        } else {
        }
    }

    function getShowroomCompatibleOfferRequests(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        $showroom = $seller->showroom;
        if ($showroom != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["requests" => $showroom->getAvailableOfferRequests()]);
        } else {
            parent::sendResponse(false, "Unautherized", null, true, 403);
        }
    }

    function getShowroomPendingOffers(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        $showroom = $seller->showroom;
        if ($showroom != null) {
            parent::sendResponse(true, "Offer Requests retrieved", (object)["offers" => $showroom->getPendingOffers()]);
        } else {
            parent::sendResponse(false, "Unautherized", null, true, 403);
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
            parent::sendResponse(false, "Unautherized", null, true, 403);
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
            parent::sendResponse(false, "Unautherized", null, true, 403);
        }
    }
}
