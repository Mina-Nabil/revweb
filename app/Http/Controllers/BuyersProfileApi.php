<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Services\FilesHandler;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class BuyersProfileApi extends AbstractApiController
{
    function register(Request $request)
    {
        $maxBday = new DateTime("now");
        date_sub($maxBday, date_interval_create_from_date_string('15 year'));

        if (parent::validateRequest($request, [
            "name"          => "required",
            "email"         => "required|unique:buyers,BUYR_MAIL",
            "mobNumber1"    => "required|unique:buyers,BUYR_MOB1|size,11",
            "mobNumber1"    => "required|unique:buyers,BUYR_MOB2",
            "password"      => "required|size:8",
            "deviceName"    => "required",
            "mobNumber2"    => "nullable|size:11",
            "gender"        => ['required', Rule::in(["Male", "Female", "Prefer not to Say"])],
            "bday"          => "required|date|after:1930-01-01|before:" . $maxBday->format('Y-01-01'),
            "nationalID"    =>  "nullable|numeric",
            "displayImage"  =>  "nullable|image|size:10000", //10 MB max
            "nationalIDFront"  =>  "nullable|image|size:10000", //10 MB max
            "nationalIDBack"  =>  "nullable|image|size:10000", //10 MB max

        ])) {
            $filesHandler = new FilesHandler();
            $displayImageFilePath = null;
            $nationalIDFrontFilePath = null;
            $nationalIDBackFilePath = null;
            if ($request->hasFile("displayImage")) {
                $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "buyers/" . $request->email . '/ids//');
            }
            if ($request->hasFile("nationalIDFront")) {
                $nationalIDFrontFilePath = $filesHandler->uploadFile($request->nationalIDFront, "buyers/" . $request->email . '/ids//');
            }
            if ($request->hasFile("nationalIDBack")) {
                $nationalIDBackFilePath = $filesHandler->uploadFile($request->nationalIDBack, "buyers/" . $request->email . '/ids//');
            }
            $newBuyer = null;
            $error = null;
            $failed = true;
            try {
                $newBuyer = Buyer::create($request->name, $request->email, $request->mobNumber1, $request->bday, $request->gender, $request->password, $request->nationalID, $request->mobNumber2, $request->bankAccount, $request->iban, $displayImageFilePath, $nationalIDFrontFilePath, $nationalIDBackFilePath);
                $failed = false;
            } catch (Exception $e) {
                $error = $e;
            }

            if ($failed || $newBuyer == null) {
                parent::sendResponse(false, "Registration Failed", ["Message" => $error->getMessage()], false);
                $filesHandler->deleteFile($displayImageFilePath);
                $filesHandler->deleteFile($nationalIDFrontFilePath);
                $filesHandler->deleteFile($nationalIDBackFilePath);
            }
            parent::sendResponse(true, "Registration Succeeded!", ["buyer" => $newBuyer, "token" => $newBuyer->getApiToken($request->deviceName)]);
        }
    }

    function login(Request $request)
    {
        if (parent::validateRequest($request, [
            "identifier" => "required",
            "password" => "required",
            'deviceName' => 'required'
        ])) {
            $loginResponse = Buyer::login($request->identifier, $request->password, $request->deviceName);
            if ($loginResponse == -1) { //Incorrect Email/Mobile Number
                parent::sendResponse(false, "Email/Mobile Number can't be found");
            } else if ($loginResponse == -2) {
                parent::sendResponse(false, "Incorrect Password");
            } else if (is_string($loginResponse)) {
                parent::sendResponse(true, "Login Succeeded", ["apiKey" => $loginResponse]);
            }
        }
    }

    function getUser(Request $request)
    {
        parent::sendResponse(true, "Current User Retrieved", $request->user());
    }

    function addImage()
    {
    }

    function addNationalID(Request $request)
    {
        if (parent::validateRequest($request, [
            "nationalID"    =>  "required",
            "nationalIDFront"  =>  "required|image|size:10000", //10 MB max
            "nationalIDBack"  =>  "required|image|size:10000", //10 MB max
        ])) {
            $filesHandler = new FilesHandler();
            $buyer = $request->user();
            if ($request->hasFile("nationalIDFront")) {
                $nationalIDFrontFilePath = $filesHandler->uploadFile($request->nationalIDFront, "buyers/" . $buyer->BUYR_MAIL . '/ids//');
            }
            if ($request->hasFile("nationalIDBack")) {
                $nationalIDBackFilePath = $filesHandler->uploadFile($request->nationalIDBack, "buyers/" . $buyer->BUYR_MAIL . '/ids//');
            }
            if ($buyer->addBuyerNationalID($request->nationalID, $nationalIDFrontFilePath, $nationalIDBackFilePath))
                parent::sendResponse(true, "Adding Succeeded");
            else
                parent::sendResponse(false, "Adding Failed");
        }
    }

    function verifyMobileNumber(Request $request)
    {
        if (parent::validateRequest($request, [
            "whichMobNumber" => "required",
        ])) {
            $buyer = $request->user();
            if ($request->whichMobNumber == 1) {
                if ($buyer->initiateMobileNumber1Verification() === true) {
                    parent::sendResponse(false, "Mobile Number already verified");
                } else {
                    parent::sendResponse(true, "Message Sent");
                }
            } else if ($request->whichMobNumber == 2) {
                $iniaterReq = $buyer->initiateMobileNumber2Verification();
                if ($iniaterReq === true) {
                    parent::sendResponse(false, "Mobile Number already verified");
                } else if ($iniaterReq === false) {
                    parent::sendResponse(false, "Mobile Number is Missing");
                } else {
                    parent::sendResponse(true, "Message Sent");
                }
            }
        }
    }

    function confirmMobileNumber(Request $request)
    {
        if (parent::validateRequest($request, [
            "code"      =>  "required",
            "isMob1"    =>  "required"
        ])) {
            $buyer = $request->user();
            if ($request->isMob1)
                $buyer->verifyMobileNumber1Code($request->code);
            else
                $buyer->verifyMobileNumber2Code($request->code);
        }
    }

    function verifyEmail(Request $request)
    {
    }
}
