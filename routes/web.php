<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PagesController;

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('app.locales')))
    ->middleware(['localeFromUrl', 'setWebConfig'])
    ->group(function () {

        // auth routes
        Route::group(['prefix' => 'auth', 'as' => 'auth'], function () {

            Route::get('/login', [PagesController::class, 'login'])->name('.login');
            // Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

            Route::get('/register', [PagesController::class, 'register'])->name('.register');
            // Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

            // Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

            Route::get('/reset-password/{token}/{email}', [AuthController::class, '.resetPasswordPage'])
                ->name('password.reset');

            Route::post('/login', [AuthController::class, 'login'])->name('.login.submit');

            Route::post('/register', [AuthController::class, 'register'])->name('.register.submit');
        });

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

        // open privacy policy
        Route::get('/privacy-policy', [PagesController::class, 'privacyPolicy'])
            ->name('privacy-policy');

        // open terms of use
        Route::get('/terms-of-use', [PagesController::class, 'termsOfUse'])
            ->name('terms-of-use');

        // open terms of service
        Route::get('/terms-of-service', [PagesController::class, 'termsOfService'])
            ->name('terms-of-service');

        // properties routes
        Route::group(['prefix' => 'properties', 'as' => 'properties.'], function () {
            Route::get('/{property}', [PagesController::class, 'details'])->name('details');
        });

        // newsletter routes
        Route::group(['prefix' => 'newsletter', 'as' => 'newsletter.'], function () {
            Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
            Route::get('/confirm/{token}', [NewsletterController::class, 'confirm'])->name('confirm');
            Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
        });
    });
