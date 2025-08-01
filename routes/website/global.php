<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\PagesController;
use App\Http\Controllers\Web\UserController;
use App\Models\Property\Property;

// Route for handling language switching logic


Route::get('language/set/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->whereIn('locale', array_keys(config('app.locales')));

Route::get('/login', [AuthController::class, 'unauthenticated'])->name('login');

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('app.locales')))
    ->middleware(['localeFromUrl', 'setWebConfig'])
    ->group(function () {

        Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {

            Route::get('/login', [PagesController::class, 'login'])
                ->name('login');

            Route::post('/login', [AuthController::class, 'login'])
                ->name('login');

            Route::get('/register', [PagesController::class, 'register'])
                ->name('register');

            Route::post('/register', [AuthController::class, 'register'])
                ->name('register');

            // Route::post('/logout', [AuthController::class, 'logout'])
            //     ->name('logout');

            Route::middleware(['auth:sanctum'])->group(function () {

                Route::post('/logout', [AuthController::class, 'logout'])
                    ->name('logout');

                Route::post('/updatePassword', [AuthController::class, 'updatePassword'])
                    ->name('updatePassword');

                Route::post('/update-info', [AuthController::class, 'updateInfo'])
                    ->name('updateInfo');
            });
        });
    });

Route::get('/', function () {
    $locale = session('locale', config('app.fallback_locale'));
    $supportedLocales = array_keys(config('app.locales'));
    if (!in_array($locale, $supportedLocales)) {
        $locale = config('app.fallback_locale');
    }
    return redirect()->route('home', ['locale' => $locale]);
});

Route::get('/test', function () {
    $properties = Property::with('amenities')->get();

    foreach ($properties as $property) {
        // $currentAmenities = $property->amenities()->pluck('amenities.id')->toArray();
        // $property->amenities()->sync($currentAmenities);
        $property->updateCachedAmenities();
    }
    return 'Sync completed for all properties.';
});
