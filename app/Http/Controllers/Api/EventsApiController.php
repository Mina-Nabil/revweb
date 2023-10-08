<?php

namespace App\Http\Controllers\Api;

use App\Models\Users\Buyer;
use App\Models\Users\Event;
use App\Models\Users\Seller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsApiController extends BaseApiController
{
    public function userEvents(Request $request)
    {
        parent::validateRequest($request, [
            "from"  =>  "required|date",
            "to"    =>  "required|date"
        ]);
        $user = Auth::user();
        $type = is_a($user, Seller::class) ? 'seller' : 'buyer';
        $from = new Carbon($request->from);
        $to = new Carbon($request->to);
        $events = Event::byUser($type, $user->id)->fromTo($from, $to)->get();
        parent::sendResponse(true, "Events returned successfully", $events);
    }

    public function event($id)
    {
        $event = Event::with('seller', 'buyer', 'offer', 'showroom')->findOrFail($id);
        return parent::sendResponse(true, "Event found", $event);
    }

    public function createEvent(Request $request)
    {
        parent::validateRequest($request, [
            "title"         =>  "required",
            "buyer_id"      =>  "nullable|exists:buyers,id",
            "showroom_id"   =>  "nullable|exists:showrooms,id",
            "offer_id"      =>  "nullable|exists:offers,id",
            "start"         =>  "required|date",
            "end"           =>  "required|date",
            "note"          =>  "nullable",
            "location"      =>  "nullable",
            "notification_time" =>  "nullable|date",
        ]);

        $event = Event::newEvent(Auth::id(), $request->buyer_id, $request->showroom_id, $request->offer_id, $request->title, $request->note, $request->start, $request->end, $request->location, $request->notification_time);

        if (is_a($event, Event::class)) {
            return parent::sendResponse(true, "Event created successfully", $event);
        } else {
            return parent::sendResponse(false, "Event creation failed");
        }
    }

    public function editEvent($id, Request $request)
    {
        /** @var Event */
        $event = Event::findOrFail($id);
        parent::validateRequest($request, [
            "title"         =>  "required",
            "start"         =>  "required|date",
            "end"           =>  "required|date",
            "note"          =>  "nullable",
            "location"      =>  "nullable",
            "notification_time" =>  "nullable|date",
        ]);

        $event->editInfo($request->title, $request->note, $request->start, $request->end, $request->location, $request->notification_time);

        if (is_a($event, Event::class)) {
            return parent::sendResponse(true, "Event updated successfully", $event);
        } else {
            return parent::sendResponse(false, "Event changes failed");
        }
    }

    public function deleteEvent($id)
    {
        $event = Event::findOrFail($id);
        $loggedInUser = Auth::user();
        if (is_a($loggedInUser, Buyer::class)) abort(403, 'Unauthorized action');
        if ($loggedInUser->id != $event->seller_id) abort(403, 'Unauthorized action');
        $res = $event->delete();
        if ($res) {
            return parent::sendResponse(true, "Event deleted successfully");
        } else {
            return parent::sendResponse(false, "Event deletion failed");
        }
    }
}
