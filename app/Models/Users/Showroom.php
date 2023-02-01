<?php

namespace App\Models\Users;

use App\Models\Cars\Brand;
use App\Models\Cars\Car;
use App\Models\Cars\CatalogItem;
use App\Models\Cars\CatalogItemDetails;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferRequest;
use App\Models\Subscriptions\Payment;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\Subscription;

use App\Services\EmailsHandler;
use App\Services\SmsHandler;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Showroom extends Model
{

    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $appends = array('image_url');
    protected $with = ["owner"];
    protected $dateFormat = 'Y-m-d H:i:s';

    use SoftDeletes;

    /***
     * Retrieves all the cars & colors the showroom is selling
     * 
     */
    function getCatalogCars()
    {
        $this->load("catalogItems");
        return $this->catalogItems;
    }

    /***
     * Add Car to Showrooms Catalog
     * @param carID car ID to add to catalog
     * @param array|null $colors .. null adds all colors .. array of color IDs to add to catalog
     */
    function addCarToCatalog($carID, $colors = null)
    {
        if (!$this->isManager()) return false;
        try {
            DB::transaction(function () use ($carID, $colors) {
                $catalogItem = $this->catalogItems()->updateOrCreate([
                    "SRCG_CAR_ID"       =>  $carID,
                    "SRCG_CAR_ACTV"       =>  1,
                ]);
                $catalogItem->details()->delete();
                if ($catalogItem) {
                    if ($colors == null) {
                        //if colors is null add all .. comment for now
                        // $car = Car::with("model.colors")->findOrFail($carID);
                        // $colors = $car->model->colors->pluck('id');
                    } else
                        foreach ($colors as $color) {
                            $catalogItem->details()->create([
                                "SRCD_COLR_ID" => $color
                            ]);
                        }
                }
            });
            return true;
        } catch (Exception $e) {
            throw $e;
            return false;
        }
    }

    /***
     * Deletes car from catalog
     * @param carID car ID to delete from catalog 
     */
    function deleteCarFromCatalog($carID)
    {
        if (!$this->isManager()) return false;
        $catalogEntry = $this->catalogItems()->where("SRCG_CAR_ID", $carID)->first();
        try {
            DB::transaction(function () use ($catalogEntry) {
                CatalogItemDetails::where("SRCD_SRCG_ID", $catalogEntry->id)->delete();
                $catalogEntry->delete();
            });
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /***
     * Deletes car from catalog
     * @param carID car ID to delete from catalog 
     */
    function deactivateCarFromCatalog($carID)
    {
        if (!$this->isManager()) return false;
        $catalogEntry = $this->catalogItems()->where("SRCG_CAR_ID", $carID)->find();
        $catalogEntry->SRCG_CAR_ACTV = 0;
        return $catalogEntry->save();
    }

    /**
     * Set the Showroom's brands -- these brands are used to load the catalog car pool 
     * @param $brandIDs array of brand IDs to associate with the showroom
     * 
     */
    function setBrands($brandIDs)
    {
        if (!$this->isManager()) return false;
        //removing incorrect brand IDs if any
        foreach ($brandIDs as $key => $brand) {
            $brandaya = Brand::select("id")->where("id", $brand)->first();
            if (!isset($brandaya->id)) {
                unset($brandIDs[$key]);
            }
        }

        try {
            return $this->brands()->sync($brandIDs);
        } catch (Exception $e) {
            throw $e;
            return false;
        }
    }

    ///profile functions
    static function create($name, $email, $mobileNumber1, $cityID, $address, $ownerID = null, $mobileNumber2 = null, $accountImagePath = null)
    {
        $newShowroom = new self();
        $newShowroom->SHRM_NAME = $name;
        $newShowroom->SHRM_MAIL = $email;
        $newShowroom->SHRM_MOB1 = $mobileNumber1;
        $newShowroom->SHRM_CITY_ID = $cityID;
        $newShowroom->SHRM_ADRS = $address;
        $newShowroom->SHRM_ACTV = 0; //showroom pending activation
        $newShowroom->SHRM_MOB2 = $mobileNumber2;
        $newShowroom->SHRM_IMGE = $accountImagePath;
        $newShowroom->SHRM_OWNR_ID = $ownerID ?? Auth::user()->id;
        $newShowroom->SHRM_BLNC = 0;
        try {
            $newShowroom->save();
            return $newShowroom;
        } catch (Exception $e) {
            throw $e;
            return false;
        }
    }

    function setBankInfo($bankBranchName, $bankAccountHolderName, $bankAccount, $ibanNumber)
    {
        try {
            DB::transaction(function () use ($bankBranchName, $bankAccountHolderName, $bankAccount, $ibanNumber) {
                if (!$this->isOwner()) {
                    return false;
                }
                if (!$this->hasBank()) {
                    $newBank = new BankInfo();
                    $newBank->BANK_HLDR_NAME = $bankAccountHolderName;
                    $newBank->BANK_ACNT = $bankAccount;
                    $newBank->BANK_BRCH = $bankBranchName;
                    $newBank->BANK_IBAN = $ibanNumber;
                    $newBank->BANK_SHRM_ID = $this->id;
                    $newBank->save();
                    $this->SHRM_BANK_ID = $newBank->id;
                    $this->save();
                } else {
                    $this->load("bankInfo");
                    $oldBank = $this->bankInfo;
                    $oldBank->BANK_HLDR_NAME = $bankAccountHolderName;
                    $oldBank->BANK_ACNT = $bankAccount;
                    $oldBank->BANK_BRCH = $bankBranchName;
                    $oldBank->BANK_IBAN = $ibanNumber;
                    return $oldBank->save();
                }
            });
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    function addSubscription(int $plan_id, string $type, int $days, float $amount, string $transaction_id): Subscription
    {
        $owner = Auth::user();

        if ($owner == null || !is_a($owner, Seller::class) || $owner->id !== $this->SHRM_OWNR_ID)
            abort(403, "Unauthorized request");

        $subscription = new Subscription;

        try {

            DB::transaction(function () use ($subscription, $amount, $transaction_id, $plan_id, $type, $days, $owner) {
                $newPayment = new Payment();

                $newPayment->seller_id = $owner->id;
                $newPayment->transaction_id = $transaction_id;
                $newPayment->amount = $amount;
                $newPayment->title = "Subscription Payment";

                $subscription = $this->subscriptions()->create([
                    "plan_id"   =>  $plan_id,
                    "state"      =>  Subscription::ACTIVE_STATE,
                    "type"      =>  $type,
                    "plan_id"   => (new Carbon())->addDays($days),
                    "seller_id" =>  $owner->id
                ]);

                $newPayment->payable()->associate($subscription);
                $newPayment->save();
            });
        } catch (Exception $e) {
            report($e);
            return false;
        }

        return $subscription;
    }

    function checkLimit(int $limit_type, bool $abortIfFalse = false, int $capacityToAdd = 0): int
    {
        $this->append('active_plan');

        $res = 0;

        switch ($limit_type) {
            case Plan::USERS_LIMIT:
                $res = $this->checkUsersLimit($capacityToAdd);
                break;

            case Plan::ADMINS_LIMIT:
                $res = $this->checkAdminsLimit($capacityToAdd);
                break;

            case Plan::MODELS_LIMIT:
                $res = $this->checkModelsLimit($capacityToAdd);
                break;

            default:
                $res = PHP_INT_MAX;
        }

        if (!$res && $abortIfFalse) {
            abort(406, "Limit reached");
        }

        return $res;
    }

    private function checkAdminsLimit(int $capacityToAdd = 0): int
    {
        $limit = $this->active_plan->admins_limit;
        if ($limit === -1) return PHP_INT_MAX;

        $sellersCount = $this->sellers()->get()->count();
        return $limit - ($sellersCount + $capacityToAdd);
    }

    private function checkUsersLimit(int $capacityToAdd = 0): int
    {
        $limit = $this->active_plan->users_limit;
        if ($limit === -1) return PHP_INT_MAX;

        $sellersCount = $this->users_count + $capacityToAdd;
        return $limit - ($sellersCount + $capacityToAdd);
    }

    private function checkModelsLimit(int $capacityToAdd = 0)
    {
        $limit = $this->active_plan->models_limit;

        if ($limit === -1) return PHP_INT_MAX;

        return $limit - ($this->models_count + $capacityToAdd);
    }

    private function checkOffersLimit(int $capacityToAdd = 0)
    {
        $limit = $this->active_plan->offers_limit;

        if ($limit === -1) return PHP_INT_MAX;

        return $limit - ($this->monthly_offers + $capacityToAdd);
    }

    public function getUsersCountAttribute()
    {
        return $this->sellers()->get()->count();
    }

    public function getModelsCountAttribute()
    {
        return DB::table('models')
        ->selectRaw('DISTINCT models.id')
        ->join('cars', 'CAR_MODL_ID', '=', 'models.id')
        ->join('showroom_catalog', 'SRCG_CAR_ID', '=', 'cars.id')
        ->where('SRCG_SHRM_ID', $this->id)
        ->get()->count();
    }

    public function getMonthlyOffersAttribute()
    {
        $startOfMonth = (new Carbon)->format('Y-m-01');
        $endOfMonth = (new Carbon)->format('Y-m-t');
        return $this->offers()->whereBetween("created_at", [
            $startOfMonth,
            $endOfMonth,
        ])
            ->get()->count();
    }

    function deleteBankInfo()
    {
        if (!$this->isOwner() || !$this->hasBank()) {
            return false;
        }
        try {
            DB::transaction(function () {
                $this->load("bankInfo");
                $this->SHRM_BANK_ID = null;
                $this->save();
                $this->bankInfo->delete();
            });
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    function hasBank()
    {
        return ($this->SHRM_BANK_ID != NULL && is_numeric($this->SHRM_BANK_ID));
    }


    function addCommercialRecord($record, $frontImage, $backImage)
    {
        if (!$this->isOwner()) {
            return false;
        }
        $this->SHRM_RECD = $record;
        $this->SHRM_RECD_FRNT = $frontImage;
        $this->SHRM_RECD_BACK = $backImage;
        $this->SHRM_RECD_STTS = "Submitted";
        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
    }

    function toggleActiveStatus(bool $isActive)
    {
        $user = Auth::user();
        if (!(is_a($user, "DashUser") || (is_a(Auth::user(), "Seller") && $this->isOwner()))) {
            //dont allow if not dashboard user or seller (owner)
            return false;
        }
        $this->SHRM_ACTV = $isActive;
        return $this->save();
    }

    static function searchText(string $searchText)
    {
        $searchText = strtolower($searchText);
        return self::with("owner")->where("SHRM_NAME", "LIKE", "%" . $searchText . "%")->get();
    }

    function inviteSellerToShowroom($sellerID)
    {
        try {
            $newJoinRequest = $this->joinRequests()->updateOrCreate([
                "JNRQ_SLLR_ID"  =>  $sellerID,
                "JNRQ_STTS"     =>  JoinRequest::REQ_BY_SHOWROOM
            ]);
            return $newJoinRequest;
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function joinRequestersQuery()
    {
        return $this->joinRequesters()->select("sellers.*", "join_requests.JNRQ_STTS")->get();
    }

    static function searchShowrooms($searchText)
    {
        $searchText = strtolower($searchText);
        return self::where("SHRM_NAME", "LIKE", "%" . $searchText . "%")->get();
    }

    function deleteJoinShowroomRequest($requestID)
    {
        return $this->joinRequests()->where("join_requests.id", $requestID)->delete();
    }

    function acceptJoinRequest($joinRequest)
    {
        $joinRequest = JoinRequest::findOrFail($joinRequest);
        if ($joinRequest->JNRQ_STTS == JoinRequest::REQ_BY_SELLER)
            return $joinRequest->acceptRequest();
        else
            return false;
    }

    function toggleRecordVerificationStatus($status = true)
    {
        if ($status)
            $this->SHRM_RECD_STTS = "Valid";
        $this->SHRM_RECD_STTS = "Rejected";

        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
    }

    function initiateEmailVerfication()
    {
        $emailHandler = new EmailsHandler();
        return $emailHandler->sendEmailVerficationCode($this->SHRM_MAIL);
    }

    function initiateMobileNumber1Verification()
    {
        if ($this->SHRM_MOB1_VRFD == 0) { //not verified already
            $smsHandler = new SmsHandler();
            return $smsHandler->sendMobileVerficationCode($this->SHRM_MOB1);
        } else {
            return true;
        }
    }

    function initiateMobileNumber2Verification()
    {
        if ($this->SHRM_MOB2 != null)
            if ($this->SHRM_MOB2_VRFD == 0) { //not verified already
                $smsHandler = new SmsHandler();
                return $smsHandler->sendMobileVerficationCode($this->SHRM_MOB1);
            } else {
                return true;
            }
        else {
            //mobile number 2 is set to null
            return false;
        }
    }

    function verifyEmail($emailCode)
    {
        $emailHandler = new EmailsHandler();

        if ($emailHandler->confirmEmailVerfication($this->SHRM_MAIL, $emailCode)) {
            $this->SHRM_MAIL_VRFD = 1;
            try {
                return $this->save();
            } catch (Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    function verifyMobileNumber1Code($sentSMSCode)
    {
        $smsHandler = new SmsHandler();
        if ($smsHandler->confirmMobNumber($this->SHRM_MOB1, $sentSMSCode)) {
            $this->SHRM_MOB1_VRFD = 1;
            try {
                return $this->save();
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    function verifyMobileNumber2Code($sentSMSCode)
    {
        $smsHandler = new SmsHandler();
        if ($smsHandler->confirmMobNumber($this->SHRM_MOB2, $sentSMSCode)) {
            $this->SHRM_MOB2_VRFD = 1;
            try {
                return $this->save();
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    function setImage($imagePath)
    {
        if (!$this->isManager()) {
            return false;
        }
        $this->SHRM_IMGE = $imagePath;
        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
    }

    function deleteShowroom()
    {
        try {
            DB::transaction(function () {
                $this->joinRequests()->delete();
                $this->bankInfo()->delete();
                foreach ($this->sellers as $seller) {
                    $seller->unsetShowroom();
                }
                foreach ($this->catalogItems as $item) {
                    foreach ($item->details as $color) {
                        $color->delete();
                    }
                    $item->delete();
                }
            });
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    ////Accessors
    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromDate($date)->format("Y-m-d H:i:s");
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::createFromDate($date)->format("Y-m-d H:i:s");
    }

    /**
     * 
     * @return array managersIDs including the owner
     */
    public function getManagers()
    {
        $ret = [];
        $ret[0] = $this->SHRM_OWNR_ID;
        $adminSellers = $this->sellers()->where("SLLR_CAN_MNGR", 1)->get();
        $i = 1;
        foreach ($adminSellers as $admin) {
            $ret[$i++] = $admin->id;
        }
        return $ret;
    }

    public function getAvailableOfferRequests()
    {

        return OfferRequest::getAvailableOffers($this->id);
    }

    /**
     * 
     * @return Collection of all new offers 
     */
    public function getPendingOffers()
    {
        return $this->offers()->where("OFFR_STTS", Offer::NEW_KEY)->whereDate("OFFR_EXPR_DATE", ">=", date("Y-m-d"))->get();
    }

    /**
     * 
     * @return Collection of offers with a response (Accepted or Declines) for the last month
     */
    public function getApprovedOffers()
    {
        return $this->offers()->whereIn("OFFR_STTS", [Offer::ACCEPTED_KEY, Offer::DECLINED_KEY])->whereDate("created_at", ">", (new Carbon())->subMonth())->get();
    }

    /**
     * 
     * @return Collection of expired offers for the last month
     */
    public function getExpiredOffers()
    {
        return $this->offers()->whereIn("OFFR_STTS", [Offer::NEW_KEY, Offer::EXPIRED_KEY])->whereDate("OFFR_EXPR_DATE", ">", date("Y-m-d"))->whereDate("created_at", ">", (new Carbon())->subMonth())->get();
    }

    public function getAvailableJoinRequests()
    {
        return $this->joinRequests()->where("JNRQ_STTS", "!=", JoinRequest::ACCEPTED)->get();
    }

    /**
     * Checks if the seller is associated with the showroom
     * @param sellerID seller id 
     * @return bool true if the seller is associated with the showroom
     */
    public function hasSeller(int $sellerID)
    {
        return ($this->sellers()->where("sellers.id", $sellerID)->get()->count() > 0);
    }

    //Accessors
    public function getImageUrlAttribute()
    {
        return (isset($this->SHRM_IMGE)) ? Storage::url($this->SHRM_IMGE) : null;
    }

    /////////relations
    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, CatalogItem::class, "SRCG_SHRM_ID", "SRCG_CAR_ID");
    }

    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class, "SRCG_SHRM_ID");
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class, "SLLR_SHRM_ID");
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, "SHRM_CITY_ID");
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Seller::class, "SHRM_OWNR_ID");
    }

    public function bankInfo(): BelongsTo
    {
        return $this->belongsTo(BankInfo::class, "SHRM_BANK_ID");
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(JoinRequest::class, "JNRQ_SHRM_ID");
    }

    public function joinRequesters(): BelongsToMany
    {
        return $this->belongsToMany(Seller::class, JoinRequest::class, "JNRQ_SHRM_ID", "JNRQ_SLLR_ID");
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, "OFFR_SHRM_ID");
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, "showroom_id")->orderBy('id', 'desc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, "showroom_id")->orderBy('id', 'desc');
    }

    public function getActiveSubscriptionAttribute(): Subscription|null
    {
        return $this->subscriptions()
            ->whereDate("subscriptions.expiry_date", "<=", date('Y-m-d'))
            ->latest()
            ->orderBy('id', 'desc')
            ->limit(1)
            ->first();
    }

    public function getActivePlanAttribute(): Plan
    {
        return $this->active_subscription->plan ?? Plan::free();
    }

    public function getBuyers()
    {
        return Buyer::join('offers', 'OFFR_BUYR_ID', '=', 'buyers.id')
            ->join('showrooms', 'OFFR_SHRM_ID', '=', 'showrooms.id')
            ->join('cars', 'OFFR_CAR_ID', '=', 'cars.id')
            ->select('offers.*', 'showrooms.*', 'cars.*', 'buyers.*')
            ->where('showrooms.id', $this->id)->get();
    }

    /****
     * Checks wether the requester is the owner
     * @return bool
     */
    public function isOwner()
    {
        $seller = Auth::user();
        return (is_a($seller, Seller::class) && $this->SHRM_OWNR_ID == $seller->id);
    }

    /****
     * Checks wether the requester is a manager
     * @return bool
     */
    public function isManager()
    {
        $seller = Auth::user();
        return $this->isOwner() || is_a($seller, Seller::class) && $this->id == $seller->SLLR_SHRM_ID;
    }
}
