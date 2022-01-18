<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Users\Seller;
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
            BaseApiController::sendResponse(false, "User unauthorized");
        }
        return $next($request);
    }
}
