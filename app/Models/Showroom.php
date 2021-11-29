<?php

namespace App\Models;

use App\Services\EmailsHandler;
use App\Services\SmsHandler;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Showroom extends Model
{

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    /***
     * Retrieves all the cars & colors the showroom is selling
     * 
     */
    function getCatalogCars()
    {
        $this->load("catalogItems");
        return $this->catalogItems;
    }

    /****
     * Retrieves all the cars available for the showroom to add to his catalog of cars
     * Using brands associated to his profile
     */
    function getCarpool()
    {
        $this->load("brands");
        $brandIDs = $this->brands->pluck("id");
        return Car::getCarsByBrandIDs($brandIDs);
    }

    /****
     * Retrieves all brands associated to his profile
     */
    function getAssociatedBrands()
    {
        $this->load("brands");
        return $this->brands;
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
                    "SRCG_CAR_ACTV"     =>  1,
                ]);
                $catalogItem->details()->delete();
                if ($catalogItem) {
                    if ($colors == null) {
                        $car = Car::with("model.colors")->findOrFail($carID);
                        $colors = $car->model->colors->pluck('id');
                    }
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
        $catalogEntry = $this->cars()->where("SRCG_CAR_ID", $carID)->find();
        try {
            DB::transaction(function () use ($catalogEntry) {
                CatalogItemDetails::where("SRCD_SRCG_ID", $catalogEntry->id)->delete();
                $catalogEntry->delete();
            });
            return true;
        } catch (Exception $e) {
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
        $catalogEntry = $this->cars()->where("SRCG_CAR_ID", $carID)->find();
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
        if (!$this->isOwner()) {
            return false;
        }
        if ($this->hasBank()) {
            $newBank = new BankInfo();
            $newBank->BANK_HLDR_NAME = $bankAccountHolderName;
            $newBank->BANK_ACNT = $bankAccount;
            $newBank->BANK_BRCH = $bankBranchName;
            $newBank->BANK_IBAN = $ibanNumber;
            $newBank->BANK_SHRM_ID = $this->id;
            try {
                return $newBank->save();
            } catch (Exception $e) {
                return false;
            }
        } else {
            $this->load("bankInfo");
            $this->bankInfo->BANK_HLDR_NAME = $bankAccountHolderName;
            $this->bankInfo->BANK_ACNT = $bankAccount;
            $this->bankInfo->BANK_BRCH = $bankBranchName;
            $this->bankInfo->BANK_IBAN = $ibanNumber;
            try {
                return $this->bankInfo->save();
            } catch (Exception $e) {
                return false;
            }
        }
    }

    function hasBank()
    {
        return $this->SHRM_BANK_ID !== NULL && is_numeric($this->SHRM_BANK_ID);
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


    //profile functions
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

    ////Accessors
    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromDate($date)->format("Y-m-d H:i:s");
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::createFromDate($date)->format("Y-m-d H:i:s");
    }

    ///relations
    public function cars()
    {
        return $this->belongsToMany(Car::class, CatalogItem::class, "SRCG_SHRM_ID", "SRCG_CAR_ID");
    }

    public function catalogItems()
    {
        return $this->hasMany(CatalogItem::class, "SRCG_SHRM_ID");
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, "showrooms_brands", "SRBR_SHRM_ID", "SRBR_BRND_ID");
    }

    public function sellers()
    {
        return $this->hasMany(Seller::class, "SLLR_SHRM_ID");
    }

    public function city()
    {
        return $this->belongsTo(City::class, "SHRM_CITY_ID");
    }

    public function bankInfo()
    {
        return $this->belongsTo(BankInfo::class, "SHRM_BANK_ID");
    }

    public function joinRequests()
    {
        return $this->hasMany(Brand::class, "showrooms_brands", "SRBR_SHRM_ID", "SRBR_BRND_ID");
    }

    /****
     * Checks wether the requester is the owner
     * @return bool
     */
    public function isOwner()
    {
        $seller = Auth::user();
        return (is_a($seller, "App\Models\Seller") && $this->SHRM_OWNR_ID == $seller->id);
    }

    /****
     * Checks wether the requester is a manager
     * @return bool
     */
    public function isManager()
    {
        $seller = Auth::user();
        return $this->isOwner() || is_a($seller, "Seller") && $this->id == $seller->SLLR_SHRM_ID;
    }
}
