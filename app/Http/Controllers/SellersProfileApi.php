<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Services\FilesHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SellersProfileApi extends AbstractApiController
{
    function register(Request $request)
    {
        parent::validateRequest($request, [
            "deviceName"    => "required",
            "name"          => "required",
            "email"         => "required|unique:sellers,SLLR_MAIL",
            "password"      => "required|min:6",
            "mobNumber1"    => "required|unique:sellers,SLLR_MOB1",
            "displayImage"  =>  "nullable|image|size:10000", //10 MB max

        ], "Seller Registration Failed");
        $filesHandler = new FilesHandler();
        $displayImageFilePath = null;
        if ($request->hasFile("displayImage")) {
            $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "sellers/" . $request->email . '/ids//');
        }
        $newSeller = null;
        $error = null;
        $failed = true;
        try {
            $newSeller = Seller::create($request->name, $request->email, $request->mobNumber1, $request->password, $request->mobNumber2, $displayImageFilePath);
            $failed = false;
        } catch (Exception $e) {
            $error = $e;
        }

        if ($failed || $newSeller == null) {
            parent::sendResponse(false, "Registration Failed", ["Message" => $error->getMessage()], false);
            $filesHandler->deleteFile($displayImageFilePath);
        }
        parent::sendResponse(true, "Registration Succeeded!", (object)["seller" => $newSeller, "token" => $newSeller->getApiToken($request->deviceName)]);
    }

    function getUser(Request $request)
    {
        parent::sendResponse(true, "User Retrieved Successfully", (object)["user" => $request->user()]);
    }

    function login(Request $request)
    {
        parent::validateRequest($request, [
            "identifier" => "required",
            "password" => "required",
            'deviceName' => 'required'
        ], "Invalid Login Request");
        $loginResponse = Seller::login($request->identifier, $request->password, $request->deviceName);
        if ($loginResponse == -1) { //Incorrect Email/Mobile Number
            parent::sendResponse(false, "Email/Mobile Number can't be found");
            die;
        } else if ($loginResponse == -2) {
            parent::sendResponse(false, "Incorrect Password");
            die;
        } else if (is_string($loginResponse)) {
            parent::sendResponse(true, "Login Succeeded", ["apiKey" => $loginResponse]);
        }
    }

    function addSellerProfileImage(Request $request)
    {
        parent::validateRequest($request, [
            "displayImage"  =>  "nullable|image|size:10000", //10 MB max
        ]);
        $seller = $request->user();
        $filesHandler = new FilesHandler();
        if ($request->hasFile("displayImage")) {
            $displayImageFilePath = $filesHandler->uploadFile($request->sellerIDFront, "sellers/" . $seller->SLLR_MAIL . '/ids//');
        }
        if ($displayImageFilePath && $seller->setImage($displayImageFilePath)) {
            parent::sendResponse(true, "Image Added Successfully");
        } else {
            parent::sendResponse(false, "Image Submission Failed");
        }
    }

    function updateSellerData(Request $request)
    {
        $seller = $request->user();

        parent::validateRequest($request, [
            "name"          => "required",
            "email"         => ["required", Rule::unique('sellers', "SLLR_NAME")->ignore($seller->SLLR_NAME, "SLLR_NAME")],
            "password"      => "nullable|min:6",
            "mobNumber1"     => ["required", Rule::unique('sellers', "SLLR_MOB1")->ignore($seller->SLLR_MOB1, "SLLR_MOB1")],
            "displayImage"  =>  "nullable|image|size:10000", //10 MB max

        ], "Seller Update Failed");

        $res = $seller->updateInfo($request->name, $request->email, $request->mobNumber1, $request->password, $request->mobNumber2);
        if ($res) {
            parent::sendResponse(true, "Seller updated Successfully");
        } else {
            parent::sendResponse(false, "Seller updated Failed");
        }
    }
}
