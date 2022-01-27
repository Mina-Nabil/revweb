<?php

namespace App\Http\Controllers\Api;

use App\Models\Cars\Car;
use App\Models\Offers\OfferRequest;
use App\Models\Users\Seller;
use App\Services\PushNotificationsHandler;
use Illuminate\Http\Request;

class OffersApiController extends BaseApiController
{

    function submitOfferRequest(Request $request)
    {
        $request->validate([
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

    function getShowroomCompatibleOfferRequests(Request $request){
        $seller = $request->user();
        $seller->load('showroom');
        $showroom = $seller->showroom;
        if($showroom!=null){
            
        } else {
            parent::sendResponse(false, "Unautherized", null, true, 403);
        }
    }

}
