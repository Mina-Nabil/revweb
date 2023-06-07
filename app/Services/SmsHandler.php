<?php

namespace App\Services;

use App\Models\Users\Buyer;
use App\Models\Users\MobVerification;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsHandler
{

    function sendSms($mobileNumber, $message)
    {

        return Http::asForm()->post('https://smssmartegypt.com/sms/api/json/', [
            'username' => Config::get('services.sms.user'),
            'password' => Config::get('services.sms.key'),
            'sendername' => Config::get('services.sms.sender_id'),
            'mobiles' => $mobileNumber,
            'message' => $message,
        ]);
    }

    public function sendMobileVerficationCode($user, $mob1 = true)
    {
        if ($user != null) {
            $mob = null;
            $name = null;

            if (is_a($user, Seller::class)) {
                $mob = $mob1 ? $user->SLLR_MOB1 : $user->SLLR_MOB2;
                $name = $user->SLLR_NAME;
            } else if (is_a($user, Buyer::class)) {
                $mob = $mob1 ? $user->BUYR_MOB1 : $user->BUYR_MOB2;
                $name = $user->BUYR_NAME;
            } else if (is_a($user, Showroom::class)) {
                $user->load('owner');
                $mob = $mob1 ? $user->SHRM_MOB1 : $user->SHRM_MOB2;
                $name = $user->owner->SLLR_NAME;
            } else {
                report(new Exception("Invalid class"));
            }

            $code = rand(1000, 9999);
            MobVerification::newVerification($user, $mob, $code);
            try {

                $message = "Hi {$name} \n";
                $message .= "Please use the following code to verify your mobile number";
                $message .= "{$code} \n";
                $message .= "Thank you";
                self::sendSms($mob, $message);

                return true;
            } catch (\Exception $e) {
                report($e);
            }
        }

        return false;
        //return true if sms sent
    }

    function confirmMobNumber($mobile, $code)
    {
    }
}
