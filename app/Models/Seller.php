<?php

namespace App\Models;

use App\Services\EmailsHandler;
use App\Services\SmsHandler;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Seller extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    //table is Sellers
    const ACCESS_TOKEN = "access_sellers_api";
    protected $table = "sellers";
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

    function updateInfo($name, $email, $mobileNumber1, $password = null, $mobileNumber2 = null)
    {
        $this->SLLR_NAME = $name;
        if ($this->SLLR_MAIL != $email) {
            $this->SLLR_MAIL = $email;
            $this->SLLR_MAIL_VRFD = 0;
        }
        if ($this->SLLR_MOB1 != $mobileNumber1) {
            $this->SLLR_MOB1 = $mobileNumber1;
            $this->SLLR_MOB1_VRFD = 0;
        }
        if ($password != null)
            $this->SLLR_PASS = Hash::make($password);
        $this->SLLR_MOB2 = $mobileNumber2;

        try {
            $this->save();
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }

    function getApiToken($deviceName)
    {
        return $this->createToken($deviceName, [self::ACCESS_TOKEN])->plainTextToken;
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
                    "showroom" => $seller->showroom,
                    "apiKey" => $seller->createToken($deviceName, [self::ACCESS_TOKEN])->plainTextToken
                ];
            } else {
                return -2; //incorrect password
            }
        } else {
            return -1; //incorrect email
        }
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

    function setShowroom($showroomID)
    {
        $this->SLLR_SHRM_ID = $showroomID;
        try {
            return $this->save();
        } catch (Exception $e) {
            return false;
        }
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

    ////relation
    public function showroom()
    {
        return $this->belongsTo(Showroom::class, "SLLR_SHRM_ID");
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
