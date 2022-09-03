<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BaseApiController extends Controller
{

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
