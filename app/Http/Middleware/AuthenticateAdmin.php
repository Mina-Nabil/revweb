<?php

namespace App\Http\Middleware;

use App\Models\DashUser;
use Closure;

class AuthenticateAdmin
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
        if ( is_a($request->user(), DashUser::class))
            return $next($request);
        else redirect("login");
    }
}
