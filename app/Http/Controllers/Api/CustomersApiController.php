<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class CustomersApiController extends BaseApiController
{
    public function getCustomers(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        /** @var Showroom */
        $showroom = $seller->showroom;
        if ($showroom != null) {
            $seller->load('buyers');
            parent::sendResponse(true, "Buyers Retrieved", (object)[
                "buyers"    =>  $seller->buyers
            ]);
        } else {
            parent::sendResponse(false, "Unautherized", null, true, 403);
        }
    }
}
