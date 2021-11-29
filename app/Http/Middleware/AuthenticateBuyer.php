<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AbstractApiController;
use App\Models\Buyer;
use Closure;

class AuthenticateBuyer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->user()->tokenCan(Buyer::ACCESS_TOKEN)){
            AbstractApiController::sendResponse(false, "User unauthorized");
        }
        return $next($request);
    }
}