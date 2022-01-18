<?php

namespace App\Models\Offers;

use App\Models\Cars\Car;
use App\Models\Users\Buyer;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    public const ACCEPTED_KEY = "Accepted";
    public const EXPIRED_KEY = "Expired";
    public const NEW_KEY = "New";
    public const DECLINED_KEY = "Declined";

    public const STATES = [
        0 => self::NEW_KEY,
        1 => self::ACCEPTED_KEY,
        2 => self::EXPIRED_KEY,
        3 => self::DECLINED_KEY,
    ];

    protected $table = "offers";

    public $timestamps = true;


    //relations
    public function colors()
    {
        return $this->hasMany(OfferColors::class, "OFFR_OFRQ_ID");
    }
    public function request()
    {
        return $this->belongsTo(OfferRequest::class, "OFFR_OFRQ_ID");
    }
    public function seller()
    {
        return $this->belongsTo(Seller::class, "OFFR_SLLR_ID");
    }
    public function buyer()
    {
        return $this->belongsTo(Buyer::class, "OFFR_BUYR_ID");
    }
    public function car()
    {
        return $this->belongsTo(Car::class, "OFFR_CAR_ID");
    }
    public function showroom()
    {
        return $this->belongsTo(Showroom::class, "OFFR_SHRM_ID");
    }
}
