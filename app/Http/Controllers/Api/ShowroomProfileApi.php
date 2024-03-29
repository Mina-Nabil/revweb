<?php

namespace App\Http\Controllers\Api;

use App\Models\Cars\Country;
use App\Models\Subscriptions\Plan;
use App\Models\Users\JoinRequest;
use App\Models\Users\Notification;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use App\Rules\Iban;
use App\Services\FilesHandler;
use App\Services\PushNotificationsHandler;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowroomProfileApi extends BaseApiController
{
    function createShowroom(Request $request)
    {
        parent::validateRequest($request, [
            "name"          => "required|unique:showrooms,SHRM_NAME",
            "email"         => "required|email",
            "mobNumber1"    => "required",
            "address"    => "required",
            "cityID"    => "required|exists:cities,id",
            "displayImage"  =>  "nullable|image|between:0,10000", //10 MB max
        ], "Showroom Creation Failed");

        $filesHandler = new FilesHandler();
        $displayImageFilePath = null;
        /** @var Seller */
        $seller = $request->user();
        if ($request->hasFile("displayImage")) {
            $displayImageFilePath = $filesHandler->uploadFile($request->displayImage, "showrooms/" . $request->name . '/photos');
        }

        $newShowroom = null;
        $error = null;
        $failed = true;
        try {
            /** @var Showroom */
            $newShowroom = Showroom::create($request->name, $request->email, $request->mobNumber1, $request->cityID, $request->address, $seller->id, $request->mobNumber2, $displayImageFilePath);
            $seller->setShowroom($newShowroom->id);
            $newShowroom->initiateEmailVerfication();
            $failed = false;
        } catch (Exception $e) {
            $error = $e;
        }

        if ($failed || $newShowroom == null) {
            parent::sendResponse(false, "Registration Failed", ["Message" => $error->getMessage()], false);
            $filesHandler->deleteFile($displayImageFilePath);
            die;
        }
        parent::sendResponse(true, "Registration Succeeded!", (object)["showroom" => $newShowroom->fresh()]);
    }

    function getShowroom(Request $request)
    {
        $seller = $request->user();
        $seller->load("showroom");
        if ($seller->showroom == NULL)  parent::sendResponse(false, "Unable to load Showroom");
        $seller->showroom->append('active_plan');
        parent::sendResponse(true, "Showroom Successfully Retrieved", $seller->showroom);
    }

    function getTeam(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        if (is_null($seller->showroom)) {
            if ($seller->showroom == NULL)  parent::sendResponse(false, "No Showroom Found");
        } else {
            $seller->showroom->load('sellers');
            parent::sendResponse(true, "Team Loaded Successfully", (object)[
                "team" => $seller->showroom->sellers
            ]);
        }
    }

    function searchSellers(Request $request)
    {
        parent::validateRequest($request, [
            "searchText" => "required|string"
        ]);
        if (is_string($request->searchText) && strlen($request->searchText) > 2) {
            $seller = $request->user();
            $seller->load('showroom');
            $res = Seller::searchText($request->searchText);
            parent::sendResponse(true, "Sellers Retrieved", (object) ["sellers" =>  $res]);
        } else {
            parent::sendResponse(true, "Search String too short - min length is 3", (object) ["sellers" =>  []]);
        }
    }

    function getJoinRequestsAndInvitations(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        if (isset($seller->showroom) && $seller->showroom->isManager()) {
            $seller->showroom->load('joinRequests');
            parent::sendResponse(true, "Requests Retrieved", (object) ["requests" =>  $seller->showroom->getAvailableJoinRequests()]);
        } else {
            parent::sendResponse(false, "Unauthorized");
        }
    }

    function acceptJoinRequest(Request $request)
    {
        parent::validateRequest($request, [
            "joinRequestID" => "required|exists:join_requests,id"
        ]);
        $seller = $request->user();
        $seller->load('showroom');
        if ($seller->showroom->isManager()) {
            $seller->showroom->checkLimit(Plan::USERS_LIMIT, true);
            $ret = $seller->showroom->acceptJoinRequest($request->joinRequestID);
            if ($ret) {
                parent::sendResponse(true, "Request Accepted", null, false);
                $joinRequest = JoinRequest::with('seller')->findOrFail($request->joinRequestID);

                $tmpNotf = Notification::newNotification(
                    Notification::TYPE_TEAM_JOIN_ACCEPT,
                    "Team Join Request Accepted",
                    "Welcome to your new team!",
                    $joinRequest->seller,
                    []
                );
                $tmpNotf->send();
            } else
                parent::sendResponse(false, "Operation Failed");
        } else {
            parent::sendResponse(false, "Unauthorized");
        }
    }

    function inviteSellerToShowroom(Request $request)
    {
        parent::validateRequest($request, [
            "sellerID" => "required|exists:sellers,id"
        ]);
        $seller = $request->user();
        $seller->load('showroom');
        if (!isset($seller->showroom)) {
            parent::sendResponse(false, "Unauthorized");
        }
        if ($seller->showroom->hasSeller($request->sellerID)) {
            parent::sendResponse(false, "Inapplicable");
        }
        /** @var Showroom */
        $showroom = $seller->showroom;
        $showroom->checkLimit(Plan::USERS_LIMIT, true);

        $ret = $seller->showroom->inviteSellerToShowroom($request->sellerID);
        if ($ret) {
            parent::sendResponse(true, "Request Submitted", (object)["request" => $ret->fresh()], false);
            $invitedSeller = Seller::findOrFail($request->sellerID);

            $tmpNotf = Notification::newNotification(
                Notification::TYPE_TEAM_JOIN_ACCEPT,
                "New Join team request",
                $seller->showroom->SHRM_NAME . " invites you to join the showroom Sales Team!",
                $invitedSeller,
                []
            );
            $tmpNotf->send();
        } else {
            parent::sendResponse(false, "Unauthorized");
        }
    }

    function deleteSellerInvitation(Request $request)
    {
        parent::validate($request, [
            "joinRequestID" => "required|exists:join_requests,id",
        ]);
        $joinRequest = JoinRequest::findOrFail($request->joinRequestID);
        $sessionSeller = Auth::user();
        $joinRequestSeller = $joinRequest->seller;
        $joinRequestShowroom = $joinRequest->showroom;
        if ($sessionSeller->id == $joinRequestSeller->id || $joinRequestShowroom->isManager()) {
            $ret = $joinRequestShowroom->deleteJoinShowroomRequest($joinRequest->id);
            if ($ret) {
                parent::sendResponse(true, "deleted");
            } else {
                parent::sendResponse(false, "Failed");
            }
        } else {
            parent::sendResponse(false, "Unauthorized");
        }
    }

    //showroom info
    function getBankInfo(Request $request)
    {
        $seller = $request->user();
        $seller->load("showroom");
        if (!$seller->showroom->isOwner()) {
            parent::sendResponse(false, "Unauthorized");
        }
        $showroom = $seller->showroom;
        $showroom->load("bankInfo");
        parent::sendResponse($showroom->bankInfo != null, "Banking info retrieved successfully", $showroom->bankInfo);
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

    function deleteBankInfo(Request $request)
    {
        $seller = $request->user();
        $seller->load('showroom');
        $showroom = $seller->showroom;
        if ($showroom == NULL)  parent::sendResponse(false, "Unable to load Showroom");
        if (!$showroom->isOwner()) {
            parent::sendResponse(false, "Unauthorized");
        }
        if ($showroom->deleteBankInfo()); {
            parent::sendResponse(true, "Banking Info Deleted");
        }
        parent::sendResponse(false, "Bank Info deletion failed");
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
            $showroomRecordImgFront = $filesHandler->uploadFile($request->showroomIDFront, "showrooms/" . $showroom->SHRM_MAIL . '/ids');
        }
        if ($request->hasFile("showroomIDBack")) {
            $showroomRecordImgBack = $filesHandler->uploadFile($request->showroomIDBack, "showrooms/" . $showroom->SHRM_MAIL . '/ids');
        }
        if ($showroomRecordImgFront != null && $showroomRecordImgBack != null && $showroom->addShowroomRecord($request->record, $showroomRecordImgFront, $showroomRecordImgBack)) {
            parent::sendResponse(true, "Adding Commercial Record Succeeded, Pending Confirmation");
        } else {
            parent::sendResponse(false, "Adding Commercial Record Failed");
            $filesHandler->deleteFile($showroomRecordImgFront);
            $filesHandler->deleteFile($showroomRecordImgBack);
        }
    }

    public function getCities()
    {
        parent::sendResponse(true, "Cities loaded successfully", Country::with("cities")->get());
    }
}
