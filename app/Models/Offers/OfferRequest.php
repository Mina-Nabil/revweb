<?php

namespace App\Models\Offers;

use App\Models\Cars\AdjustmentOption;
use App\Models\Cars\Car;
use App\Models\Cars\ModelAdjustment;
use App\Models\Users\Buyer;
use App\Models\Users\Seller;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfferRequest extends Model
{
    use SoftDeletes;

    public const NEW_KEY = "New";
    public const REPLIED_KEY = "RepliedToBuyer";
    public const CANCELLED_KEY = "Cancelled";
    public const SETTLED_KEY = "Settled";

    public const STATES = [
        0 => self::NEW_KEY,
        1 => self::REPLIED_KEY,
        2 => self::CANCELLED_KEY,
        3 => self::SETTLED_KEY,
    ];

    const LOAN_KEY = "Loan";
    const CASH_KEY = "Cash";

    public const PYMT_STATES = [
        0 => self::CASH_KEY,
        1 => self::LOAN_KEY,
    ];

    protected $table = "offers_requests";
    protected $with = ["colors", "buyer", "car", "car.model", "car.colors", "offers"];
    protected $appends = ['available_options'];
    public $timestamps = true;

    public static function createRequest(int $buyerID, int $carID, string $paymentMethod = self::CASH_KEY, string $comment = null, array $colors = [], array $options = [])
    {

        $newOffer = new self();
        $newOffer->OFRQ_BUYR_ID = $buyerID;
        $newOffer->OFRQ_CAR_ID = $carID;
        $newOffer->OFRQ_DATE = date("Y-m-d");
        $newOffer->OFRQ_STTS = self::NEW_KEY;
        $newOffer->OFRQ_PRFD_PYMT = $paymentMethod;
        $newOffer->OFRQ_CMNT = $comment;

        $car = Car::with("colors")->findOrFail($carID);
        try {
            DB::transaction(function () use ($newOffer, $car, $colors, $options) {
                $newOffer->save();
                $i = 0;
                $colorsIDs = $car->colors->pluck('id')->toArray();
                foreach ($colors as $color) {
                    if (in_array($color, $colorsIDs)) {
                        $newOffer->colors()->create([
                            "OFRC_COLR_ID" => $color,
                            "OFRC_PRTY" => $i++ * 100
                        ]);
                    }
                }
                $newOffer->options()->sync($options);
            });
            return $newOffer;
        } catch (Exception $e) {
            report($e);
        }
    }

    public function updateRequest(string $paymentMethod = self::CASH_KEY, string $comment = null, array $colors = []): bool
    {

        $this->OFRQ_PRFD_PYMT = $paymentMethod;
        $this->OFRQ_CMNT = $comment;

        $car = Car::with("colors")->findOrFail($this->OFRQ_CAR_ID);
        try {
            $that = $this;
            DB::transaction(function () use ($that, $car, $colors) {
                $that->save();
                $that->colors()->delete();
                $i = 0;
                $colorsIDs = $car->colors->pluck('id')->toArray();
                foreach ($colors as $color) {
                    if (in_array($color, $colorsIDs)) {
                        $that->colors()->create([
                            "OFRC_COLR_ID" => $color,
                            "OFRC_PRTY" => $i++ * 100
                        ]);
                    }
                }
            });
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Sets request as replied to if it is not
     */
    public function setAsRepliedTo(): bool
    {
        $this->OFRQ_STTS = self::REPLIED_KEY;
        return $this->save();
    }

    /**
     * Sets request as cancelled
     */
    public function setAsCancelled(): bool
    {
        $this->OFRQ_STTS = self::CANCELLED_KEY;
        return $this->save();
    }

    public function owned_by(Buyer $buyer): bool
    {
        return $this->OFRQ_BUYR_ID == $buyer->id;
    }

    public function getAvailableOptionsAttribute()
    {
        return ModelAdjustment::join('adjustments_options', 'ADOP_ADJT_ID', '=', 'model_adjustments.id')
            ->with(['options' => function ($query) {
                $query->whereIn('model_adjustments.id', $this->options()->get()->pluck('id')->toArray());
            }])->get();
    }


    /**
     * @param $showroomID offer requests available for the mentioned showroom
     * @return Collection of Offer requests
     */
    public static function getAvailableOffers($showroomID)
    {

        $query = self::selectRaw("DISTINCT offers_requests.id, offers_requests.*")->join("offers_requests_colors as offerDetails", "offers_requests.id", "=", "OFRC_OFRQ_ID")
            ->join("showroom_catalog as catalog1",  function ($join) use ($showroomID) {
                $join->on("OFRQ_CAR_ID", '=', 'SRCG_CAR_ID');
                $join->where("SRCG_SHRM_ID", "=", $showroomID);
            })->join("showroom_catalog_details", function ($join) use ($showroomID) {
                $join->on("SRCD_SRCG_ID", "=", "catalog1.id");
                $join->whereRaw("offerDetails.OFRC_COLR_ID IN (SELECT SRCD_COLR_ID from showroom_catalog_details where SRCD_SRCG_ID = catalog1.id and catalog1.SRCG_SHRM_ID = {$showroomID} )");
            })->whereIn("OFRQ_STTS", [OfferRequest::NEW_KEY, OfferRequest::REPLIED_KEY])->whereDate("created_at", ">", (new Carbon("now"))->subWeekdays(14));

        return $query->get();
    }

    public function offers()
    {
        /** @var Seller */
        $user = Auth::user();
        $rel = $this->hasMany(Offer::class, "OFFR_OFRQ_ID");
        if (is_a($user, Seller::class)) {
            $user->loadMissing("showroom");
            if ($user->showroom != null)
                $rel = $rel->where("offers.OFFR_SHRM_ID", $user->showroom->id);
        }
        return $rel;
    }

    public function colors()
    {
        return $this->hasMany(OfferRequestColors::class, "OFRC_OFRQ_ID");
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(AdjustmentOption::class, "offer_request_adjustment_options", "CRAD_OFRQ_ID", "CRAD_ADOP_ID");
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, "OFRQ_BUYR_ID");
    }

    public function car()
    {
        return $this->belongsTo(Car::class, "OFRQ_CAR_ID");
    }
}
