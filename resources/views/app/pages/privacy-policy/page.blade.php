@extends('app.layouts.default')

@php($metaData = $pages_meta['privacy_policy'])
@section('og_title', $metaData['title'])
@section('twitter_title', $metaData['title'])
@section('description', $metaData['description'])
@section('og_description', $metaData['description'])
@section('twitter_description', $metaData['description'])
@section('keywords', $metaData['keywords'])

@push('css_or_js')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/breadcrumb.css') }}">
    <link rel="stylesheet" href="{{ asset('website/privacyPolicy/css/style.css') }}">

    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
@endpush


@section('content')

    <section class="page-header-banner static-page-banner terms-banner-v3"
        style="background-image: url('{{ Storage::disk('asset')->url('images/PrivacyPolicy/PrivacyPolicy.jpg') }}');">
        <div class="container-v3 banner-text-content">
            <p class="banner-subtitle-v3">{{ __('app/privacy_policy.banner_subtitle') }}</p>
            <h1 class="banner-title-v3">{{ __('app/privacy_policy.banner_title') }}</h1>
        </div>
    </section>

    <section class="static-content-section section-padding-v3">
        <div class="container-v3 static-page-layout">

            <aside class="static-page-sidebar card-v3" data-aos="fade-left">
                <h3 class="sidebar-title">{{ __('app/privacy_policy.sidebar_legal_documents') }}</h3>
                <ul class="sidebar-nav-links">
                    <li>
                        <a href="{{ route('terms-of-use') }}">
                            {{ __('app/privacy_policy.sidebar_terms_of_use') }}
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms-of-service') }}">
                            {{ __('app/privacy_policy.sidebar_terms_of_service') }}
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact-us') }}">
                            {{ __('app/privacy_policy.sidebar_contact_us') }}
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </li>
                </ul>
                <hr class="sidebar-divider">
                <h4 class="sidebar-help-title">{{ __('app/privacy_policy.sidebar_need_help') }}</h4>
                <a href="{{ route('contact-us') }}" class="btn-v3 btn-v3-primary btn-block sidebar-cta-btn">
                    {{ __('app/privacy_policy.sidebar_help_center') }} <i class="fas fa-headset"></i>
                </a>
                @isset($web_config['company_email'])
                    <p class="sidebar-email-info">
                        {{ __('app/privacy_policy.sidebar_email_info') }} <br>
                        <a href="mailto:{{ $web_config['company_email'] }}"> {{ $web_config['company_email'] }}</a>
                    </p>
                @endisset
            </aside>

            <div class="static-page-main-content card-v3" data-aos="fade-right">
                <p class="content-category-label">{{ __('app/privacy_policy.content_category_label') }}</p>
                <p class="last-updated">
                    {{ __('app/privacy_policy.last_updated', ['date' => $legal['updated_at_formate'] ?? '']) }}</p>

                <article>

                    {!! $legal['content'] ?? '' !!}

                </article>
            </div>
        </div>
    </section>
@endsection

@push('script')
    @vite(['resources/js/alpine/app/privacy-policy/main.js'])
@endpush
