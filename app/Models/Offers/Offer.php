<?php

namespace App\Models\Offers;

use App\Models\Cars\AdjustmentOption;
use App\Models\Cars\Car;
use App\Models\Cars\ModelAdjustment;
use App\Models\Offers\OfferDoc;
use App\Models\Users\Buyer;
use App\Models\Users\Event;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Offer extends Model
{
    const PROFILE_RELS = [
        'colors', 'showroom', 'car', 'buyer', 'seller', 'options', 'adjustments', 'extras', 'documents', 'colors'
    ];

    public const ACCEPTED_KEY = "Accepted";
    public const EXPIRED_KEY = "Expired";
    public const CANCELLED_KEY = "Cancelled";
    public const NEW_KEY = "New";
    public const DECLINED_KEY = "Declined";
    public const SOLD_KEY = "SOLD";         //Sold to customer
    public const ABORTED_KEY = "ABORTED";   //aborted after accepted

    public const STATES = [
        0 => self::NEW_KEY,
        1 => self::ACCEPTED_KEY,
        2 => self::EXPIRED_KEY,
        3 => self::DECLINED_KEY,
        4 => self::CANCELLED_KEY,
        5 => self::SOLD_KEY,
        6 => self::ABORTED_KEY,
    ];

    protected $table = "offers";
    protected $with = ["showroom", "seller", "buyer", "car", "colors", "car.model", "car.colors"];
    protected $appends = ['available_options'];
    public $timestamps = true;

    static function createOffer(OfferRequest $request, Seller $seller, $isLoan, $price, $downpayment, DateTime $startDate, DateTime $endDate, array $colors, array $options, $comment = null)
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
            DB::transaction(function () use ($newOffer, $colors, $request, $options) {
                $newOffer->save();
                foreach ($colors as $color) {
                    $newOffer->colors()->create([
                        "OFCL_COLR_ID" => $color
                    ]);
                }
                $optionsArr = array();
                foreach ($options as $key => $opt) {
                    $optionsArr[$key] = ["OADO_ADJT_ID" => $opt];
                }
                $newOffer->options()->sync($optionsArr);
                $request->setAsRepliedTo();
            });
        } catch (Exception $e) {
            report($e);
            return false;
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

    public function cancelOffer($comment = null): bool
    {
        if (is_a(Auth::user(), Seller::class)) {
            $this->OFFR_STTS = self::CANCELLED_KEY;
            $this->OFFR_RSPN_DATE = date("Y-m-d H:i:s");
            if ($comment != null) {
                $this->OFFR_SLLR_CMNT .= "\nCancellation Comment: \n" . $comment;
            }
            return $this->save();
        } else return false;
    }

    public function extendOffer(DateInterval $time_range): bool
    {
        $currentExpiry = new DateTime($this->OFFR_EXPR_DATE);
        $currentExpiry->add($time_range);
        $this->OFFR_EXPR_DATE = $currentExpiry->format('Y-m-d H:i:s');
        return $this->save();
    }

    public function getAvailableOptionsAttribute()
    {
        $availableOptionIDs = $this->options()->get()->pluck('id')->toArray();
        $availableAdjustmentIDs = $this->adjustments()->get()->pluck('id')->toArray();

        return ModelAdjustment::whereIn('model_adjustments.id', $availableAdjustmentIDs)
            ->with(['options' => function ($query) use ($availableOptionIDs) {
                $query->whereIn('adjustments_options.id', $availableOptionIDs);
            }])->get();
    }

    public function addDocument($title, $document_url = null, $note = null): OfferDoc|false
    {
        if (!$this->is_accepted) abort(403, 'Offer is not accepted');
        try {
            $vals = array();
            $vals["doc_url"] = $document_url;
            $vals["is_seller"] = ($document_url != null);
            if ($note) {
                $vals["note"]  = $note;
            }
            return $this->documents()->firstOrCreate([
                "title" =>  $title
            ], $vals);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function deleteDocument($id): bool
    {
        try {
            return $this->documents()->where('id', $id)->delete();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function addExtra($title, $price = null, $note = null, $image_url = null): OfferExtra|false
    {
        if (!$this->is_accepted) abort(403, 'Offer is not accepted');
        try {
            $vals = array();
            $vals["price"] = $price;
            if ($note) {
                $vals["note"]  = $note;
            }
            if ($image_url) {
                $vals["image_url"]  = $image_url;
            }
            return $this->extras()->firstOrCreate([
                "title" =>  $title
            ], $vals);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function deleteExtra($id): bool
    {
        try {
            return $this->extras()->where('id', $id)->delete();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    //attributes
    public function getIsAcceptedAttribute()
    {
        return $this->OFFR_STTS == self::ACCEPTED_KEY;
    }

    //relations
    public function colors()
    {
        return $this->hasMany(OfferColor::class, "OFCL_OFFR_ID");
    }
    public function documents()
    {
        return $this->hasMany(OfferDoc::class, "OFDC_OFFR_ID");
    }
    public function extras()
    {
        return $this->hasMany(OfferExtra::class, "OFXT_OFFR_ID");
    }
    public function adjustments(): BelongsToMany
    {
        return $this->belongsToMany(AdjustmentOption::class, "offer_adjustment_options", "OADO_OFFR_ID", "OADO_ADJT_ID");
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(AdjustmentOption::class, "offer_adjustment_options", "OADO_OFFR_ID", "OADO_ADOP_ID");
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
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
