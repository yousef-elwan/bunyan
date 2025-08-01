<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            $locale = Lang::getLocale();

            $encryptedEmail = Crypt::encryptString($user->getEmailForPasswordReset());
            
            return route('auth.password.reset', [
                'locale' => $locale,
                'token' => $token,
                'email' => $encryptedEmail,
            ]);
        });
    }
}
