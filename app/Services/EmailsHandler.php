<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use App\Models\Users\Buyer;
use App\Models\Users\MailVerification;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use Exception;
use Illuminate\Support\Facades\Mail;

class EmailsHandler
{


    function sendEmailVerficationCode(Seller|Buyer|Showroom $user): bool
    {
        if ($user != null) {
            $mail = null;
            $name = null;
            if (is_a($user, Seller::class)) {
                $mail = $user->SLLR_MAIL;
                $name = $user->SLLR_NAME;
            } else if (is_a($user, Buyer::class)) {
                $mail = $user->BUYR_MAIL;
                $name = $user->BUYR_NAME;
            } else if (is_a($user, Showroom::class)) {
                $user->load('owner');
                $mail = $user->SHRM_MAIL;
                $name = $user->owner->SLLR_NAME;
            } else {
                report(new Exception("Invalid class"));
            }

            $code = rand(1000, 9999);
            MailVerification::newVerification($user, $mail, $code);
            try {
                Mail::to($mail)->send(new VerifyEmail($name, $code));
                return true;
            } catch (\Exception $e) {
                report($e);
            }
        }

        return false;
    }


    function confirmEmailVerfication($email, $code)
    {
    }


    function sendEmail($email, $message)
    {
    }
}
