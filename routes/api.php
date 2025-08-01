<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

use App\Http\Controllers\Api\{
    CustomAttributeController,
    PropertyController,
    PropertyStatusController,
    ApiSubscriptionController,
    ContactUsController,
    AuthController,
};

use App\Http\Controllers\Web\{
    AuthController as WebAuthController,
};

use App\Http\Controllers\Dashboard\{
    CategoryController,
    ChatController,
    ConditionController,
    FloorController,
    OrientationController,
    PropertyController  as DashboardPropertyController,
    ReportController,
    TypeController,
    UserController  as DashboardUserController,
};


Route::middleware(['setWebConfig', 'lang', 'optional.sanctum'])->name('api.')->group(function () {

    // auth routes
    Route::group(['prefix' => 'auth', 'as' => 'auth'], function () {


        Route::post('/login', [AuthController::class, 'login'])->name('.login');
        Route::post('/register', [AuthController::class, 'register'])->name('.register');


        // Route::group(['prefix' => 'web', 'as' => '.web', 'middleware' => ['web']], function () {
        //     Route::post('/login', [WebAuthController::class, 'login'])->name('.login');
        //     Route::post('/register', [WebAuthController::class, 'register'])->name('.register');
        // });

        Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('.password.email');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('.password.update');


        Route::middleware(['auth:sanctum'])->group(function () {

            Route::post('/update-info', [AuthController::class, 'updateInfo'])->name('.updateInfo');

            Route::post('/updatePassword', [AuthController::class, 'updatePassword'])->name('.updatePassword');

            Route::post('/updateAvatar', [AuthController::class, 'updateAvatar'])->name('.updateAvatar');

            Route::post('/updateNotifications', [AuthController::class, 'updateNotifications'])->name('.updateNotifications');

            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('.logout');

            Route::delete('/avatar', [AuthController::class, 'deleteAvatar'])->name('.deleteAvatar');


            Route::group(['prefix' => 'web', 'as' => '.web', 'middleware' => ['web']], function () {
                Route::post('/logout', [WebAuthController::class, 'logout'])
                    ->name('.logout');
            });


            Route::post('/updatePassword', [AuthController::class, 'updatePassword'])
                ->name('.updatePassword');

            Route::post('/update-info', [AuthController::class, 'updateInfo'])
                ->name('.updateInfo');
        });
    });

    // properties routes
    Route::group(['prefix' => 'properties', 'as' => 'properties'], function () {

        Route::get('/', [PropertyController::class, 'index']);

        // protected routes
        Route::middleware(['auth:sanctum'])->group(function () {

            Route::group(['prefix' => '{property}'], function () {
                // handle toggle favorite
                Route::post('/toggle-favorite', [PropertyController::class, 'toggleFavorite'])
                    ->name('.toggle-favorite');

                // handle toggle blacklist
                Route::post('/toggle-blacklist', [PropertyController::class, 'toggleBlacklist'])
                    ->name('.toggle-blacklist');

                // handle send report
                Route::post('/submit-report', [PropertyController::class, 'submitReport'])
                    ->name('.submit-report');

                // handle contact owner
                Route::post('/contact-agent', [PropertyController::class, 'contactAgent'])
                    ->name('.contact-agent');
            });
        });
    });

    // properties statuses routes
    Route::group(['prefix' => 'properties-statuses', 'as' => 'properties-statuses'], function () {
        Route::get('/', [PropertyStatusController::class, 'index']);
    });

    // contact us routes
    Route::group(['prefix' => 'contact-us', 'as' => 'contact-us'], function () {
        Route::post('/', [ContactUsController::class, 'store'])->name('.store');
    });

    // category routes
    Route::group(['prefix' => 'category', 'as' => 'category'], function () {
        Route::get('/', [CategoryController::class, 'search']);
    });

    // floor routes
    Route::group(['prefix' => 'floor', 'as' => 'floor'], function () {
        Route::get('/', [FloorController::class, 'search']);
    });

    // orientation routes
    Route::group(['prefix' => 'orientation', 'as' => 'orientation'], function () {
        Route::get('/', [OrientationController::class, 'search']);
    });

    // type routes
    Route::group(['prefix' => 'type', 'as' => 'type'], function () {
        Route::get('/', [TypeController::class, 'search']);
    });

    // Conditions routes
    Route::group(['prefix' => 'condition', 'as' => 'condition'], function () {
        Route::get('/',   [ConditionController::class, 'search']);
    });

    // custom attributes routes
    Route::group(['prefix' => 'customAttribute', 'as' => 'customAttribute'], function () {
        Route::get('/', [CustomAttributeController::class, 'index']);
    });



    // for dashboard
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // chatting routes
        Route::group(['prefix' => 'chat', 'as' => 'chat'], function () {

            // conversations routes
            Route::group(['as' => '.conversations'], function () {
                Route::get('/conversations', [ChatController::class, 'fetchConversations'])->name('.fetch');
                Route::get('/conversations/search', [ChatController::class, 'search'])->name('.search');
            });

            // messages routes
            Route::group(['as' => '.messages'], function () {
                Route::get('/conversations/topics/{topic}/messages', [ChatController::class, 'show'])->name('.show');
                Route::post('/conversations/topics/{topic}/messages', [ChatController::class, 'store'])->name('.store');
                Route::post('/conversations/topics/{topic}/read', [ChatController::class, 'markAsRead'])->name('.read');
                Route::delete('/conversations/topics/{topic}/{message}', [ChatController::class, 'markAsRead'])->name('.delete');
                Route::delete('messages/bulk-delete', [ChatController::class, 'bulkDeleteMessages'])->name('.bulk-delete');
            });

            // topics routes
            Route::group(['as' => '.topics'], function () {
                Route::get('/conversations/{conversation}/topics', [ChatController::class, 'fetchTopics'])->name('.fetch');
                Route::post('/conversations/{conversation}/topics', [ChatController::class, 'createTopic'])->name('.store');
                Route::post('topics/{topic}/reopen', [ChatController::class, 'reopenTopic'])->name('.reopen');
                Route::post('topics/{topic}/close', [ChatController::class, 'closeTopic'])->name('.close');
            });
        });

        // Properties routes
        Route::group(['prefix' => 'dashboard-properties', 'as' => 'dashboard-properties'], function () {
            // search handler
            Route::get('/', [DashboardPropertyController::class, 'search']);

            // handel create 
            Route::post('/', [DashboardPropertyController::class, 'store'])
                ->name('.store');

            // handel upload images
            Route::post('/{property}/images', [DashboardPropertyController::class, 'uploadImages'])
                ->name('.images.store');

            // handel update 
            Route::put('/{property}', [DashboardPropertyController::class, 'update'])
                ->name('.update');

            // handel delete 
            Route::delete('/{property}', [DashboardPropertyController::class, 'destroy'])
                ->name('.destroy');
        });


        Route::group(['middleware' => ['role:admin']], function () {

            // users routes
            Route::group(['prefix' => 'users', 'as' => 'users'], function () {
                Route::get('/', [DashboardUserController::class, 'search']);
                Route::delete('/{user}', [DashboardUserController::class, 'destroy'])->name('.destroy');
                Route::group(['prefix' => '{user}'], function () {
                    Route::get('', [UserController::class, 'show'])->name('.show');
                    Route::post('/blacklist', [UserController::class, 'blacklist'])->name('.blacklist');
                    Route::delete('/blacklist', [UserController::class, 'unblacklist'])->name('.unblacklist');
                    Route::put('/status', [UserController::class, 'updateStatus'])->name('.update_status');
                });
            });

            // subscriptions routes
            Route::group(['as' => 'subscriptions'], function () {
                Route::get('/users-search', [ApiSubscriptionController::class, 'searchUsers'])->name('.users.search');
                Route::get('/users/{user}/subscriptions', [ApiSubscriptionController::class, 'getSubscriptionHistory'])->name('.history');
                Route::post('/users/{user}/subscriptions', [ApiSubscriptionController::class, 'store'])->name('.store');
            });

            // reports routes
            Route::prefix('reports')->name('reports')->group(function () {
                Route::get('/', [ReportController::class, 'search']);
                Route::patch('/{report}/update-status', [ReportController::class, 'updateStatus'])->name('.update_status');
            });

            //Category routes
            Route::group(['prefix' => 'category', 'as' => 'category'], function () {
                Route::put('/{category}', [CategoryController::class, 'update'])->name('.update');
                Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('.destroy');
                Route::post('/', [CategoryController::class, 'store'])->name('.store');
            });

            //Floors routes
            Route::group(['prefix' => 'floor', 'as' => 'floor'], function () {
                Route::put('/{floor}', [FloorController::class, 'update'])->name('.update');
                Route::delete('/{floor}', [FloorController::class, 'destroy'])->name('.destroy');
                Route::post('/', [FloorController::class, 'store'])->name('.store');
            });

            // Orientations routes
            Route::group(['prefix' => 'orientation', 'as' => 'orientation'], function () {
                Route::put('/{orientation}', [OrientationController::class, 'update'])->name('.update');
                Route::delete('/{orientation}', [OrientationController::class, 'destroy'])->name('.destroy');
                Route::post('/', [OrientationController::class, 'store'])->name('.store');
            });

            // Types routes
            Route::group(['prefix' => 'type', 'as' => 'type'], function () {
                Route::put('/{type}', [TypeController::class, 'update'])->name('.update');
                Route::delete('/{type}', [TypeController::class, 'destroy'])->name('.destroy');
                Route::post('/', [TypeController::class, 'store'])->name('.store');
            });

            // Conditions routes
            Route::group(['prefix' => 'condition', 'as' => 'condition'], function () {
                Route::put('/{condition}', [ConditionController::class, 'update'])->name('.update');
                Route::delete('/{condition}', [ConditionController::class, 'destroy'])->name('.destroy');
                Route::post('/', [ConditionController::class, 'store'])->name('.store');
            });
        });
    });
});
