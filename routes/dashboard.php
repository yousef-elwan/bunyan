<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ChatController;
use App\Http\Controllers\Dashboard\ConditionController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\FloorController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\OrientationController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\PropertyController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\TypeController;
use App\Http\Controllers\Dashboard\UserController;


Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('app.locales')))
    ->middleware(['localeFromUrl', 'setWebConfig'])
    ->group(function () {

        // dashboard routes
        Route::group([
            'prefix' => 'dashboard',
            'as' => 'dashboard.',
            'middleware' => ['auth:sanctum']
        ], function () {

            // dashboard routes
            Route::get('/', [DashboardController::class, 'home'])->name('home');

            // chatting routes
            Route::middleware('auth')->prefix('chat')->name('chat')->group(function () {
                Route::get('/chat', [ChatController::class, 'index']);
            });

            // notifications routes
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

            // profile
            Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

            // Properties routes
            Route::group(['prefix' => 'properties', 'as' => 'properties'], function () {

                // open properties list pages
                Route::get('/', [PropertyController::class, 'index'])->name('.index');

                Route::get('/blacklist', [PropertyController::class, 'blacklist'])->name('.blacklist');
                Route::get('/favorite', [PropertyController::class, 'favorite'])->name('.favorite');

                Route::middleware(['role:user'])->group(function () {

                    // open create page
                    Route::get('/create', [PropertyController::class, 'create'])
                        ->name('.create');

                    // open edit update
                    Route::get('/{property}', [PropertyController::class, 'edit'])
                        ->name('.edit');
                });
            });

            //Role
            Route::middleware(['role:admin'])->group(function () {

                // reports routes
                Route::prefix('reports')->name('reports')->group(function () {
                    Route::get('/', [ReportController::class, 'index'])->name('.index');
                });
                // users routes
                Route::group(['prefix' => 'users', 'as' => 'users'], function () {
                    Route::get('/', [UserController::class, 'index'])->name('.index');
                });

                //Category routes
                Route::group(['prefix' => 'category', 'as' => 'category'], function () {
                    Route::get('/', [CategoryController::class, 'index'])->name('.index');
                    Route::get('/create', [CategoryController::class, 'create'])->name('.create');
                    Route::get('/{category}', [CategoryController::class, 'edit'])->name('.edit');
                });

                //Floors routes
                Route::group(['prefix' => 'floor', 'as' => 'floor'], function () {
                    Route::get('/', [FloorController::class, 'index'])->name('.index');
                    Route::get('/create', [FloorController::class, 'create'])->name('.create');
                    Route::get('/{floor}', [FloorController::class, 'edit'])->name('.edit');
                });

                // Orientations routes
                Route::group(['prefix' => 'orientation', 'as' => 'orientation'], function () {
                    Route::get('/', [OrientationController::class, 'index'])->name('.index');
                    Route::get('/create', [OrientationController::class, 'create'])->name('.create');
                    Route::get('/{orientation}', [OrientationController::class, 'edit'])->name('.edit');
                });

                // Types routes
                Route::group(['prefix' => 'type', 'as' => 'type'], function () {
                    Route::get('/', [TypeController::class, 'index'])->name('.index');
                    Route::get('/create', [TypeController::class, 'create'])->name('.create');
                    Route::get('/{type}', [TypeController::class, 'edit'])->name('.edit');
                });

                // Conditions routes
                Route::group(['prefix' => 'condition', 'as' => 'condition'], function () {
                    Route::get('/', [ConditionController::class, 'index'])->name('.index');
                    Route::get('/create', [ConditionController::class, 'create'])->name('.create');
                    Route::get('/{condition}', [ConditionController::class, 'edit'])->name('.edit');
                });

                // subscriptions routes
                Route::group(['prefix' => 'subscriptions', 'as' => 'subscriptions'], function () {
                    Route::get('/', [SubscriptionController::class, 'index'])->name('.index');
                });
            });
        });
    });
