<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AbstractApiController;
use App\Models\Seller;
use Closure;

class AuthenticateSeller
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
        if(!$request->user()->tokenCan(Seller::ACCESS_TOKEN)){
            AbstractApiController::sendResponse(false, "User unauthorized");
        }
        return $next($request);
    }
}
