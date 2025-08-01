<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class UtilsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        foreach ([app_path('Utils')] as $utilsPath) {
            $utilsFiles = File::files($utilsPath);
            foreach ($utilsFiles as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                require_once $utilsPath . DIRECTORY_SEPARATOR . $filename . '.php';
            }
        }
    }
}
