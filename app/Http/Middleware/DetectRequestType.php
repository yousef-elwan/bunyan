<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectRequestType
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            $request->attributes->set('request_type', 'api');
        } else {
            $request->attributes->set('request_type', 'web');
        }

        return $next($request);
    }
}
