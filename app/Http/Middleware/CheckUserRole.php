<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  // We can pass one or more roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {

        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => __('messages.errors.unauthenticated') ?? 'Unauthenticated.'
                ], 401);
            }

            return redirect('home');
        }


        $user = Auth::user();

        // Check if the user has one of the required roles
        foreach ($roles as $role) {
            if ($user->type === $role) { // Assuming your user model has a 'role' attribute
                return $next($request);
            }
        }

        // Check if the user has one of the required roles
        // if ((int)$user->is_admin >= $minRole) { // Assuming your user model has a 'type' attribute
        //     return $next($request);
        // }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => __('messages.errors.403.title') ?? 'Unauthorized.'
            ], 403);
        }


        abort(403, __('messages.errors.403.title') ?? 'Unauthorized.');
    }
}
