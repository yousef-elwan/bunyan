<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\LanguageController;

// Route for handling language switching logic


Route::get('language/set/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->whereIn('locale', array_keys(config('app.locales')));

Route::get('/login', [AuthController::class, 'unauthenticated'])->name('login');


Route::get('/', function () {
    $locale = session('locale', config('app.fallback_locale'));
    $supportedLocales = array_keys(config('app.locales'));
    if (!in_array($locale, $supportedLocales)) {
        $locale = config('app.fallback_locale');
    }
    return redirect()->route('home', ['locale' => $locale]);
});
