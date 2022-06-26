<?php

namespace App\Http\Controllers\Api;

use App\Models\Cars\Brand;
use App\Models\Users\Buyer;
use App\Services\FilesHandler;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuyersDataApi extends BaseApiController
{
    /***
     * gets all brands for the buyer app
     */
    public function brands()
    {
        parent::sendResponse(true, "Brands Retrieved", (object) ["brands" => Brand::getActive()]);
    }


    /***
     * gets models by brands
     */
    public function models($brandID)
    {
        $brand = Brand::findOrFail($brandID);
        parent::sendResponse(true, "Models Retrieved", $brand->activeModels());
    }
}
