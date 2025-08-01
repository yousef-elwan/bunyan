@extends('app.layouts.default')


@php
    $metaDescription = strip_tags(Str::limit($property['content'] ?? __('app/properties.about_this_property'), 160));
    $metaTitle = $property['location'] ?? __('app/properties.about_this_property');
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@if (isset($property['images']) && !empty($property['images']))
    @section('og_image', asset($property['images'][0] ?? ''))
    @section('twitter_card', asset($property['images'][0] ?? ''))
@endif
@section('og_title', $metaTitle)
@section('twitter_title', $metaTitle)
@section('og_description', $metaDescription)
@section('twitter_description', $metaDescription)
@section('description', $metaDescription)

@push('css_or_js')
    {{-- External CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.21/css/intlTelInput.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link rel="stylesheet" href="{{ asset('website/details/css/style.css') }}">
@endpush

@section('content')

    <main class="property-details-page-v3">
        <section class="hero-swiper-section-v3">
            <div class="swiper hero-swiper">
                <div class="swiper-wrapper" id="heroSwiperWrapperV3"></div>
                <div class="swiper-pagination swiper-pagination-slider"></div>
                <div class="swiper-button-prev swiper-button-prev-slider"></div>
                <div class="swiper-button-next swiper-button-next-slider"></div>
                <div class="swiper-progress-indicator" id="swiperProgressV3"></div>
            </div>
            <div class="thumbnail-container-v3" id="thumbnailContainerV3"></div>
        </section>

        <div class="container-v3 property-main-content-v3">
            <section class="property-intro-v3">
                <div class="title-location-v3">
                    <p class="location-text-v3">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $property['location'] ?? __('app/properties.location_not_specified') }}
                    </p>
                </div>
                <div class="price-actions-v3">
                    <div class="price-tag-v3">
                        {{ $property['price_display'] }}
                    </div>
                    <div class="actions-bar-v3">

                        <button class="action-btn-v3 favorite-btn-v3 {{ $property['is_favorite'] ? 'active' : '' }}"
                            aria-label="{{ __('app/properties.add_to_favorites_label') }}"
                            data-tooltip="{{ __('app/properties.add_to_favorites_label') }}"
                            data-property-id="{{ $property['id'] }}">
                            <i class="far fa-heart"></i>
                        </button>

                        <button class="action-btn-v3 share-btn-trigger-v3"
                            aria-label="{{ __('app/properties.share_label') }}"
                            data-tooltip="{{ __('app/properties.share_label') }}" data-property-id="{{ $property['id'] }}">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        <button class="action-btn-v3 report-btn-trigger-v3 open-report-modal-btn allow-guest-report"
                            aria-label="{{ __('app/properties.report_label') }}"
                            data-tooltip="{{ __('app/properties.report_label') }}"
                            data-property-id="{{ $property['id'] }}">
                            <i class="fas fa-flag"></i>
                        </button>
                        <button
                            class="action-btn-v3 blacklist-btn-trigger-v3 {{ $property['is_blacklist'] ? 'active' : '' }}"
                            aria-label="{{ __('app/properties.blacklist_add_label') }}"
                            data-tooltip="{{ __('app/properties.blacklist_add_label') }}"
                            data-property-id="{{ $property['id'] }}">
                            <i class="fas fa-ban"></i></button>
                    </div>
                </div>
                <div class="property-meta-v3">
                    <span><i class="fas fa-eye"></i>
                        {{ $property['view_count'] ?? 0 }} {{ __('app/properties.views') }}
                    </span>
                    <span><i class="fas fa-bed"></i>
                        {{ $property['rooms_count'] ?? 0 }} {{ __('app/properties.rooms') }}
                    </span>
                    <span><i class="fas fa-ruler-combined"></i>
                        {{ $property['size'] ?? 0 }} {{ __('app/properties.sqm') }}
                    </span>
                    <span><i class="fas fa-calendar-alt"></i>
                        {{ __('app/properties.date_added') }}:
                        {{ $property['published_at_formatted'] ?? __('app/properties.not_specified') }}
                    </span>
                </div>
            </section>

            <div class="property-layout-v3">
                <div class="main-details-column-v3">
                    <section class="property-description-v3 card-v3">
                        <h2>{{ __('app/properties.about_this_property') }}</h2>
                        <div id="descriptionContentV3" class="description-text-v3 short-v3">
                            {!! $property['content'] ?? '<p>' . __('app/properties.no_description_available') . '</p>' !!}
                        </div>
                        <button id="seeMoreBtnV3" class="btn-v3 btn-v3-link">{{ __('app/properties.show_more') }}</button>
                    </section>

                    <section class="tab-section-v3 card-v3">
                        {{-- <div class="tab-buttons-v3">
                            <button class="tab-button-v3 active"
                                data-tab="details">{{ __('app/properties.details') }}</button>
                            @if (!empty($property['amenities']))
                                <button class="tab-button-v3"
                                    data-tab="amenities">{{ __('app/properties.amenities') }}</button>
                            @endif
                            @if (!empty($property['video_url']))
                                <button class="tab-button-v3" data-tab="video">{{ __('app/properties.video') }}</button>
                            @endif
                            @if (!empty($property['latitude']) && !empty($property['longitude']))
                                <button class="tab-button-v3" data-tab="map">{{ __('app/properties.location') }}</button>
                            @endif
                        </div> --}}
                        <div class="tabs-wrapper-v3" id="tabsWrapperV3">
                            <button class="tab-scroll-btn-v3 left" id="scrollTabsLeftBtn" aria-label="Scroll tabs left">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="tab-buttons-v3" id="tabButtonsContainerV3">
                                <button class="tab-button-v3 active"
                                    data-tab="details">{{ __('app/properties.details') }}</button>
                                @if (!empty($property['amenities']))
                                    <button class="tab-button-v3"
                                        data-tab="amenities">{{ __('app/properties.amenities') }}</button>
                                @endif
                                @if (!empty($property['video_url']))
                                    <button class="tab-button-v3"
                                        data-tab="video">{{ __('app/properties.video') }}</button>
                                @endif
                                @if (!empty($property['latitude']) && !empty($property['longitude']))
                                    <button class="tab-button-v3"
                                        data-tab="map">{{ __('app/properties.location') }}</button>
                                @endif
                                {{-- You can add more tabs here if needed --}}
                            </div>
                            <button class="tab-scroll-btn-v3 right" id="scrollTabsRightBtn" aria-label="Scroll tabs right">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <div class="tab-content-v3 active" id="detailsContentV3">
                            <h3>{{ __('app/properties.overview') }}</h3>
                            <ul id="propertyAttributesListV3" class="attributes-list-v3">
                                @if (isset($property['attribute']) && count($property['attribute']) > 0)
                                    <li><i class="fas fa-tag"></i><strong>{{ __('app/properties.category') }}:</strong>
                                        <span>{{ $property['category'] ?? __('N/A') }}</span>
                                    </li>
                                    <li><i
                                            class="fas fa-compass"></i><strong>{{ __('app/properties.orientation') }}:</strong>
                                        <span>{{ $property['orientation'] ?? __('N/A') }}</span>
                                    </li>
                                    <li><i
                                            class="fas fa-layer-group"></i><strong>{{ __('app/properties.floor') }}:</strong>
                                        <span>{{ $property['floor'] ?? __('N/A') }}</span>
                                    </li>
                                    <li><i
                                            class="fas fa-file-signature"></i><strong>{{ __('app/properties.contract_type') }}:</strong>
                                        <span>{{ $property['contract_type'] ?? __('N/A') }}</span>
                                    </li>
                                    <li><i
                                            class="fas fa-house-medical"></i><strong>{{ __('app/properties.condition') }}:</strong>
                                        <span>{{ $property['condition'] ?? __('N/A') }}</span>
                                    </li>
                                    <li><i
                                            class="fas fa-hammer"></i><strong>{{ __('app/properties.year_built') }}:</strong>
                                        <span>{{ $property['year_built'] ?? __('N/A') }}</span>
                                    </li>
                                    @foreach ($property['attribute'] as $attr)
                                        <li>
                                            <i class="fas {{ $attr['icon'] ?? 'fa-info-circle' }}"></i>
                                            <strong>{{ $attr['name'] ?? __('Unknown') }}:</strong>
                                            <span>{{ $attr['value'] ?? '-' }}</span>
                                        </li>
                                    @endforeach
                                @else
                                @endif
                            </ul>
                        </div>

                        @if (!empty($property['amenities']))
                            <div class="tab-content-v3" id="amenitiesContentV3">
                                <h3>{{ __('app/properties.amenities') }}</h3>
                                <ul id="amenitiesListV3" class="amenities-list-v3"></ul>
                            </div>
                        @endif

                        @if (!empty($property['video_url']))
                            <div class="tab-content-v3" id="videoContentV3">
                                <h3>{{ __('app/properties.video') }}</h3>
                                <div class="video-placeholder-v3" id="videoPlaceholderV3" style="cursor: pointer;"
                                    title="{{ __('app/properties.view_video_tour_action') }}">
                                    @if (!empty($property['video_img_url']))
                                        <img src="{{ $property['video_img_url'] }}"
                                            alt="{{ __('app/properties.video_thumbnail_alt', ['title' => $property['slug']]) }}"
                                            style="width:100%; border-radius: var(--v3-border-radius); max-height: 400px; object-fit: cover;">
                                        <div class="play-button-overlay"><i class="fas fa-play-circle fa-3x"></i></div>
                                    @else
                                        <button class="btn-v3 btn-v3-primary btn-lg">
                                            <i class="fab fa-youtube"></i>
                                            {{ __('app/properties.view_video_tour_action') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if (!empty($property['latitude']) && !empty($property['longitude']))
                            <div class="tab-content-v3" id="mapContentV3">
                                <h3>{{ __('app/properties.location') }}</h3>
                                <div id="leafletMapContainerV3"
                                    style="height: 350px; width:100%; border-radius: var(--v3-border-radius); margin-top:15px; background-color: #f0f0f0;">
                                </div>
                            </div>
                        @endif
                    </section>

                    @if (isset($property['faqs']) && count($property['faqs']) > 0)
                        @include('app.pages.property._faq', ['faqs' => $property['faqs']])
                    @endif
                    {{-- @include('app.pages.property._community')  --}}
                </div>

                @include('app.pages.property._sidebar', [
                    'agent' => $property['owner'],
                    'propertyId' => $property['id'],
                ])
            </div>
        </div>
    </main>

    <button id="floatingContactBtnV3" class="btn-v3 btn-v3-primary floating-btn-v3">
        <i class="fas fa-comments"></i> {{ __('app/properties.contact_agent_title') }}
    </button>

    @include('app.pages.property._models')

    <button id="floatingContactBtnV3" class="btn-v3 btn-v3-primary floating-btn-v3">
        <i class="fas fa-comments"></i> تواصل معنا
    </button>

@endsection

@push('script')

    @if (isset($property))
        <script>
            // تأكد أن AppConfig مُعرف بالفعل من الـ layout
            window.AppConfig = window.AppConfig || {};
            window.AppConfig.pageData = {!! json_encode($property) !!};

            // تأكد أن AppConfig و AppConfig.routes مُعرفان
            window.AppConfig = window.AppConfig || {};
            window.AppConfig.routes = window.AppConfig.routes || {};

            @if (isset($property) && $property['id']) // استخدم $property['id'] مباشرة
                Object.assign(window.AppConfig.routes, {
                    'properties.toggle-favorite': "{{ route('api.properties.toggle-favorite', ['property' => $property['id']]) }}",
                    'properties.toggle-blacklist': "{{ route('api.properties.toggle-blacklist', ['property' => $property['id']]) }}",
                    'properties.submit-report': "{{ route('api.properties.submit-report', ['property' => $property['id']]) }}",
                    'properties.contact-agent': "{{ route('api.properties.contact-agent', ['property' => $property['id']]) }}"
                });
            @endif
        </script>
    @endif


    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    {{-- =============================================================== --}}
    {{-- START: MAP INITIALIZATION AND RESIZE FIX                        --}}
    {{-- =============================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let leafletMap = null;
            let mapInitialized = false;

            const mapContainer = document.getElementById('leafletMapContainerV3');
            const mapTabContent = document.getElementById('mapContentV3');

            if (!mapContainer || !mapTabContent) {
                return;
            }

            function initializeMap() {
                if (!window.AppConfig?.pageData?.latitude) {
                    mapContainer.innerHTML =
                        `<p style="text-align: center; padding-top: 50px;">{{ __('app/properties.map_data_not_available') }}</p>`;
                    return;
                }

                const lat = parseFloat(window.AppConfig.pageData.latitude);
                const lng = parseFloat(window.AppConfig.pageData.longitude);
                const propertyLocation = window.AppConfig.pageData.location || 'Property Location';

                leafletMap = L.map(mapContainer).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(leafletMap);
                L.marker([lat, lng]).addTo(leafletMap)
                    .bindPopup(`<b>${propertyLocation}</b>`)
                    .openPopup();

                mapInitialized = true;
            }

            const observer = new MutationObserver((mutationsList) => {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        // When the tab becomes visible
                        if (mutation.target.classList.contains('active')) {
                            if (!mapInitialized) {
                                // Initialize map for the first time
                                initializeMap();
                            } else {
                                // For subsequent times, invalidate size to force re-render
                                // Use a small timeout to ensure the container is fully rendered in the DOM
                                setTimeout(() => {
                                    if (leafletMap) {
                                        leafletMap.invalidateSize(true);
                                    }
                                }, 150);
                            }
                        }
                    }
                }
            });

            // Start observing the map tab content for class changes
            observer.observe(mapTabContent, {
                attributes: true
            });
        });
    </script>
    {{-- =============================================================== --}}
    {{-- END: MAP INITIALIZATION AND RESIZE FIX                          --}}
    {{-- =============================================================== --}}

    @vite(['resources/js/alpine/app/details/main.js'])

@endpush
