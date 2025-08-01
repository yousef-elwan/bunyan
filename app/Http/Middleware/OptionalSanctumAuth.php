<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        // if ($request->bearerToken()) {
        //     if (Auth::guard('api')->loginUsingId($request->user('sanctum')->id)) {
        //         return $next($request);
        //     }
        // }

        // if (Auth::guard('web')->check()) {
        //     Auth::setUser(Auth::guard('web')->user());
        // }

        if ($request->bearerToken()) {
            $user = Auth::guard('api')->user();

            if ($user) {
                Auth::setUser($user);
            }
        } elseif (Auth::guard('web')->check()) {
            Auth::setUser(Auth::guard('web')->user());
        }

        return $next($request);
    }
}
