<?php

use App\Models\Lang;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

if (!function_exists('translate')) {

    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string|array|null
     */
    function translate($key = null, $replace = [], $locale = null)
    {
        if (is_null($locale)) {
            $locale = getDefaultLanguage();
        }
        // return $key;
        return trans(
            key: $key,
            replace: $replace,
            locale: $locale
        );
    }
}


if (!function_exists('getDefaultLanguage')) {
    function getDefaultLanguage(): string
    {
        if (strpos(url()->current(), '/api')) {
            $lang = App::getLocale();
        } elseif (session()->has('locale')) {
            $lang = session('locale');
        } else {
            // $data = getWebConfig('language');
            // dd($data);
            $locale = 'en';
            $direction = 'ltr';
            $data =  Lang::where('id', getWebConfig('system_default_lang'))->first();
            $locale = $data->locale;
            $direction = $data->direction;

            session()->put('local', $locale);
            Session::put('direction', $direction);
            $lang = $locale;
        }
        return $lang;
    }
}


if (!function_exists('getLocaleDirection')) {
    function getLocaleDirection($locale = null)
    {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }
        $rtlLocales = ['ar', 'he', 'fa', 'ur']; // Add other RTL locales if needed
        return in_array($locale, $rtlLocales) ? 'rtl' : 'ltr';
    }
}
