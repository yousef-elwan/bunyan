<?php

use App\Models\User;
use Illuminate\Http\Request;

if (!function_exists('formatDescription')) {
    function formatDescription($description, $maxLength = 100)
    {
        // Check if the description length exceeds the maximum length
        if (strlen($description) > $maxLength) {
            // Truncate the description and add ellipsis
            return substr($description, 0, $maxLength) . '...';
        }
        // Return the original description if it's within the limit
        return $description;
    }
}
if (!function_exists('getAuthUser')) {

    function getAuthUser($request = null)
    {

        if (is_null($request)) {
            $request = request();
        }

        $user = null;
        //  for web
        if (auth('web')->check()) {
            $user = auth('web')->user();
        }
        
        if ($user == null) {
            $user = 'offline';
        }
        return $user;
    }
}

if (!function_exists('wasProtectedRoute')) {
    function wasProtectedRoute($url)
    {
        try {
            $path = parse_url($url, PHP_URL_PATH);

            // Get the route from the path
            $route = app('router')->getRoutes()->match(Request::create($path));

            // Check if it has 'auth' or 'auth:sanctum' middleware
            $middlewares = $route->gatherMiddleware();

            return collect($middlewares)->contains(function ($middleware) {
                return $middleware === 'auth' || $middleware === 'auth:sanctum';
            });
        } catch (\Exception $e) {
            // In case route not found or parsing failed, assume public
            return false;
        }
    }
}

// if (!function_exists('extractYouTubeVideoId')) {

//     function extractYouTubeVideoId($url)
//     {
//         if (!filter_var($url, FILTER_VALIDATE_URL)) {
//             return null;
//         }

//         $host = parse_url($url, PHP_URL_HOST);
//         $path = parse_url($url, PHP_URL_PATH);
//         $query = parse_url($url, PHP_URL_QUERY);

//         // 1. Watch URLs: youtube.com/watch?v=VIDEO_ID
//         if (strpos($url, 'watch') !== false && $query) {
//             parse_str($query, $queryParams);
//             if (!empty($queryParams['v'])) {
//                 return $queryParams['v'];
//             }
//         }

//         // 2. Short URL: youtu.be/VIDEO_ID
//         if (strpos($host, 'youtu.be') !== false) {
//             return ltrim($path, '/');
//         }

//         // 3. Embed URL or /v/: youtube.com/embed/VIDEO_ID or youtube.com/v/VIDEO_ID
//         if (preg_match('/\/(embed|v)\/([^\/\?]+)/', $path, $matches)) {
//             return $matches[2];
//         }

//         // 4. Shorts URL: youtube.com/shorts/VIDEO_ID
//         if (preg_match('/\/shorts\/([^\/\?]+)/', $path, $matches)) {
//             return $matches[1];
//         }

//         // 5. Fallback regex for rare cases
//         if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|embed|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches)) {
//             return $matches[1];
//         }

//         return null;
//     }
// }

if (!function_exists('extractYouTubeVideoId')) {

    function extractYouTubeVideoId($url)
    {
        // Unified regex pattern covering all cases
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:watch|embed|live|v|shorts)/|(?:watch\?.*v=)|(?:embed|live|v|shorts)/)|youtu\.be/)([^"&?/ ]{11})%i';

        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

if (!function_exists('objectToBase64')) {
    function objectToBase64($object): string
    {
        $serialized = json_encode($object);

        // Encode the serialized string into base64
        return base64_encode($serialized);
    }
}
if (!function_exists('base64ToObject')) {
    function base64ToObject(string $base64)
    {
        return json_decode(base64_decode($base64));
    }
}
