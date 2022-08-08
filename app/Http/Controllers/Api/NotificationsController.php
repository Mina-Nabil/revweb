<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users\Seller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{

    function getNotifications()
    {
        /** @var Seller|Buyer */
        $user = Auth::user();
        return response()->json($user->notifications()->unread()->simplePaginate(15));
    }

    function readNotification($id)
    {
        /** @var Seller|Buyer */
        $user = Auth::user();
        try {
            $user->notifications()->find($id)->markAsRead();
            return response()->json(["status" => true]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["status" => false]);
        }
    }

    function deleteNotification($id)
    {
        /** @var Seller|Buyer */
        $user = Auth::user();
        try {
            $user->notifications()->find($id)->delete();
            return response()->json(["status" => true]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["status" => false]);
        }
    }

    function setToken(Request $request)
    {
        $request->validate([
            "token" =>  "required"
        ]);
        /** @var Seller|Buyer */
        $user = Auth::user();
        $status = $user->setToken($request->token);
        $code = $status ? 200 : 500;
        return response()->json([
            "status"    =>  $status
        ], $code);
    }
}
