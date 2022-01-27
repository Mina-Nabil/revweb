<?php

namespace App\Models\Offers;

use App\Models\Cars\Car;
use App\Models\Users\Buyer;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    protected $with = ["colors", "buyer", "car"];
    public $timestamps = true;

    public static function createRequest(int $buyerID, int $carID, string $paymentMethod = self::CASH_KEY, string $comment = null, array $colors = [])
    {

        $newOffer = new self();
        $newOffer->OFRQ_BUYR_ID = $buyerID;
        $newOffer->OFRQ_CAR_ID = $carID;
        $newOffer->OFRQ_DATE = date("Y-m-d");
        $newOffer->OFRQ_STTS = self::NEW_KEY;
        $newOffer->OFRQ_PRFD_PYMT = $paymentMethod;
        $newOffer->OFRQ_CMNT = $comment;

        $car = Car::with("colors", "model", "model.colors")->findOrFail($carID);

        dd( $car->model, $car->colors, "\n\nFrom Models: \n\n", $car->model->colors);
        try {
            DB::transaction(function () use ($newOffer, $car, $colors) {
                $newOffer->save();
                $i = 0;
                $colorsIDs = $car->colors->pluck('id');
                foreach ($colors as $color) {
                    if (in_array($color, $colorsIDs)) {
                        $newOffer->colors()->create([
                            "OFRC_COLR_ID" => $color,
                            "OFRC_PRTY" => $i++ * 100
                        ]);
                    }
                }
            });
            return $newOffer;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
    /**
     * @param $showroomID offer requests available for the mentioned showroom
     * @return Collection of Offer requests
     */
    public static function getAvailableOffers($showroomID)
    {
        /*
        showroom - catalog_item - car - offer requests

        */
        $query = self::join("showroom_catalog", "SRCG_SHRM_ID", "=", $showroomID)
            ->whereRaw("  ")->get();
    }

    public function colors()
    {
        return $this->hasMany(OfferRequestColors::class, "OFRC_OFRQ_ID");
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
