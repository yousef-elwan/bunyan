<?php

namespace App\Http\Middleware; // Adjust this to your application's namespace

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromUrl
{
    /**
     * Handle an incoming request.
     *
     * This middleware sets the application locale based on the 'locale' parameter
     * found in the URL. It validates the locale against a configured list of
     * supported locales and falls back to the application's default fallback locale
     * if the provided locale is invalid or not supported.
     *
     * It also sets the default locale for URL generation and stores the chosen
     * locale in the session for potential future use or persistence across requests
     * where the locale might not be in the URL (though this should be minimized
     * in a URL-driven localization strategy).
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Get the locale from the route parameter (e.g., '{locale}' in your route definition)
        $urlLocale = $request->route('locale');

        // 2. Get the list of supported locales from your configuration
        // Ensure you have 'locales' defined in config/app.php or a dedicated config file
        // Example: 'locales' => ['en' => 'English', 'ar' => 'العربية', 'fr' => 'Français'],
        $supportedLocales = array_keys(config('app.locales', []));

        // 3. Determine the locale to set
        $localeToSet = null;

        if ($urlLocale && in_array($urlLocale, $supportedLocales)) {
            // If a valid locale is present in the URL, use it.
            $localeToSet = $urlLocale;
        } else {
            // If no locale in URL, or it's invalid, you might want to:
            // a) Redirect to a URL with a default locale (handled by root route usually)
            // b) Fallback to session locale (if you want to remember user's last choice)
            // c) Fallback to browser's Accept-Language header (more complex, less reliable for SEO)
            // d) Fallback to the application's default fallback_locale

            // For this middleware, which is expected to run on locale-prefixed routes,
            // an invalid or missing $urlLocale would typically mean the route itself
            // didn't match (or a misconfiguration). However, as a safeguard:
            $sessionLocale = Session::get('locale');
            if ($sessionLocale && in_array($sessionLocale, $supportedLocales)) {
                $localeToSet = $sessionLocale;
            } else {
                // As a final fallback, use the application's configured fallback locale
                $localeToSet = config('app.fallback_locale', 'en'); // Default to 'en' if not configured
            }

            // If the URL locale was missing or invalid, and we're falling back,
            // you might consider if a redirect to a properly-prefixed URL is needed.
            // However, this middleware's primary job is to set the locale for the *current* request
            // based on an *existing* URL prefix. Redirection logic is usually better handled
            // at the routing level (e.g., for the root '/' path).
        }


        // 4. Set the application's locale
        App::setLocale($localeToSet);

        // 5. Set the default locale for URL generation
        // This ensures that `route()` calls automatically include the current locale
        // if the route definition expects a 'locale' parameter.
        URL::defaults(['locale' => $localeToSet]);

        // 6. Store the locale in the session
        // This can be useful for remembering the user's preference or for
        // parts of the application that might not have the locale in the URL (e.g., API calls from JS).
        Session::put('locale', $localeToSet);

        // 7. Continue processing the request
        return $next($request);
    }
}
