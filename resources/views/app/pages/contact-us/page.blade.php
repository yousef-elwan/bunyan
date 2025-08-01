@extends('app.layouts.default')

@php($metaData = $pages_meta['contact'])
@section('og_title', $metaData['title'])
@section('twitter_title', $metaData['title'])
@section('description', $metaData['description'])
@section('og_description', $metaData['description'])
@section('twitter_description', $metaData['description'])
@section('keywords', $metaData['keywords'])

@push('css_or_js')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/breadcrumb.css') }}">
    <link rel="stylesheet" href="{{ asset('website/contact/css/style.css') }}">

    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        Object.assign(window.AppConfig.routes, {
            'contact-us': "{{ route('api.contact-us.store') }}",
        });
    </script>
@endpush

@section('content')
    <section class="page-header-banner contact-header-banner"
        style="background-image: url('{{ Storage::disk('asset')->url('/images/page-title/page-title-4.jpg') }}');">
        <div class="container-v3">
            <h1>{{ __('app/contact.contact') }}</h1>
            <p><a href="{{ route('home') }}">{{ __('app/contact.pages') }}</a> / {{ __('app/contact.contact') }}</p>
        </div>
    </section>
    <section class="contact-page-content section-padding-v3">
        <div class="container-v3">
            <div class="contact-layout-grid">
                <div class="contact-info-column card-v3" data-aos="fade-right">
                    <h2 class="contact-section-title">{{ __('app/contact.info') }}</h2>
                    <p class="contact-subtitle">{{ __('app/contact.sub_title') }}</p>

                    @isset($web_config['company_address'])
                        <address>
                            <strong>{{ __('app/contact.company_name') }}</strong><br>
                            {{ $web_config['company_address'] }}
                        </address>
                    @endisset
                    <ul class="contact-details-list">
                        @isset($web_config['company_phone1'])
                            <li>
                                <i class="fas fa-phone fa-fw"></i>
                                <a href="tel:{{ $web_config['company_phone1'] }}" aria-label="{{ __('app/contact.phone') }}">
                                    {{ $web_config['company_phone1'] }}
                                </a>
                            </li>
                        @endisset
                        @isset($web_config['company_email'])
                            <li>
                                <i class="fas fa-envelope fa-fw"></i>
                                <a href="mailto:{{ $web_config['company_email'] }}"
                                    aria-label="{{ __('app/contact.email') }}">
                                    {{ $web_config['company_email'] }}
                                </a>
                            </li>
                        @endisset
                        <li><i class="fas fa-clock fa-fw"></i>
                            <span>{{ __('app/contact.open_time') }}: <br>{{ __('app/contact.working_hours') }}</span>
                        </li>
                    </ul>
                    <h3 class="contact-follow-title">{{ __('app/contact.follow') }}</h3>
                    <div class="contact-social-icons">
                        @isset($web_config['facebook_link'])
                            <a href="{{ $web_config['facebook_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endisset
                        @isset($web_config['instagram_link'])
                            <a href="{{ $web_config['instagram_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endisset
                        @isset($web_config['youtube_link'])
                            <a href="{{ $web_config['youtube_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="youtube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endisset
                    </div>
                </div>

                <div class="contact-form-column card-v3" data-aos="fade-left">
                    <h2 class="contact-section-title">{{ __('app/contact.direct_message') }}</h2>
                    <p class="contact-subtitle">{{ __('app/contact.fill_form') }}</p>
                    <form id="contactForm" class="main-contact-form">
                        <div class="form-row">

                            <div class="form-group-v3">
                                <label for="contact-name">{{ __('app/contact.name') }}<span
                                        class="required-asterisk">*</span></label>
                                <input type="text" id="contact-name" name="name" required
                                    placeholder="{{ __('app/contact.name_placeholder') }}" aria-required="true">
                            </div>
                            <div class="form-group-v3">
                                <label for="contact-email">{{ __('app/contact.email') }}<span
                                        class="required-asterisk">*</span></label>
                                <input type="email" id="contact-email" name="email" required
                                    placeholder="{{ __('app/contact.email_placeholder') }}" aria-required="true">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group-v3">
                                <label for="contact-phone">{{ __('app/contact.phone_optional') }}</label>
                                <div class="phone-input-group">
                                    <input type="tel" id="contact-phone" name="mobile">
                                </div>
                                <span class="invalid-feedback" data-field="mobile"></span>
                            </div>
                            <div class="form-group-v3">
                                <label for="contact-subject">{{ __('app/contact.subject') }}<span
                                        class="required-asterisk">*</span></label>
                                <input type="text" id="contact-subject" name="subject" required
                                    placeholder="{{ __('app/contact.subject_placeholder') }}" aria-required="true">
                            </div>
                        </div>
                        <div class="form-group-v3">
                            <label for="contact-message">{{ __('app/contact.your_message') }}<span
                                    class="required-asterisk">*</span></label>
                            <textarea id="contact-message" name="message" rows="6" required
                                placeholder="{{ __('app/contact.message_placeholder') }}" aria-required="true"></textarea>
                        </div>
                        <div class="form-submit-group">
                            <button type="submit" class="btn-v3 btn-v3-primary">{{ __('app/contact.send') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    @vite(['resources/js/alpine/app/contact/main.js'])
@endpush
