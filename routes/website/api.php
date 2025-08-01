<?php

use App\Http\Controllers\Api\ApiSubscriptionController;
use App\Http\Controllers\Api\CustomAttributeController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\PropertyStatusController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\FloorController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PagesController;
use Illuminate\Support\Facades\Route;


Route::group(['as' => 'api.', 'middleware' => ['setWebConfig', 'lang']], function () {
    Route::get('/customAttribute', [CustomAttributeController::class, 'index'])->name('customAttribute');
});

Route::middleware(['setWebConfig', 'lang'])->group(function () {

    Route::get('/api/properties', [PagesController::class, 'apiProperties'])->name('property.search');


    Route::get('/my-profile', [ProfileController::class, 'index'])
        ->middleware('auth:sanctum')->name('profile');

    Route::get('/api/search', [PropertyController::class, 'search'])
        ->name('api.search');

    // Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::group(['as' => 'api.', 'middleware' => ['setWebConfig', 'lang', 'auth:sanctum']], function () {
        Route::get('/users-search', [ApiSubscriptionController::class, 'searchUsers'])->name('subscriptions.users.search');
        Route::get('/users/{user}/subscriptions', [ApiSubscriptionController::class, 'getSubscriptionHistory'])->name('subscriptions.history');
        Route::post('/users/{user}/subscriptions', [ApiSubscriptionController::class, 'store'])->name('subscriptions.store');
    });
});

//floor
// Route::group(['prefix' => 'floor', 'as' => 'floor.'], function () {

//     Route::delete('/{floor}', [FloorController::class, 'distory'])
//         ->name('distory');

//     Route::post('/', [FloorController::class, 'store'])
//         ->name('store');

//     Route::put('/{floor}', [FloorController::class, 'update'])
//         ->name('update');
// })->middleware('web');
