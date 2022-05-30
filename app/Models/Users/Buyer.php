<?php

namespace App\Models\Users;

use App\Models\Offers\Offer;
use App\Services\EmailsHandler;
use App\Services\SmsHandler;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;

class Buyer extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    //table is Buyers
    const ACCESS_TOKEN = "access_buyers_api" ;
    protected $table = "buyers";
    public $timestamps = true;

    static function create($name, $email, $mobileNumber1, $bday, $gender, $password, $buyerNationalID = null, $mobileNumber2 = null, $bankAccount = null, $ibanNumber = null, $accountImagePath = null, $buyerNationalIDFrontImagePath = null, $buyerNationalIDBackImagePath = null)
    {
        $newbuyer = new self();
        $newbuyer->BUYR_NAME = $name;
        $newbuyer->BUYR_MAIL = $email;
        $newbuyer->BUYR_MOB1 = $mobileNumber1;
        $newbuyer->BUYR_PASS = Hash::make($password);
        $newbuyer->BUYR_MOB2 = $mobileNumber2;
        $newbuyer->BUYR_BDAY = $bday;
        $newbuyer->BUYR_GNDR = $gender;
        $newbuyer->BUYR_NTID = $buyerNationalID;
        $newbuyer->BUYR_BANK = $bankAccount;
        $newbuyer->BUYR_IBAN = $ibanNumber;
        $newbuyer->BUYR_IMGE = $accountImagePath;
        $newbuyer->BUYR_NTID_FRNT = $buyerNationalIDFrontImagePath;
        $newbuyer->BUYR_NTID_BACK = $buyerNationalIDBackImagePath;
        $newbuyer->BUYR_BDAY = date('Y-m-d');

        try {
            $newbuyer->save();
            return $newbuyer;
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class] );
            throw $e;
        }
    }



    //Authentication Stuff
    static function login($emailOrMob, $password, $deviceName)
    {
        $buyer = self::where("BUYR_MAIL", $emailOrMob)->orWhere("BUYR_MOB1", $emailOrMob)->first();
        if ($buyer != null) {
            $passwordStatus = Hash::check($password, $buyer->BUYR_PASS);
            if ($passwordStatus) {
                return $buyer->createToken($deviceName, [self::ACCESS_TOKEN])->plainTextToken;
            } else {
                return -2; //Incorrect Password
            }
        } else {
            return -1; //Incorrect Email/Mobile Number
        }
    }

    function getApiToken($deviceName){
        return $this->createToken($deviceName, [self::ACCESS_TOKEN])->plainTextToken;
    }

    function initiateEmailVerfication()
    {
        $emailHandler = new EmailsHandler();
        return $emailHandler->sendEmailVerficationCode($this->BUYR_MAIL);
    }

    function initiateMobileNumber1Verification()
    {
        if ($this->BUYR_MOB1_VRFD == 0) { //not verified already
            $smsHandler = new SmsHandler();
            return $smsHandler->sendMobileVerficationCode($this->BUYR_MOB1);
        } else {
            return true;
        }
    }

    function initiateMobileNumber2Verification()
    {
        if ($this->BUYR_MOB2 != null)
            if ($this->BUYR_MOB2_VRFD == 0) { //not verified already
                $smsHandler = new SmsHandler();
                return $smsHandler->sendMobileVerficationCode($this->BUYR_MOB1);
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

        if ($emailHandler->confirmEmailVerfication($this->BUYR_MAIL, $emailCode)) {
            $this->BUYR_MAIL_VRFD = 1;
            try{
                $this->save();
            } catch (Exception $e) {
                Log::alert($e->getMessage(), ["DB" => self::class] );
                throw $e;
            }
            return true;
        } else {
            return false;
        }
    }

    function verifyMobileNumber1Code($sentSMSCode)
    {
        $smsHandler = new SmsHandler();
        if ($smsHandler->confirmMobNumber($this->BUYR_MOB1, $sentSMSCode)) {
            $this->BUYR_MOB1_VRFD = 1;
            try {
                $this->save();
            } catch (Exception $e) {
                Log::alert($e->getMessage(), ["DB" => self::class] );
                throw $e;
            }
        } else {
            return false;
        }
    }

    function verifyMobileNumber2Code($sentSMSCode)
    {
        $smsHandler = new SmsHandler();
        if ($smsHandler->confirmMobNumber($this->BUYR_MOB2, $sentSMSCode)) {
            $this->BUYR_MOB2_VRFD = 1;
            try {
                $this->save();
            } catch (Exception $e) {
                Log::alert($e->getMessage(), ["DB" => self::class] );
                throw $e;
            }
        } else {
            return false;
        }
    }

    function addBuyerNationalID($nationalid, $frontImage, $backImage)
    {
        $this->BUYR_NTID = $nationalid;
        $this->BUYR_NTID_FRNT = $frontImage;
        $this->BUYR_NTID_BACK = $backImage;
        $this->BUYR_NTID_STTS = "Submitted";
        try {
            return $this->save();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class] );
            throw $e;
        }
    }

    function toggleNationalIDVerificationStatus($status = true)
    {
        if ($status)
            $this->BUYR_NTID_STTS = "Valid";
        $this->BUYR_NTID_STTS = "Rejected";
        try {
            $this->save();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class] );
            throw $e;
        }
    }

    function setImage($imagePath)
    {
        $this->BUYR_IMGE = $imagePath;
        try {
            $this->save();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class] );
            throw $e;
        }
    }


    //relations
    function favCars()
    {
        return $this->belongsToMany(Car::class, "fav_cars", "FAVC_BUYR_ID", "FAVC_CAR_ID");
    }

    function ownedCars()
    {
        return $this->belongsToMany(Car::class, "owned_cars", "OWND_BUYR_ID", "OWND_CAR_ID");
    }

    public function offers():HasMany{
        return $this->hasMany(Offer::class, "");
    }


    ///Authentication attributes
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'BUYR_MAIL';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->BUYR_PASS;
    }

    protected $hidden = [
        "BUYR_PASS"
    ];
}
