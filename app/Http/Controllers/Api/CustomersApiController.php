<?php

namespace App\Http\Controllers\Api;

use App\Models\Users\Showroom;
use Illuminate\Http\Request;

class CustomersApiController extends BaseApiController
{
    public function getCustomers(Request $request)
    {
        $seller = $request->user();
        $seller->loadMissing('showroom');
        /** @var Showroom */
        $showroom = $seller->showroom;
        $buyersOffers = $showroom->offers()->with('buyer', 'car')->get();
        if ($showroom != null) {
            parent::sendResponse(true, "Buyers Retrieved", (object)[
                "soldOffers"    =>  $buyersOffers
            ]);
        } else {
            parent::sendResponse(false, "Unauthorized", null, true, 403);
        }
    }
}
