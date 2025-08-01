@extends('app.layouts.default')

@push('css_or_js')
    <!-- Local CSS Files -->
    <link rel="stylesheet" href="{{ asset('style/search.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
@endpush

@section('content')
    <div x-data="propertySearch()" id="property-search-page" class="">
        {{-- الشريط العلوي الثابت --}}
        <div class="page-title-bar">
            <header class="top-filter-bar">
                <div class="filter-controls main-filters">
                    <select x-model="category" @change="setParams(); fetchProperties()">
                        <option value="">{{ trans('app/search.all_categories') }}</option>
                        @foreach ($categories as $value)
                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                    <select x-model="type" @change="setParams(); fetchProperties()">
                        <option value="">{{ trans('app/search.all_types') }}</option>
                        @foreach ($types as $value)
                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                    <select x-model="governorate" @change="setParams(); fetchProperties()">
                        <option value="">{{ trans('app/search.all_city') }}</option>
                        @foreach ($governorates as $value)
                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="action-buttons">
                    <button class="filter-button" @click="openSidebar"><i class="fas fa-filter"></i><span
                            class="btn-text">{{ trans('app/search.filters') }}</span></button>

                    {{-- ✨ تعديل: العودة إلى استخدام <select> للترتيب --}}
                    <select x-model="sortKey" @change="updateSort()" class="top-bar-select" x-show="layout!='map'">
                        <option value="">{{ trans('app/search.sort_default') }}</option>
                        <option value="newest_des">{{ trans('app/search.sort_newest') }}</option>
                        <option value="featured_des">{{ trans('app/search.sort_featured') }}</option>
                        <option value="price_asc">{{ trans('app/search.sort_price_asc') }}</option>
                        <option value="price_des">{{ trans('app/search.sort_price_desc') }}</option>
                        <option value="size_des">{{ trans('app/search.sort_size_desc') }}</option>
                        <option value="size_asc">{{ trans('app/search.sort_size_asc') }}</option>
                        <option value="rooms_count_des">{{ trans('app/search.sort_rooms_desc') }}</option>
                        <option value="rooms_count_asc">{{ trans('app/search.sort_rooms_asc') }}</option>
                        <option value="cached_floor_value_des">{{ trans('app/search.sort_floor_desc') }}</option>
                        <option value="cached_floor_value_asc">{{ trans('app/search.sort_floor_asc') }}</option>
                    </select>

                    {{-- ✨ تعديل: العودة إلى استخدام <select> لتغيير العرض --}}
                    <select x-model="layout" @change="handleLayoutChange()" class="top-bar-select">
                        <option value="row" class="layout-option-row">{{ trans('app/search.layout_row') }}</option>
                        <option value="grid">{{ trans('app/search.layout_grid') }}</option>
                        <option value="map">{{ trans('app/search.layout_map') }}</option>
                    </select>
                </div>
            </header>
            <div class="listings-info" x-show="properties.length > 0">
                <span x-show="loading">{{ trans('app/search.loading') }}</span>
                <span x-show="!loading"
                    x-text="`{{ trans('app/search.showing_results', ['total' => ':total', 'shown' => ':shown']) }}`.replace(':total', pagination?.total || 0).replace(':shown', properties.length || 0)"></span>
                <div x-show="showMapWarning" class="map-limit-warning" x-cloak>
                    <i class="fas fa-info-circle"></i>
                    <span x-text="`Showing the first ${limitOnMap} results. Zoom in for more details.`"></span>
                </div>
            </div>
        </div>

        {{-- حاوية المحتوى الرئيسي (تبدأ بعد الشريط العلوي) --}}
        <div class="search-content-wrapper">
            <div class="content-area" id="contentArea">
                <section class="listings-container" id="listingsContainer">

                    <div id="propertyCardsContainer" class="property-cards-container" x-show="properties.length > 0">
                        <template x-for="property in properties" :key="property.id">
                            <a :href="`{{ route('properties.details', ['property' => ':id']) }}`.replace(':id', property.id)"
                                class="property-card-link">
                                <article class="property-card">
                                    <div class="property-image"><img :src="property.image_url" :alt="property.title"
                                            loading="lazy"></div>
                                    <div class="property-details">
                                        <div class="property-price-actions">
                                            <p class="price" x-text="property.price_display">
                                            </p>
                                            <button @click.prevent.stop="" class="icon-button"><i
                                                    class="far fa-heart"></i></button>
                                        </div>
                                        <p class="features"
                                            x-text="`${property.rooms_count} {{ trans('app/search.rooms') }} • ${property.size} {{ trans('app/search.area') }}`">
                                        </p>
                                        <p class="address" x-text="property.location"></p>
                                    </div>
                                </article>
                            </a>
                        </template>
                    </div>
                    <div class="loading-spinner" x-show="loading" x-cloak>
                        <div class="spinner"></div>
                    </div>
                    <div class="loading-spinner" x-show="loadingMore">
                        <div class="spinner"></div>
                    </div>

                    {{-- no results --}}
                    <div x-show="!loading && properties.length === 0" class="no-results-container" x-cloak>
                        <div class="no-results-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h2>{{ trans('app/search.NoProperty') }}</h2>
                        <p>{{ trans('app/search.sub') }}</p>
                        <button @click="openSidebar" class="no-results-button">
                            <i class="fas fa-filter"></i> {{ trans('app/search.Adjust') }}
                        </button>
                    </div>

                    {{-- end  result --}}
                    <div class="end-of-results-message"
                        x-show="reachedEnd && !loadingMore && !(!loading && properties.length === 0)" x-cloak>
                        <i class="fas fa-check-circle"></i>
                        <p>{{ trans('app/search.allForNow') }}</p>
                        <button @click="checkForUpdates" class="retry-button">
                            <i class="fas fa-sync-alt"></i> {{ trans('app/search.check') }}
                        </button>
                    </div>

                </section>
                <section class="map-container" id="mapContainer">
                    <div id="map"></div>
                </section>
            </div>
        </div>

        <aside class="filter-sidebar" id="filterSidebar">
            <div class="sidebar-header">
                <h2>{{ trans('app/search.filters') }}</h2>
                <button @click="closeSidebar" class="close-button" aria-label="Close filters">×</button>
            </div>
            <div class="sidebar-content">
                <div class="form-style">
                    <label>{{ trans('app/search.category') }}</label>
                    <select x-model="category">
                        <option value="">{{ trans('app/search.all_categories') }}</option>
                        @foreach ($categories as $value)
                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-style">
                    <label>{{ trans('app/search.type') }}</label>
                    <select x-model="type">
                        <option value="">{{ trans('app/search.all_types') }}</option>
                        @foreach ($types as $value)
                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-style">
                    <label>{{ trans('app/search.governorate') }}</label>
                    <select x-model="governorate">
                        <option value="">{{ trans('app/search.all_city') }}</option>
                        @foreach ($governorates as $value)
                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-style">
                    <label class="font-semibold">{{ trans('app/search.floor') }}</label>
                    <div class="input-range">

                        <select x-model="floor_min">
                            <option value="">{{ trans('app/search.floor_min') }}</option>
                            @foreach ($floors as $value)
                                <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                            @endforeach
                        </select>

                        <span>-</span>

                        <select x-model="floor_max">
                            <option value="">{{ trans('app/search.floor_max') }}</option>
                            @foreach ($floors as $value)
                                <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="form-style">
                    <label class="font-semibold">{{ trans('app/search.price') }}</label>
                    <div class="input-range">
                        <input type="text" x-model.debounce.500ms="price_min" x-mask:dynamic="$money($input, ',')"
                            placeholder="{{ trans('app/search.price_min') }}">
                        <span>-</span>
                        <input type="text" x-model.debounce.500ms="price_max" x-mask:dynamic="$money($input, ',')"
                            placeholder="{{ trans('app/search.price_max') }}">
                    </div>
                </div>
                <div class="form-style">
                    <label class="font-semibold">{{ trans('app/search.area') }} (m²)</label>
                    <div class="input-range">
                        <input type="text" x-model.debounce.500ms="area_min" x-mask:dynamic="$money($input, ',')"
                            placeholder="{{ trans('app/search.area_min') }}">
                        <span>-</span>
                        <input type="text" x-model.debounce.500ms="area_max" x-mask:dynamic="$money($input, ',')"
                            placeholder="{{ trans('app/search.area_max') }}">
                    </div>
                </div>
                <div class="form-style">
                    <label class="font-semibold">{{ trans('app/search.rooms') }}</label>
                    <input type="number" x-model.debounce.500ms="rooms_count" placeholder="Any" min="0"
                        step="1">
                </div>
                <div class="form-style wd-amenities">
                    <h6 class="title">{{ trans('app/search.amenities') }}</h6>
                    <div class="group-amenities">
                        @foreach ($amenities as $amenity)
                            <fieldset class="amenities-item">
                                <input type="checkbox" x-model="amenities" value="{{ $amenity['id'] }}"
                                    id="amenity-sidebar-{{ $amenity['id'] }}">
                                <label for="amenity-sidebar-{{ $amenity['id'] }}">{{ $amenity['name'] }}</label>
                            </fieldset>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="sidebar-footer">
                <button @click="applyFiltersAndClose"
                    class="apply-filters-button">{{ trans('app/search.search') }}</button>
            </div>
        </aside>

        <div id="filterOverlay" @click="closeSidebar" class="filter-overlay"></div>
    </div>
@endsection

@push('script')
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        Object.assign(window.AppConfig.routes, {
            'properties.search': "{{ route('api.properties') }}",
            'properties.details': "{{ route('properties.details', ['property' => ':property']) }}",
        });
    </script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    @vite(['resources/js/alpine/app/search/main.js'])
@endpush
