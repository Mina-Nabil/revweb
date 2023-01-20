<?php

namespace App\Http\Controllers\Api;

use App\Models\Users\Showroom;
use App\Subscriptions\Plan;
use App\Subscriptions\Subscription;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionsController extends BaseApiController
{
    public function addSubscriptions(Request $request)
    {
        parent::validateRequest($request, [
            "plan_id"   =>  "required|exists:plans.id",
            "type"      =>  "required|in:" . implode(",", Subscription::SUBSCRIPTION_TYPES),
            "days"      =>  "required|numeric", //number of days
            "amount"    =>  "required", //amount paid
            "transaction_id"    =>  "required"
        ]);
        /** @var Seller */
        $seller = Auth::user();
        /** @var Showroom */
        $showroom = $seller->showroom;
        $ret = $showroom->addSubscription($request->plan_id, $request->type, $request->days,  $request->amount, $request->transaction_id);
        if (is_a($ret, Subscription::class)) {
            parent::sendResponse(true, "Subscription succeeded", [
                "subscription"  =>  $ret,
            ], true);
        } else {
            parent::sendResponse(false, "Subscription false");
            throw (new Exception("Subscription failed. Debug info " . print_r($ret, true)));
        }
    }

    public function plans()
    {
        parent::sendResponse(true, "Success", [
            "plans"     =>  Plan::all()
        ]);
    }
}
