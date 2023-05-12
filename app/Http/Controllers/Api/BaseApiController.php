<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users\Buyer;
use App\Models\Users\MailVerification;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseApiController extends Controller
{

    public function verifyCode(Request $request)
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
