<?php

namespace App\Models\Users;

use App\Notifications\FcmNotificationSender;
use App\Notifications\RequestOfferCreated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Notification extends Model
{

    const TYPE_REQUEST_OFFER_CREATED = 'request_offer_created';
    const TYPE_OFFER_CREATED = 'offer_created';
    const TYPE_OFFER_CANCELLED = 'offer_cancelled';
    const TYPE_OFFER_DECLINED = 'offer_declined';
    const TYPE_OFFER_ACCEPTED = 'offer_accepted';

    protected $hidden = ['type'];

    ///static functions
    public static function newNotification($type, $title, $body, $user, array $data, $route = null): self
    {
        $newNotf = new self;
        $newNotf->id = Str::uuid()->toString();
        $newNotf->type = $type;
        $newNotf->title = $title;
        $newNotf->body = $body;
        $newNotf->data = json_encode($data);
        $newNotf->route = $route;
        $newNotf->notifiable()->associate($user);
        $newNotf->save();
        return $newNotf;
    }


    //////functions
    public function send()
    { //""
        $this->loadMissing('user');
        $this->user->notify(new FcmNotificationSender($this));
    }


    public function read()
    {
        $this->read_at = (Carbon::now())->format('Y-m-d H:i:');
        $this->save();
    }


    //////relations
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
