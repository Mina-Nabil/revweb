<?php

namespace App\Models\Offers;

use App\Models\Cars\Car;
use App\Models\Users\Buyer;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    protected $with = ["showroom", "seller", "buyer", "car", "colors", "car.model"];
    public $timestamps = true;

    static function createOffer(OfferRequest $request, Seller $seller, $isLoan, $price, $downpayment, DateTime $startDate, DateTime $endDate, array $colors, $comment = null)
    {
        $request->loadMissing(["buyer", "car"]);
        $seller->loadMissing("showroom");
        $newOffer = new self();
        $newOffer->OFFR_OFRQ_ID = $request->id;
        $newOffer->OFFR_SHRM_ID = $seller->showroom->id;
        $newOffer->OFFR_SLLR_ID = $seller->id;
        $newOffer->OFFR_BUYR_ID = $request->buyer->id;
        $newOffer->OFFR_CAR_ID = $request->car->id;
        $newOffer->OFFR_CAN_LOAN = $isLoan;
        $newOffer->OFFR_PRCE = $price;
        $newOffer->OFFR_MIN_PYMT = $downpayment;
        $newOffer->OFFR_STRT_DATE = $startDate;
        $newOffer->OFFR_EXPR_DATE = $endDate;
        $newOffer->OFFR_SLLR_CMNT = $comment;
        $newOffer->OFFR_STTS = self::NEW_KEY;
        try {
            DB::transaction(function () use ($newOffer, $colors, $request) {
                $newOffer->save();
                foreach ($colors as $color) {
                    $newOffer->colors()->create([
                        "OFCL_OFFR_ID" => $color
                    ]);
                }
                $request->setAsRepliedTo();
            });
        } catch (Exception $e) {
            Log::error("Offer creation failed");
        }
        return $newOffer->fresh();
    }

    //actions
    public function acceptOffer($comment = null)
    {
        $this->OFFR_STTS = self::ACCEPTED_KEY;
        $this->OFFR_RSPN_DATE = date("Y-m-d H:i:s");
        $this->OFFR_BUYR_CMNT = $comment;
        return $this->save();
    }

    public function declineOffer($comment = null)
    {
        $this->OFFR_STTS = self::DECLINED_KEY;
        $this->OFFR_RSPN_DATE = date("Y-m-d H:i:s");
        $this->OFFR_BUYR_CMNT = $comment;
        return $this->save();
    }

    //relations
    public function colors()
    {
        return $this->hasMany(OfferColor::class, "OFFR_OFRQ_ID");
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
