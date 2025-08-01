<?php

namespace App\Http\Controllers\Web; // Or your specific namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{

    public function switch(Request $request, $newLocale)
    {
        $supportedLocales = array_keys(config('app.locales', []));

        if (!in_array($newLocale, $supportedLocales)) {
            $newLocale = config('app.fallback_locale', 'en');
        }

        Session::put('locale', $newLocale);

        $previousUrl = url()->previous(); // full previous URL

        // Parse the previous URL path (relative to base)
        $baseUrl = url('/');
        $relativePath = str_replace($baseUrl, '', $previousUrl); // removes domain part
        $segments = explode('/', ltrim($relativePath, '/'));

        if (isset($segments[0]) && in_array($segments[0], $supportedLocales)) {
            $segments[0] = $newLocale; // replace the old locale with the new one
        } else {
            array_unshift($segments, $newLocale); // no locale? Add it
        }

        $newPath = implode('/', $segments);
        $newUrl = url($newPath);


        if (request()->has('hash')) {
            $newUrl .= request()->input('hash');
        }

        // return errorResponse($newUrl);

        return Redirect::to($newUrl);
    }
}
