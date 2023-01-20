<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'seller_id', 'transaction_id', 'amount', 'title', 'desc'
    ];




    /////relations
    public function payable() : MorphTo
    {
        return $this->morphTo();
    }
}
