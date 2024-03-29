<?php

namespace App\Http\Controllers\Api;

use App\Models\Users\JoinRequest;
use App\Models\Users\Notification;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use App\Services\FilesHandler;
use App\Services\PushNotificationsHandler;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SellersProfileApi extends BaseApiController
{
    function register(Request $request)
    {
        parent::validateRequest($request, [
            "deviceName"    => "required",
            "name"          => "required",
            "email"         => "required|unique:sellers,SLLR_MAIL",
            "password"      => "required|min:6",
            "mobNumber1"    => "required|unique:sellers,SLLR_MOB1",
            "displayImage"  =>  "nullable|image|between:0,10000", //10 MB max

        ], "Seller Registration Failed");
        $filesHandler = new FilesHandler();
        $displayImageFilePath = null;
        if ($request->hasFile("displayImage")) {
            $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "sellers/" . $request->email . '/photos');
        }
        $newSeller = null;
        $error = null;
        $failed = true;
        try {
            /** @var Seller */
            $newSeller = Seller::create($request->name, $request->email, $request->mobNumber1, $request->password, $request->mobNumber2, $displayImageFilePath);
            $failed = false;
            $newSeller->initiateMobileNumber1Verification();
        } catch (Exception $e) {
            $error = $e;
        }

        if ($failed || $newSeller == null) {
            parent::sendResponse(false, "Registration Failed", ["Message" => $error->getMessage()], false);
            $filesHandler->deleteFile($displayImageFilePath);
        }
        parent::sendResponse(true, "Registration Succeeded!", (object)["seller" => $newSeller, "token" => $newSeller->getApiToken($request->deviceName)]);
    }


    function editUser(Request $request)
    {
        /** @var Seller */
        $user = Auth::user();

        if (parent::validateRequest($request, [
            "name"          => "required",
            "mobNumber1"    => "required|unique:sellers,SLLR_MOB1," . $user->id . ",|size:11",
            "mobNumber2"    => "nullable|unique:sellers,SLLR_MOB1," . $user->id,
            // "password"      => "required|min:8",
            // "deviceName"    => "required",
            // "nationalID"    =>  "nullable|numeric",
            "displayImage"  =>  "nullable|image|size:10000", //10 MB max
            // "nationalIDFront"  =>  "nullable|image|size:10000", //10 MB max
            // "nationalIDBack"  =>  "nullable|image|size:10000", //10 MB max

        ])) {
            $filesHandler = new FilesHandler();
            $displayImageFilePath = null;

            if ($request->hasFile("displayImage")) {
                $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "sellers/" . $request->email . '/photos');
            }
            $res = $user->updateInfo($request->name, $request->mobNumber1, $request->mobNumber2, $displayImageFilePath);
            if (!$res) {
                parent::sendResponse(false, "Registration Failed");
                $filesHandler->deleteFile($displayImageFilePath);
            } else {
                parent::sendResponse(true, "Registration Succeeded!", ["seller" => $user]);
            }
        }
    }

    function getUser(Request $request)
    {
        $seller = $request->user();
        $seller->load("showroom");
        parent::sendResponse(true, "User Retrieved Successfully", (object)["user" => $seller]);
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
        } else if (is_array($loginResponse)) {
            parent::sendResponse(true, "Login Succeeded", $loginResponse);
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
            $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "sellers/" . $seller->SLLR_MAIL . '/ids');
        }
        if ($displayImageFilePath && $seller->setImage($displayImageFilePath)) {
            parent::sendResponse(true, "Image Added Successfully");
        } else {
            parent::sendResponse(false, "Image Submission Failed");
        }
    }

    function updateSellerData(Request $request)
    {
        /** @var Seller */
        $seller = $request->user();

        parent::validateRequest($request, [
            "name"          => "required",
            "email"         => ["required", Rule::unique('sellers', "SLLR_MAIL")->ignore($seller->SLLR_MAIL, "SLLR_MAIL")],
            "password"      => "nullable|min:6",
            "mobNumber1"     => ["required", Rule::unique('sellers', "SLLR_MOB1")->ignore($seller->SLLR_MOB1, "SLLR_MOB1")],
            "displayImage"  =>  "nullable|image|size:10000", //10 MB max

        ], "Seller Update Failed");
        $filesHandler = new FilesHandler();
        $displayImageFilePath = null;
        if ($request->hasFile("displayImage")) {
            $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "sellers/" . $request->email . '/photos');
        }
        $res = $seller->updateInfo($request->name, $request->mobNumber1, $request->mobNumber2, $displayImageFilePath);

        if ($res) {
            parent::sendResponse(true, "Seller updated Successfully");
        } else {
            parent::sendResponse(false, "Seller updated Failed");
        }
    }

    function acceptShowroomInvitation(Request $request)
    {
        parent::validateRequest($request, [
            "joinRequestID" => "required|exists:join_requests,id"
        ]);
        $seller = $request->user();
        $ret = $seller->acceptJoinInvitation($request->joinRequestID);
        if ($ret) {
            parent::sendResponse(true, "Request Accepted", null, false);
            $joinRequest = JoinRequest::findOrFail($request->joinRequestID);
            $joinRequest->load('showroom');
            $managersIDs = $joinRequest->showroom->getManagers();
            foreach ($managersIDs as $manId) {
                $tmpSeller = Seller::find($manId);
                if ($tmpSeller) {
                    $tmpNotf = Notification::newNotification(
                        Notification::TYPE_SALES_JOIN_ACCEPT,
                        "Team Invitation Accepted",
                        $seller->SLLR_NAME . " has joined your Team!",
                        $tmpSeller,
                        []
                    );
                    $tmpNotf->send();
                }
            }
        } else
            parent::sendResponse(false, "Operation Failed");
    }

    function searchShowrooms(Request $request)
    {
        parent::validateRequest($request, [
            "searchText" => "required|string"
        ]);
        if (is_string($request->searchText) && strlen($request->searchText) > 2) {
            $res = Showroom::searchText($request->searchText);
            parent::sendResponse(true, "Sellers Retrieved", (object) ["showrooms" =>  $res]);
        } else {
            parent::sendResponse(true, "Search String too short - min length is 3", (object) ["showrooms" =>  []]);
        }
    }

    function getJoinRequestsAndInvitations(Request $request)
    {
        $seller = $request->user();
        $seller->load("joinRequests");
        parent::sendResponse(true, "Requests Retrieved Successfully", (object)["requests" => $seller->joinRequests]);
    }

    function submitShowroomJoinRequest(Request $request)
    {
        parent::validateRequest($request, [
            "showroomID" => "required|exists:showrooms,id"
        ]);
        $seller = $request->user();
        $showroom = Showroom::findOrFail($request->showroomID);
        $seller->load('showroom');
        if (!isset($seller->showroom) && !$showroom->hasSeller($seller->id)) {
            $ret = $seller->submitJoinShowroomRequest($request->showroomID);
            if ($ret) {
                parent::sendResponse(true, "Request Submitted", (object)["request" => $ret->fresh()]);
                $showroom =  Showroom::findOrFail($request->showroomID);
                $managersIDs = $showroom->getManagers();
                foreach ($managersIDs as $manId) {
                    $tmpSeller = Seller::find($manId);
                    if ($tmpSeller) {
                        $tmpNotf = Notification::newNotification(
                            Notification::TYPE_SALES_JOIN_REQUEST,
                            "New Team Join Request",
                            $seller->SLLR_NAME . " want to join your showroom!",
                            $tmpSeller,
                            []
                        );
                        $tmpNotf->send();
                    }
                }
            } else {
                parent::sendResponse(false, "Request Failed");
            }
        } else {
            parent::sendResponse(false, "Request Inapplicable");
        }
    }

    function leaveShowroom(Request $request)
    {
        $seller = $request->user();
        $seller->load("showroom");
        if (isset($seller->showroom) && !$seller->showroom->isOwner()) {
            $ret = $seller->unsetShowroom();
            if ($ret) {
                parent::sendResponse(true, "Left Showroom");
            } else {
                parent::sendResponse(false, "Request Failed");
            }
        } else {
            parent::sendResponse(false, "Request Invalid");
        }
    }

    function deleteShowroom(Request $request)
    {
        $seller = $request->user();
        $seller->load("showroom");
        if (isset($seller->showroom) && $seller->showroom->isOwner()) {
            $ret = $seller->showroom->deleteShowroom();
            if ($ret) {
                parent::sendResponse(true, "Showroom Deleted");
            } else {
                parent::sendResponse(false, "Request Failed");
            }
        } else {
            parent::sendResponse(false, "Request Invalid");
        }
    }

    function isEmailTaken(Request $request)
    {
        parent::validateRequest($request, [
            "email" => "required"
        ]);
        $taken = Seller::isEmailTaken($request->email);
        parent::sendResponse(true, "Request passed", $taken);
    }

    function isPhoneTaken(Request $request)
    {
        parent::validateRequest($request, [
            "phone" => "required"
        ]);
        $taken = Seller::isPhoneTaken($request->phone);
        parent::sendResponse(true, "Request passed", $taken);
    }
}
