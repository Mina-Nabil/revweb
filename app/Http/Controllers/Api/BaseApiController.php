<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users\Buyer;
use App\Models\Users\MailVerification;
use App\Models\Users\MobVerification;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BaseApiController extends Controller
{

    public function verifyMailCode(Request $request)
    {
        self::validateRequest($request, [
            "code"  =>  "required|exists:mail_verifications,code",
            "mail" =>  "required|exists:mail_verifications,mail"
        ]);
        $code = MailVerification::getMailVerfication($request->mail);
        $expire = new Carbon($code->expiry);

        if ($code->code != $request->code) {
            self::sendResponse(false, "Code mismatch");
        }

        if ($expire->isPast()) {
            self::sendResponse(false, "Code expired");
        }
        $code->loadMissing('mailer');
        /** @var Seller|Buyer|Showroom */
        $mailer = $code->mailer;
        $mailer->verifyEmail();
        self::sendResponse(true, "Email verified");
    }

    public function verifyMobCode(Request $request)
    {

        Log::info("Request data");
        Log::info("Code: " . $request->code);
        Log::info("Mobile: " . $request->mob);

        self::validateRequest($request, [
            "code"  =>  "required|exists:mob_verifications,code",
            "mob" =>  "required|exists:mob_verifications,mob"
        ]);
        $code = MobVerification::getMobVerfication($request->mob);
        $expire = new Carbon($code->expiry);

        if ($code->code != $request->code) {
            self::sendResponse(false, "Code mismatch");
        }

        if ($expire->isPast()) {
            self::sendResponse(false, "Code expired");
        }
        $code->loadMissing('mober');
        /** @var Seller|Buyer|Showroom */
        $mober = $code->mober;
        $mober->verifyMob($request->mob);
        self::sendResponse(true, "Mob verified");
    }

    public function resendMailCode()
    {
        /** @var Seller|Buyer */
        $user = Auth::user();
        $user->initiateEmailVerfication();
        self::sendResponse(true, "Email resent");
    }

    public function resendMob1Code()
    {
        /** @var Seller|Buyer */
        $user = Auth::user();
        $user->initiateMobileNumber1Verification();
        self::sendResponse(true, "Sms resent");
    }

    public function resendMob2Code()
    {
        /** @var Seller|Buyer */
        $user = Auth::user();
        $user->initiateMobileNumber2Verification();
        self::sendResponse(true, "Sms resent");
    }

    public function deleteUser()
    {
        /** @var Seller|Buyer */
        $user = Auth::user();
        $user->delete();
    }

    /**
     * validate request via passed rules
     * 
     * @param array $rules
     * @param Request $request
     * @param string $validationErrorMessage error message to return in case validation failed
     */
    public static function validateRequest($request, $rules, $validationErrorMessage = "Validation Failed")
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            self::sendResponse(false, $validationErrorMessage, ['errors' => $validator->errors()], true, 422);
        } else return true;
    }

    /**
     * 
     * echo generic json message and body if found then Dies
     * 
     * 
     * @param bool $apiCallStatus Passed or failed
     * @param string $message message received by client
     * @param mixed|null $body object to return as json 
     * @param bool $die kills the request if true
     * @param int $status response status code
     */
    public static function sendResponse(bool $apiCallStatus, string $message, $body = null, $die = true, $status = 200)
    {
        response()->json(new ApiMessage($apiCallStatus, $message, $body), $status)->withHeaders(['Content-Type' => 'application/json'])->send();
        if ($die)
            die;
    }
}


class ApiMessage
{
    public $status;
    public $body;
    public $message;

    function __construct(bool $status, $message, $body = null)
    {
        $this->status = $status;
        $this->message = $message;
        if ($body !== null)
            $this->body = $body;
    }
}
