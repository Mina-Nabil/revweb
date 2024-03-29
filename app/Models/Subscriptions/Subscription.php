<?php

namespace App\Models\Subscriptions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    const MORPH_TYPE = 'subscription';

    ////////subscription types
    const TRIAL_TYPE = 'trial';
    const PAID_TYPE = 'paid';
    
    const SUBSCRIPTION_TYPES = [
        self::TRIAL_TYPE, self::PAID_TYPE
    ];
    
    const MONTHLY_RANGE = 'monthly';
    const YEARLY_RANGE = 'yearly';

    const SUBSCRIPTION_RANGES = [
        self::MONTHLY_RANGE, self::YEARLY_RANGE
    ];

    ////////states
    const CANCEL_STATE = 'cancel';
    const ACTIVE_STATE = 'active';

    const STATES = [
        self::CANCEL_STATE, self::ACTIVE_STATE
    ];

    protected $fillable = [
        'seller_id', 'plan_id', 'type', 'expiry_date', 'showroom_id', 'state', 'range'
    ];
    protected $with = ['plan'];
    public $timestamps = true;


    ///////subscription functions
    public function cancel()
    {
        $this->state = self::CANCEL_STATE;
        $this->cancellation_date = date('Y-m-d H:i:s');
        $this->save();
    }


    //////relations
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
