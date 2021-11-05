<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AbstractApiController extends Controller
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
            self::sendResponse(false, $validationErrorMessage, ['errors' => $validator->errors()]);
        } else return true;
    }

    /**
     * 
     * echo generic json message and body if found then Dies
     * 
     * 
     * @param bool $status Passed or failed
     * @param string $message message received by client
     * @param mixed|null $body object to return as json 
     * @param bool $die kills the request if true
     */
    public static function sendResponse($apiCallStatus, $message, $body = null, $die = true)
    {
        response(json_encode(new ApiMessage($apiCallStatus, $message, $body), JSON_UNESCAPED_UNICODE))->withHeaders(['Content-Type' => 'application/json'])->send();
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
