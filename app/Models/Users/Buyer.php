<?php

namespace App\Models\Users;

use App\Models\Offers\Offer;
use App\Models\Offers\OfferRequest;
use App\Services\EmailsHandler;
use App\Services\SmsHandler;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Buyer extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

    //table is Buyers
    const ACCESS_TOKEN = "access_buyers_api";
    protected $table = "buyers";
    protected $appends = array('image_url');
    public $timestamps = true;

    static function create($name, $email, $mobileNumber1, $bday, $gender, $password, $buyerNationalID = null, $mobileNumber2 = null, $bankAccount = null, $ibanNumber = null, $accountImagePath = null, $buyerNationalIDFrontImagePath = null, $buyerNationalIDBackImagePath = null)
    {
        $newbuyer = new self();
        $newbuyer->BUYR_NAME = $name;
        $newbuyer->BUYR_MAIL = $email;
        $newbuyer->BUYR_MOB1 = $mobileNumber1;
        $newbuyer->BUYR_PASS = Hash::make($password);
        $newbuyer->BUYR_MOB2 = $mobileNumber2;
        $newbuyer->BUYR_BDAY = (new Carbon($bday))->format('Y-m-d');
        $newbuyer->BUYR_GNDR = $gender;
        $newbuyer->BUYR_NTID = $buyerNationalID;
        $newbuyer->BUYR_BANK = $bankAccount;
        $newbuyer->BUYR_IBAN = $ibanNumber;
        $newbuyer->BUYR_IMGE = $accountImagePath;
        $newbuyer->BUYR_NTID_FRNT = $buyerNationalIDFrontImagePath;
        $newbuyer->BUYR_NTID_BACK = $buyerNationalIDBackImagePath;

        try {
            $newbuyer->save();
            return $newbuyer;
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }
    ///model function
    public function updateInfo($name, $mobileNumber1, $bday, $gender, $mobileNumber2 = null, $displayImage = null): bool
    {
        $this->BUYR_NAME = $name;
        if ($mobileNumber1 != $this->BUYR_MOB1) {
            $this->BUYR_MOB1 = $mobileNumber1;
            $this->BUYR_MOB1_VRFD = false;
        }
        if ($displayImage != null) {
            $this->BUYR_IMGE = $displayImage;
        }
        $this->BUYR_MOB2 = $mobileNumber2;
        $this->BUYR_BDAY = (new Carbon($bday))->format('Y-m-d');
        $this->BUYR_GNDR = $gender;

        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function setToken($token): bool
    {
        $this->BUYR_PUSH_ID = $token;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    //Authentication Stuff
    static function login($emailOrMob, $password, $deviceName)
    {
        $buyer = self::where("BUYR_MAIL", $emailOrMob)->orWhere("BUYR_MOB1", $emailOrMob)->first();
        if ($buyer != null) {
            $passwordStatus = Hash::check($password, $buyer->BUYR_PASS);
            if ($passwordStatus) {
                return [
                    "buyer" => $buyer,
                    "apiKey" => $buyer->createToken($deviceName, [self::ACCESS_TOKEN])->plainTextToken
                ];
            } else {
                return -2; //Incorrect Password
            }
        } else {
            return -1; //Incorrect Email/Mobile Number
        }
    }

    function getApiToken($deviceName)
    {
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
            try {
                $this->save();
            } catch (Exception $e) {
                Log::alert($e->getMessage(), ["DB" => self::class]);
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
                Log::alert($e->getMessage(), ["DB" => self::class]);
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
                Log::alert($e->getMessage(), ["DB" => self::class]);
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
            Log::alert($e->getMessage(), ["DB" => self::class]);
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
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }

    function setImage($imagePath)
    {
        $this->BUYR_IMGE = $imagePath;
        try {
            $this->save();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }

    //Accessors
    public function getImageUrlAttribute()
    {
        return (isset($this->BUYR_IMGE)) ? Storage::url($this->BUYR_IMGE) : null;
    }

    function getActiveOffers()
    {
        $offers = $this->offers()->where('OFFR_STTS', Offer::NEW_KEY)->whereDate("OFFR_EXPR_DATE", ">=", date('Y-m-d'));
        return $offers->simplePaginate(7);
    }

    function getAllOffers()
    {
        $offers = $this->offers();
        return $offers->simplePaginate(7);
    }

    function getActiveRequests()
    {
        $requests = $this->offer_requests()->append('options')->whereIn("OFRQ_STTS", [OfferRequest::NEW_KEY, OfferRequest::REPLIED_KEY]);
        return $requests->get();
    }

    function getRequestsHistory()
    {
        $requests = $this->offer_requests()->append('options');
        return $requests->simplePaginate(7);
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

    public function offer_requests(): HasMany
    {
        return $this->hasMany(OfferRequest::class, "OFRQ_BUYR_ID");
    }

    public function offers(): HasManyThrough
    {
        return $this->hasManyThrough(Offer::class, OfferRequest::class, "OFRQ_BUYR_ID", "OFFR_OFRQ_ID");
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


    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->SLLR_PUSH_ID;
    }


    protected $hidden = [
        "BUYR_PASS"
    ];
}
