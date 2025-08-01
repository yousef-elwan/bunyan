<?php

namespace App\Providers;

use Carbon\Translator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        $host = request()->getHost();
        $scheme = request()->getScheme();
        $port = request()->getPort();

        $basePath = parse_url(env('APP_URL'), PHP_URL_PATH);
        $dynamicAppUrl = "$scheme://$host";
        if (!in_array($port, [80, 443])) {
            $dynamicAppUrl .= ":$port";
        }
        $dynamicAppUrl .= $basePath;

        config([
            'app.url' => $dynamicAppUrl,
            'reverb.apps.apps.0.options.host' => $host,
            'reverb.apps.apps.0.options.port' => $port,
            'reverb.apps.apps.0.options.scheme' => $scheme,
            'reverb.apps.apps.0.options.useTLS' => $scheme === 'https',
        ]);


        if (env('USE_NEW_RESOURCES')) {

            // Override view path
            View::getFinder()->setPaths([
                base_path('resources_new/views'),
            ]);

            // Override lang path (for __(), trans(), etc.)
            App::singleton('translator', function ($app) {
                $loader = new FileLoader(new Filesystem, base_path('resources_new/lang'));
                $locale = $app['config']['app.locale'];

                return new Translator($loader, $locale);
            });
        }
    }
}
