<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SetLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $locale = request()->segment(1);
        // if (!in_array($locale, config('app.locales'))) {
        //     abort(404);
        // }
        $acceptLang = $request->header('Accept-Language');
        $availableLocales = config('app.locales', []);
        $locale = null;

        if ($acceptLang) {
            $acceptedLocales = explode(',', $acceptLang);
            foreach ($acceptedLocales as $acceptedLocale) {
                $acceptedLocale = strtolower(trim(explode(';', $acceptedLocale)[0]));
                if (array_key_exists($acceptedLocale, $availableLocales)) {
                    $locale = $acceptedLocale;
                    break;
                }
            }
        }

        if (!$locale) {
            $locale = session('locale', app()->getLocale());
        }

        Session::put('locale', $locale);
        App::setLocale(Session::get('locale'));
        return $next($request);
    }
}
