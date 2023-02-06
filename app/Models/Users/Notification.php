<?php

namespace App\Models\Users;

use App\Notifications\RequestOfferCreated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{

    CONST TYPE_REQUEST_OFFER_CREATED = 'request_offer_created';
    CONST TYPE_OFFER_CREATED = 'offer_created';
    CONST TYPE_OFFER_CANCELLED = 'offer_cancelled';
    CONST TYPE_OFFER_DECLINED = 'offer_declined';
    CONST TYPE_OFFER_ACCEPTED = 'offer_accepted';

    protected $hidden = ['type'];

    ///static functions
    public static function newNotification($type, $title, $body, $user, array $data, $route = null) : self
    {
        $newNotf = new self;
        $newNotf->type = $type;
        $newNotf->title = $title;
        $newNotf->body = $body;
        $newNotf->data = json_encode($data);
        $newNotf->route = $route;
        $newNotf->notifiable()->associate($user);
        $newNotf->save();
        return $newNotf();
    }


    //////functions
    public function send()
    { //""
        $this->loadMissing('user');
        switch ($this->type) {
            case self::TYPE_REQUEST_OFFER_CREATED:
                $this->user->notify(new RequestOfferCreated($this));
                break;
            
            default:
                # code...
                break;
        }
    }


    public function read()
    {
        $this->read_at = (Carbon::now())->format('Y-m-d H:i:');
        $this->save();
    }


    //////relations
    public function notifiable() : MorphTo
    {
        return $this->morphTo();
    }
}
