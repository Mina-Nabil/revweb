<?php

namespace App\Http\Controllers\Api;

use App\Models\Cars\Car;
use App\Models\Offers\OfferRequest;
use App\Services\PushNotificationsHandler;
use Illuminate\Http\Request;

class OffersApiController extends BaseApiController
{

    function submitOfferRequest(Request $request)
    {
        $request->validate([
            "carID"     => "required:cars,id",
            "colors"    => "nullable|array",
            "pymtType"  => "required|in:" . OfferRequest::LOAN_KEY . ',' . OfferRequest::CASH_KEY ,
        ]);
        $buyer = $request->user();
        $newRequest = OfferRequest::createRequest($buyer->id, $request->carID, $request->pymtType, $request->comment, $request->colors);
        if ($newRequest != null) {
            parent::sendResponse(true, "Offers Request Created", $newRequest->fresh(), false);
            $car = Car::findOrFail($request->carID);
            $pushService = new PushNotificationsHandler();
            $pushService->sendPushNotification("New Offer Request", $car->model->brand->BRND_NAME . " " . $car->model->MODL_NAME . " request submitted" , [], "route/to/offer");
        } else {
        }
    }
}
