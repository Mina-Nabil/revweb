<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Showroom;
use App\Rules\Iban;
use App\Services\FilesHandler;
use Exception;
use Illuminate\Http\Request;

class ShowroomProfileApi extends AbstractApiController
{
    function createShowroom(Request $request)
    {
        parent::validateRequest($request, [
            "name"          => "required",
            "email"         => "required|email|unique:showrooms,SHRM_MAIL",
            "mobNumber1"    => "required|unique:showrooms,SHRM_MOB1",
            "address"    => "required",
            "cityID"    => "required|exists:cities,id",
            "displayImage"  =>  "nullable|image|size:10000", //10 MB max
        ], "Showroom Creation Failed");

        $filesHandler = new FilesHandler();
        $displayImageFilePath = null;
        $seller = $request->user();
        if ($request->hasFile("displayImage")) {
            $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "showrooms/" . $request->email . '/ids//');
        }

        $newShowroom = null;
        $error = null;
        $failed = true;
        try {
            $newShowroom = Showroom::create($request->name, $request->email, $request->mobNumber1, $request->cityID, $request->address, $seller->id, $request->mobNumber2, $displayImageFilePath);
            $seller->setShowroom($newShowroom->id);
            $failed = false;
        } catch (Exception $e) {
            $error = $e;
        }

        if ($failed || $newShowroom == null) {
            parent::sendResponse(false, "Registration Failed", ["Message" => $error->getMessage()], false);
            $filesHandler->deleteFile($displayImageFilePath);
            die;
        }
        parent::sendResponse(true, "Registration Succeeded!", (object)["showroom" => $newShowroom]);
    }

    function getShowroom(Request $request)
    {
        $seller = $request->user();
        $seller->load("showroom");
        if ($seller->showroom == NULL)  parent::sendResponse(false, "Unable to load Showroom");
        $seller->showroom->SHRM_CAN_MNGR = $seller->showroom->isManager();
        parent::sendResponse(true, "Showroom Successfully Retrieved", $seller->showroom);
    }

    function setBankInfo(Request $request)
    {
        parent::validateRequest($request, [
            "iban"                      => ["required", new Iban()],
            "bankAccountHolderName"     => "required",
            "bankBranch"                => "required",
            "bankAccountNo"             => "nullable",
        ], "Adding Banking Information Failed");
        $seller = $request->user();
        $seller->load('showroom');
        $showroom = $seller->showroom;
        if ($showroom == NULL)  parent::sendResponse(false, "Unable to load Showroom");
        if (!$showroom->isOwner()) {
            parent::sendResponse(false, "Unauthorized");
        }
        if ($showroom->setBankInfo($request->bankBranch, $request->bankAccountHolderName, $request->bankAccountNo, $request->iban)) {
            parent::sendResponse(true, "Banking Info Added");
        } else {
            parent::sendResponse(false, "Banking Info Failed");
        }
    }

    function addCommercialRecord(Request $request)
    {
        parent::validateRequest($request, [
            "record"        => "required",
            "showroomIDFront"  =>  "required|image|size:10000", //10 MB max
            "showroomIDBack"  =>  "required|image|size:10000", //10 MB max
        ], "Adding Commercial Record Failed");
        $seller = $request->user();
        $showroom = $seller->showroom;
        if (!$showroom->isOwner()); {
            parent::sendResponse(false, "Unauthorized");
        }
        $filesHandler = new FilesHandler();
        if ($request->hasFile("showroomIDFront")) {
            $showroomRecordImgFront = $filesHandler->uploadFile($request->showroomIDFront, "showrooms/" . $showroom->SHRM_MAIL . '/ids//');
        }
        if ($request->hasFile("showroomIDBack")) {
            $showroomRecordImgBack = $filesHandler->uploadFile($request->showroomIDBack, "showrooms/" . $showroom->SHRM_MAIL . '/ids//');
        }
        if ($showroomRecordImgFront != null && $showroomRecordImgBack != null && $showroom->addShowroomRecord($request->record, $showroomRecordImgFront, $showroomRecordImgBack)) {
            parent::sendResponse(true, "Adding Commercial Record Succeeded, Pending Confirmation");
        } else {
            parent::sendResponse(false, "Adding Commercial Record Failed");
            $filesHandler->deleteFile($showroomRecordImgFront);
            $filesHandler->deleteFile($showroomRecordImgBack);
        }
    }

    public function getCities(){
        parent::sendResponse(true, "Cities loaded successfully", Country::with("cities")->get());
    }
}
