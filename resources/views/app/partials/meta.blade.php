@php
    $metaDescription = strip_tags(str_replace('&nbsp;', ' ', $web_config['meta_description'] ?? ''));
@endphp
<!-- Mobile Specific Meta -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:site_name" content="@yield('site_name', $web_config['company_name'])">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:image" content="@yield('og_image', $web_config['company_logo'])">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="436">
<meta property="og:image:height" content="228">
<meta property="og:title" content="@yield('og_title', $web_config['company_name']))">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:description" content="@yield('og_description', $metaDescription)">
<meta property="twitter:card" content="@yield('twitter_card', $web_config['company_logo'] ?? '')">
<meta property="twitter:title" content="@yield('twitter_title', $web_config['company_name'] ?? '')">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:description" content="@yield('twitter_description', $metaDescription)">
<meta name="keywords" content="@yield('keywords')">
<meta name="author" content="@yield('author', $web_config['company_name'])">
<meta name="description" content="@yield('description', $metaDescription)">
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<base href="{{ config('app.url') }}">
<meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
