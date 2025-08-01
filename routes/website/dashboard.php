<?php

use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ChatController;
use App\Http\Controllers\Dashboard\ConditionController;
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Web\AuthController;

Route::get('/testSendMessage', [ChatController::class, 'testSendMessage']);


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

            // profile routes
            Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

            Route::post('/update-info', [AuthController::class, 'updateInfo'])->name('auth.updateInfo');

            Route::post('/updatePassword', [AuthController::class, 'updatePassword'])->name('auth.updatePassword');

            Route::post('/updateAvatar', [AuthController::class, 'updateAvatar'])->name('auth.updateAvatar');

            Route::post('/updateNotifications', [AuthController::class, 'updateNotifications'])->name('auth.updateNotifications');

            Route::get('/chat', [ChatController::class, 'index'])->name('chat');
            // chatting routes

            Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
                Route::get('/conversations', [ChatController::class, 'fetchConversations'])->name('conversations.fetch');
                Route::get('/conversations/topics/{topic}/messages', [ChatController::class, 'show'])->name('messages.show');
                Route::post('/conversations/topics/{topic}/messages', [ChatController::class, 'store'])->name('messages.store');
                Route::post('/conversations/topics/{topic}/read', [ChatController::class, 'markAsRead'])->name('messages.read');
                Route::delete('/conversations/topics/{topic}/{message}', [ChatController::class, 'markAsRead'])->name('messages.delete');

                // Topics routes
                Route::get('/conversations/{conversation}/topics', [ChatController::class, 'fetchTopics'])->name('topics.fetch');
                Route::post('/conversations/{conversation}/topics', [ChatController::class, 'createTopic'])->name('topics.store');

                Route::post('topics/{topic}/reopen', [ChatController::class, 'reopenTopic'])->name('topics.reopen');
                Route::post('topics/{topic}/close', [ChatController::class, 'closeTopic'])->name('topics.close');
                Route::delete('messages/bulk-delete', [ChatController::class, 'bulkDeleteMessages'])->name('messages.bulk-delete');


                // Search
                Route::get('/conversations/search', [ChatController::class, 'search'])->name('conversations.search');
            });

            // notifications routes
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');


            // Properties routes
            Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/search', [UserController::class, 'search'])->name('search');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::get('/{user}', [UserController::class, 'edit'])->name('edit');
                Route::get('/{user}', [UserController::class, 'show'])->name('show');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                Route::post('/', [UserController::class, 'store'])->name('store');
            });

            // Properties routes
            Route::group(['prefix' => 'properties', 'as' => 'properties.'], function () {

                // open properties list pages
                Route::get('/', [PropertyController::class, 'index'])->name('index');

                // search handler
                Route::get('/search', [PropertyController::class, 'search'])->name('search');

                // open create page
                Route::get('/create', [PropertyController::class, 'create'])
                    ->name('create');

                // handel create 
                Route::post('/', [PropertyController::class, 'store'])
                    ->name('store');

                // handel upload images
                Route::post('/{property}/images', [PropertyController::class, 'uploadImages'])
                    ->name('images.store');

                // open edit update
                Route::get('/{property}', [PropertyController::class, 'edit'])
                    ->name('edit');

                // handel update 
                Route::put('/{property}', [PropertyController::class, 'update'])
                    ->name('update');

                // handel delete 
                Route::delete('/{property}', [PropertyController::class, 'destroy'])
                    ->name('destroy');
            });

            // reports routes
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [ReportController::class, 'index'])->name('index');
                Route::get('/search', [ReportController::class, 'search'])->name('search');
                Route::patch('/{report}/update-status', [ReportController::class, 'updateStatus'])->name('update_status');
            });

            //Category routes
            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::get('/', [CategoryController::class, 'index'])->name('index');
                Route::get('/search', [CategoryController::class, 'search'])->name('search');
                Route::get('/create', [CategoryController::class, 'create'])->name('create');
                Route::get('/{category}', [CategoryController::class, 'edit'])->name('edit');
                Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
                Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
                Route::post('/', [CategoryController::class, 'store'])->name('store');
            });

            //Floors routes
            Route::group(['prefix' => 'floor', 'as' => 'floor.'], function () {
                Route::get('/', [FloorController::class, 'index'])->name('index');
                Route::get('/search', [FloorController::class, 'search'])->name('search');
                Route::get('/create', [FloorController::class, 'create'])->name('create');
                Route::get('/{floor}', [FloorController::class, 'edit'])->name('edit');
                Route::put('/{floor}', [FloorController::class, 'update'])->name('update');
                Route::delete('/{floor}', [FloorController::class, 'destroy'])->name('destroy');
                Route::post('/', [FloorController::class, 'store'])->name('store');
            });

            // Orientations routes
            Route::group(['prefix' => 'orientation', 'as' => 'orientation.'], function () {
                Route::get('/', [OrientationController::class, 'index'])->name('index');
                Route::get('/search', [OrientationController::class, 'search'])->name('search');
                Route::get('/create', [OrientationController::class, 'create'])->name('create');
                Route::get('/{orientation}', [OrientationController::class, 'edit'])->name('edit');
                Route::put('/{orientation}', [OrientationController::class, 'update'])->name('update');
                Route::delete('/{orientation}', [OrientationController::class, 'destroy'])->name('destroy');
                Route::post('/', [OrientationController::class, 'store'])->name('store');
            });

            // Types routes
            Route::group(['prefix' => 'type', 'as' => 'type.'], function () {
                Route::get('/', [TypeController::class, 'index'])->name('index');
                Route::get('/search', [TypeController::class, 'search'])->name('search');
                Route::get('/create', [TypeController::class, 'create'])->name('create');
                Route::get('/{type}', [TypeController::class, 'edit'])->name('edit');
                Route::put('/{type}', [TypeController::class, 'update'])->name('update');
                Route::delete('/{type}', [TypeController::class, 'destroy'])->name('destroy');
                Route::post('/', [TypeController::class, 'store'])->name('store');
            });

            // Conditions routes
            Route::get('condition-create', [ConditionController::class, 'create'])->name('condition.create');
            Route::get('condition-search',   [ConditionController::class, 'search'])->name('condition.search');
            Route::group(['prefix' => 'condition', 'as' => 'condition.'], function () {
                Route::get('/', [ConditionController::class, 'index'])->name('index');
                Route::get('/{condition}', [ConditionController::class, 'edit'])->name('edit');
                Route::put('/{condition}', [ConditionController::class, 'update'])->name('update');
                Route::delete('/{condition}', [ConditionController::class, 'destroy'])->name('destroy');
                Route::post('/', [ConditionController::class, 'store'])->name('store');
            });

            Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        });
    });
