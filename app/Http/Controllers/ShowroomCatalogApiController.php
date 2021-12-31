<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use Illuminate\Http\Request;

class ShowroomCatalogApiController extends BaseApiController
{

    function getCatalog(Request $request)
    {
        $seller = $request->user();
        $seller->load("showroom");
        $showroom = $seller->showroom;
        if ($showroom == NULL) {
            parent::sendResponse(false, "Failed to load Showroom");
        }
        parent::sendResponse(true, "Catalog", (object)["catalog" => $showroom->getCatalogCars()]);
    }

    function addCar(Request $request)
    {
        parent::validateRequest($request, [
            "carIDs"         =>  "required|exists:cars,id|array"
        ], "Car addition failed");

        $seller = $request->user();
        $showroom = $seller->showroom;
        if ($showroom == NULL) {
            parent::sendResponse(false, "Failed to load Showroom");
        }
        foreach ($request->carIDs as $carID) {
            $showroom->addCarToCatalog($carID, $request->{'colors' . $carID});
        }
        parent::sendResponse(true, "Car Adding Succeeded");
    }

    function deactivateCar(Request $request)
    {
        parent::validateRequest($request, [
            "carID"         =>  "required|exists:cars,id",
        ], "Car not found");
        $seller = $request->user();
        $showroom = $seller->showroom;
        if ($showroom == NULL) {
            parent::sendResponse(false, "Failed to load Showroom");
        }
        if ($showroom->deactivateCarFromCatalog($request->carID)) {
            parent::sendResponse(true, "Car Removal Succeeded");
        } else {
            parent::sendResponse(false, "Car Removal Failed");
        }
    }

    function removeCar(Request $request)
    {
        parent::validateRequest($request, [
            "carID"         =>  "required|exists:cars,id",
        ], "Car not found");
        $seller = $request->user();
        if ($seller->deleteCarFromCatalog($request->carID)) {
            parent::sendResponse(true, "Car Removal Succeeded");
        } else {
            parent::sendResponse(false, "Car Removal Failed");
        }
    }

    /**
     * Set the brands associated with the seller
     * 
     */
    function setBrands(Request $request)
    {
        parent::validateRequest($request, [
            "brandIDs" => "required|array"
        ]);

        $seller = $request->user();
        $seller->load("showroom");
        $showroom = $seller->showroom;
        if ($showroom == NULL) {
            parent::sendResponse(false, "Failed to load Showroom");
        }

        $response = $showroom->setBrands($request->brandIDs);
        if ($response)
            parent::sendResponse(true, "Brands Update: " . count($response["attached"]) . " added " . count($response["detached"]) . " removed", $response);
        else
            parent::sendResponse(false, "Brands Updating Failed");
    }

    /***
     * gets all brands from the system
     */
    function getAllBrands()
    {
        parent::sendResponse(true, "Brands Retrieved", (object) ["brands" => Brand::getActive()]);
    }

    /***
     * gets models by brands
     */
    function getModelsByBrand($brandID)
    {
        $brand = Brand::findOrFail($brandID);
        parent::sendResponse(true, "Models Retrieved", $brand->activeModels());
    }

    /***
     * gets cars by model
     */
    function getCarsByModel($modelID)
    {
        $cars = Car::getCarsByModel($modelID);
        parent::sendResponse(true, "Cars Retrieved", $cars);
    }

    /***
     * gets colors by model
     */
    function getColorsByModel($modelID)
    {
        $model = CarModel::findOrFail($modelID);
        $model->load('colors');
        parent::sendResponse(true, "Colors Retrieved", $model->colors);
    }
}
