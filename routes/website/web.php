<?php

use App\Http\Controllers\Api\CustomAttributeController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PagesController;

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('app.locales')))
    ->middleware(['localeFromUrl', 'setWebConfig'])
    ->group(function () {


        // open home page
        Route::get('/', [PagesController::class, 'home'])
            ->name('home');

        // open search page
        Route::get('/search', [PagesController::class, 'search'])
            ->name('search');

        // open terms of use
        Route::get('/terms-of-use', [PagesController::class, 'termsOfUse'])
            ->name('terms-of-use');

        // open contact us
        Route::get('/contact-us', [PagesController::class, 'contactUs'])
            ->name('contact-us');

        // handle contact us
        Route::post('/contact-us', [PagesController::class, 'sendMessage'])
            ->name('sendMessage');

        // open privacy policy
        Route::get('/privacy-policy', [PagesController::class, 'privacyPolicy'])
            ->name('privacy-policy');

        // open terms of use
        Route::get('/terms-of-use', [PagesController::class, 'termsOfUse'])
            ->name('terms-of-use');

        // open terms of service
        Route::get('/terms-of-service', [PagesController::class, 'termsOfService'])
            ->name('terms-of-service');

        // get custom attributes
        Route::get('/customAttribute', [CustomAttributeController::class, 'index'])->name('customAttribute');

        // properties routes
        Route::group(['prefix' => 'properties', 'as' => 'properties.'], function () {
            // handel search 
            Route::get('/', [PropertyController::class, 'index'])
                ->name('search');

            Route::prefix('{property}')
                ->group(function () {
                    // open details page
                    Route::get('/', [PagesController::class, 'details'])
                        ->name('details');
                    // handle send report
                    Route::post('/submit-report', [PropertyController::class, 'submitReport'])
                        ->name('submit-report');
                    // handle contact owner
                    Route::post('/contact-agent', [PropertyController::class, 'contactAgent'])
                        ->name('contact-agent');
                    // protected routes
                    Route::middleware(['auth:sanctum'])->group(function () {
                        // handle toggle favorite
                        Route::post('/toggle-favorite', [PropertyController::class, 'toggleFavorite'])
                            ->name('toggle-favorite');
                        // handle toggle blacklist
                        Route::post('/toggle-blacklist', [PropertyController::class, 'toggleBlacklist'])
                            ->name('toggle-blacklist');
                    });
                });
        });


        Route::group(['prefix' => 'newsletter', 'as' => 'newsletter.'], function () {
            Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
            Route::get('/confirm/{token}', [NewsletterController::class, 'confirm'])->name('confirm');
            Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
        });
    });
