<?php

namespace App\Models\Users;

use App\Models\Offers\Offer;
use App\Services\EmailsHandler;
use App\Services\SmsHandler;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Seller extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    //table is Sellers
    const ACCESS_TOKEN = "access_sellers_api";
    protected $table = "sellers";
    protected $appends = array('cars_sold_price', 'cars_sold_count', 'image_url');
    public $timestamps = true;

    ///profile functions
    static function create($name, $email, $mobileNumber1, $password, $mobileNumber2 = null, $accountImagePath = null)
    {
        $newseller = new self();
        $newseller->SLLR_NAME = $name;
        $newseller->SLLR_MAIL = $email;
        $newseller->SLLR_MOB1 = $mobileNumber1;
        $newseller->SLLR_PASS = Hash::make($password);
        $newseller->SLLR_MOB2 = $mobileNumber2;
        $newseller->SLLR_IMGE = $accountImagePath;

        try {
            $newseller->save();
            return $newseller;
        } catch (Exception $e) {
            throw $e;
        }
    }

    function updateInfo($name, $mobileNumber1, $mobileNumber2 = null,  $accountImagePath = null) : bool
    {
        $this->SLLR_NAME = $name;
        if ($this->SLLR_MOB1 != $mobileNumber1) {
            $this->SLLR_MOB1 = $mobileNumber1;
            $this->SLLR_MOB1_VRFD = 0;
        }
        $this->SLLR_MOB2 = $mobileNumber2;
        if ($accountImagePath != null) {
            $this->SLLR_IMGE = $accountImagePath;
        }
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    function getApiToken($deviceName)
    {
        return $this->createToken($deviceName, [self::ACCESS_TOKEN])->plainTextToken;
    }

    static function searchText($searchText)
    {
        $searchText = strtolower($searchText);
        $query = self::with("showroom")->where("SLLR_NAME", "LIKE", "%" . $searchText . "%")->orWhere("SLLR_MAIL", "LIKE", "%" . $searchText . "%")
            ->orWhere("SLLR_MOB1", "LIKE", "%" . $searchText . "%");
        return $query->get();
    }

    /**
     * 
     * returns an array of sellers who can sell the mentioned car
     * @param array $colorIDs array of model color ID (integers) 
     * @return Collection
     */
    static function getCarSellers($carID, $colorIDs = [])
    {
        $query = self::join("showrooms", "SLLR_SHRM_ID", '=', "showrooms.id")
            ->join("showroom_catalog", "showrooms.id", '=', 'SRCG_SHRM_ID')
            ->where("showroom_catalog.SRCG_CAR_ID", '=', $carID)
            ->select("sellers.id");
        if ($colorIDs != null && count($colorIDs) > 0) {
            $query = $query->join(
                "showroom_catalog_details",
                function ($join) use ($colorIDs) {
                    $join->on("SRCD_SRCG_ID", '=', 'showroom_catalog.id')
                        ->whereIn("SRCD_COLR_ID", $colorIDs)->orWhere("SRCG_ALL_COLR", '=', 1);
                }
            );
        }
        return $query->get();
    }

    //Authentication Stuff
    static function login($emailOrMob, $password, $deviceName)
    {
        $seller = self::where("SLLR_MAIL", $emailOrMob)->orWhere("SLLR_MOB1", $emailOrMob)->first();
        if ($seller != null) {
            $passwordStatus = Hash::check($password, $seller->SLLR_PASS);
            if ($passwordStatus) {
                $seller->load("showroom");
                return [
                    "seller" => $seller,
                    "apiKey" => $seller->createToken($deviceName, [self::ACCESS_TOKEN])->plainTextToken
                ];
            } else {
                return -2; //incorrect password
            }
        } else {
            return -1; //incorrect email
        }
    }

    function getCarsSoldPriceAttribute()
    {
        return $this->offers()->where('OFFR_STTS', Offer::ACCEPTED_KEY)->get('OFFR_PRCE')->sum('OFFR_PRCE');
    }

    function getCarsSoldCountAttribute()
    {
        return $this->offers()->where('OFFR_STTS', Offer::ACCEPTED_KEY)->get('OFFR_PRCE')->count();
    }

    function initiateEmailVerfication()
    {
        $emailHandler = new EmailsHandler();
        return $emailHandler->sendEmailVerficationCode($this->SLLR_MAIL);
    }

    function initiateMobileNumber1Verification()
    {
        if ($this->SLLR_MOB1_VRFD == 0) { //not verified already
            $smsHandler = new SmsHandler();
            return $smsHandler->sendMobileVerficationCode($this->SLLR_MOB1);
        } else {
            return true;
        }
    }

    function initiateMobileNumber2Verification()
    {
        if ($this->SLLR_MOB2 != null)
            if ($this->SLLR_MOB2_VRFD == 0) { //not verified already
                $smsHandler = new SmsHandler();
                return $smsHandler->sendMobileVerficationCode($this->SLLR_MOB1);
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

        if ($emailHandler->confirmEmailVerfication($this->SLLR_MAIL, $emailCode)) {
            $this->SLLR_MAIL_VRFD = 1;
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
        if ($smsHandler->confirmMobNumber($this->SLLR_MOB1, $sentSMSCode)) {
            $this->SLLR_MOB1_VRFD = 1;
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
        if ($smsHandler->confirmMobNumber($this->SLLR_MOB2, $sentSMSCode)) {
            $this->SLLR_MOB2_VRFD = 1;
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
        $this->SLLR_IMGE = $imagePath;
        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
    }

    function setAsManager($showroomID)
    {
        $this->SLLR_CAN_MNGR = 1;
        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
    }

    function setShowroom($showroomID)
    {
        $this->SLLR_SHRM_ID = $showroomID;
        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
    }

    function unsetShowroom()
    {
        $this->SLLR_SHRM_ID = NULL;
        $this->SLLR_CAN_MNGR = 0;
        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
    }


    function acceptJoinInvitation($joinRequestID)
    {
        $joinRequest = JoinRequest::findOrFail($joinRequestID);
        if ($joinRequest->JNRQ_STTS == JoinRequest::REQ_BY_SHOWROOM)
            return $joinRequest->acceptRequest();
        else
            return false;
    }

    function submitJoinShowroomRequest($showroomID)
    {
        return $this->joinRequests()->updateOrCreate([
            "JNRQ_SHRM_ID"  =>  $showroomID,
            "JNRQ_STTS"     =>  JoinRequest::REQ_BY_SELLER
        ]);
    }

    function deleteJoinShowroomRequest($requestID)
    {
        return $this->joinRequests()->where("join_requests.id", $requestID)->delete();
    }

    public static function isEmailTaken($email)
    {
        $res = self::where("SLLR_MAIL", $email)->get();
        if (count($res) > 0) return true;
        else return false;
    }

    public static function isPhoneTaken($phone)
    {
        $res = self::where("SLLR_MOB1", $phone)->orWhere("SLLR_MOB2", $phone)->get();
        if (count($res) > 0) return true;
        else return false;
    }

    //Accessors
    public function getImageUrlAttribute()
    {
        return (isset($this->SLLR_IMGE)) ? Storage::url($this->SLLR_IMGE) : null;
    }

    ////relation
    public function showroom()
    {
        return $this->belongsTo(Showroom::class, "SLLR_SHRM_ID");
    }

    public function joinRequests()
    {
        return $this->hasMany(JoinRequest::class, "JNRQ_SLLR_ID");
    }

    public function joinRequestShowrooms()
    {
        return $this->belongsToMany(Showroom::class, JoinRequest::class, "JNRQ_SLLR_ID", "JNRQ_SHRM_ID");
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, "OFFR_SLLR_ID");
    }
    ///Authentication attributes
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'SLLR_MAIL';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->SLLR_PASS;
    }

    protected $hidden = [
        "SLLR_PASS"
    ];
}
