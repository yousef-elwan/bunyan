@extends('dashboard.layouts.default')

@section('title', __('dashboard/property.edit.page_title', ['name' => $property->default_translation->location ??
    '...']))

    @push('css_or_js')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .leaflet-container {
                height: 350px;
                width: 100%;
                border-radius: 0.5rem;
                z-index: 1;
            }

            .price-input {
                text-align: left;
            }

            .drag-over {
                border-color: #4f46e5;
                background-color: #eef2ff;
            }

            .marked-for-deletion img {
                opacity: 0.4;
                border: 2px solid #dc2626;
            }

            .marked-for-deletion .undo-delete-btn {
                background-color: #4f46e5;
            }
        </style>
    @endpush

@section('content')
    <div class="flex flex-col min-h-screen bg-page-bg">

        @php
            $propertyName =
                $property->default_translation->location ??
                ($property->translations->firstWhere('locale', app()->getLocale())->location ??
                    ($property->translations->first()->location ?? 'Property #' . $property->id));
        @endphp

        <!-- Header Section -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-4 p-4">
            <nav class="mb-2 max-w-[80vw]" aria-label="Breadcrumb">
                <ol class="flex items-center flex-nowrap overflow-hidden text-sm text-text-secondary">
                    @isset($breadcrumbs)
                        @foreach ($breadcrumbs as $breadcrumb)
                            <li class="flex items-center min-w-0 {{ $loop->last ? 'flex-shrink-0' : '' }}">
                                @if ($breadcrumb['url'])
                                    <a href="{{ $breadcrumb['url'] }}" class="truncate hover:text-accent-primary hover:underline"
                                        title="{{ $breadcrumb['name'] }}">
                                        <span>{{ $breadcrumb['name'] }}</span>
                                    </a>
                                    @if (!$loop->last)
                                        <i class="fas fa-angle-left text-gray-400 mx-2 flex-shrink-0"></i>
                                    @endif
                                @else
                                    <span class="font-medium text-text-primary truncate" title="{{ $breadcrumb['name'] }}">
                                        {{ $breadcrumb['name'] }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    @endisset
                </ol>
            </nav>
            <div class="flex flex-wrap items-center justify-between gap-y-2">
                <h1 class="text-2xl font-bold text-text-primary">
                    {{ __('dashboard/property.edit.page_title', ['name' => Str::limit($propertyName, 30)]) }}
                </h1>
            </div>
        </div>

        <form id="editPropertyForm" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @csrf
            @method('PUT')

            {{-- ====================================================================== --}}
            {{-- START: Main Content Column (Scrollable) --}}
            {{-- ====================================================================== --}}
            <div class="lg:col-span-8 space-y-6">

                <!-- Language Tabs for Descriptions -->
                <div class="bg-card-bg rounded-xl shadow-custom border border-border-color" x-data="{ activeTab: '{{ $langs->first()->locale ?? 'en' }}' }">
                    <!-- Tab Buttons -->
                    <div class="border-b border-border-color">
                        <nav class="flex -mb-px" aria-label="Tabs">
                            @foreach ($langs as $lang)
                                <button type="button" @click="activeTab = '{{ $lang->locale }}'"
                                    :class="{
                                        'border-accent-primary text-accent-primary': activeTab === '{{ $lang->locale }}',
                                        'border-transparent text-text-secondary hover:text-accent-primary hover:border-gray-300': activeTab !== '{{ $lang->locale }}'
                                    }"
                                    class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm focus:outline-none">
                                    {{ $lang->name }}
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Tab Content Panels -->
                    <div class="p-5">
                        @foreach ($langs as $lang)
                            @php $translation = $property->translations->firstWhere('locale', $lang->locale); @endphp
                            <div x-show="activeTab === '{{ $lang->locale }}'" x-cloak>
                                <div class="mb-5">
                                    <label for="propDesc_{{ $lang->locale }}"
                                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_description', ['lang' => strtoupper($lang->name)]) }}
                                        <span class="text-danger">*</span></label>
                                    <textarea id="propDesc_{{ $lang->locale }}" name="content_{{ $lang->locale }}" class="tinymce-editor"
                                        @if ($lang->direction === 'rtl') dir="rtl" @endif>{{ old('content_' . $lang->locale, $translation ? $translation->content : '') }}</textarea>
                                    <div class="mt-1 text-sm text-danger" id="content_{{ $lang->locale }}_error"></div>
                                </div>

                                <div>
                                    <label for="propLocation_{{ $lang->locale }}"
                                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_location_desc', ['lang' => strtoupper($lang->name)]) }}
                                    </label>
                                    <input type="text" id="propLocation_{{ $lang->locale }}"
                                        name="location_{{ $lang->locale }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                        @if ($lang->direction === 'rtl') dir="rtl" @endif
                                        value="{{ old('location_' . $lang->locale, $translation ? $translation->location : '') }}">
                                    <div class="mt-1 text-sm text-danger" id="location_{{ $lang->locale }}_error"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Location & Map Box -->
                <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                    <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                        {{ __('dashboard/property.edit.section_location') }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column: Form Fields -->
                        <div class="space-y-6">
                            <div>
                                <label for="propCity"
                                    class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_city') }}
                                    <span class="text-danger">*</span></label>
                                <select id="propCity" name="city_id"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                    required>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city['id'] }}" @selected(old('city_id', $property->city_id) == $city['id'])>
                                            {{ $city['name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="mt-1 text-sm text-danger invalid-feedback" id="city_id_error"></div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="propLatitude"
                                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_latitude') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" id="propLatitude" name="latitude"
                                        class="block w-full text-sm bg-gray-100 border-gray-300 rounded-md shadow-sm"
                                        value="{{ old('latitude', $property->latitude) }}" readonly required>
                                    <div class="mt-1 text-sm text-danger invalid-feedback" id="latitude_error"></div>
                                </div>
                                <div>
                                    <label for="propLongitude"
                                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_longitude') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" id="propLongitude" name="longitude"
                                        class="block w-full text-sm bg-gray-100 border-gray-300 rounded-md shadow-sm"
                                        value="{{ old('longitude', $property->longitude) }}" readonly required>
                                    <div class="mt-1 text-sm text-danger invalid-feedback" id="longitude_error"></div>
                                </div>
                            </div>

                            <div>
                                <label for="propVideoUrl"
                                    class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_video_url') }}</label>
                                <input type="url" id="propVideoUrl" name="video_url"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                    placeholder="https://www.youtube.com/watch?v=your_video_id"
                                    value="{{ old('video_url', $property->video_url) }}">
                                <div class="mt-1 text-sm text-danger invalid-feedback" id="video_url_error"></div>
                            </div>
                        </div>

                        <!-- Right Column: Map -->
                        <div>
                            <label
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_map') }}</label>
                            <div id="propertyLocationMap"></div>
                        </div>
                    </div>
                </div>

                <!-- Property Details Section -->
                <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                    <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                        {{ __('dashboard/property.edit.section_details') }}</h2>
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="propArea"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_area') }}
                            </label>
                            <input type="number" id="propArea" name="size"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                value="{{ old('size', $property->size) }}">
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="size_error"></div>
                        </div>
                        <div>
                            <label for="propYearBuilt"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_year_built') }}</label>
                            <select id="propYearBuilt" name="year_built"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary">
                                @for ($year = date('Y'); $year >= 1800; $year--)
                                    <option value="{{ $year }}" @selected(old('year_built', $property->year_built) == $year)>{{ $year }}
                                    </option>
                                @endfor
                            </select>
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="year_built_error"></div>
                        </div>
                        <div>
                            <label for="propFloor"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_floor') }}</label>
                            <select id="propFloor" name="floor_id"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary">
                                <option value="">{{ __('dashboard/property.edit.select_floor_placeholder') }}
                                </option>
                                @foreach ($floors as $floor)
                                    <option value="{{ $floor['id'] }}" @selected(old('floor_id', $property->floor_id) == $floor['id'])>{{ $floor['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="floor_id_error"></div>
                        </div>
                        <div>
                            <label for="propBedrooms"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_rooms') }}</label>
                            <input type="number" id="propBedrooms" name="rooms_count"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                min="0" value="{{ old('rooms_count', $property->rooms_count) }}">
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="rooms_count_error"></div>
                        </div>
                        <div>
                            <label for="propOrientation"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_orientation') }}</label>
                            <select id="propOrientation" name="orientation_id"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary">
                                <option value="">{{ __('dashboard/property.edit.select_orientation_placeholder') }}
                                </option>
                                @foreach ($orientations as $orientation)
                                    <option value="{{ $orientation['id'] }}" @selected(old('orientation_id', $property->orientation_id) == $orientation['id'])>
                                        {{ $orientation['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="orientation_id_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Custom Attributes Section -->
                <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color"
                    id="customAttributesSection" @if (!old('category_id', $property->category_id)) style="display: none;" @endif>
                    <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                        {{ __('dashboard/property.edit.section_custom_attributes') }}</h2>
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2" id="customAttributesContainer"></div>
                    <div class="mt-1 text-sm text-danger invalid-feedback" id="attributes_error"></div>
                </div>

                <!-- Amenities Section -->
                <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                    <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                        {{ __('dashboard/property.edit.section_amenities') }}</h2>
                    <div>
                        <label
                            class="block mb-3 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_amenities') }}</label>
                        <div id="amenitiesCheckboxGroup" class="flex flex-wrap gap-x-6 gap-y-3">
                            @php $propertyAmenitiesIds = old('amenities', $property->amenities->pluck('id')->toArray()); @endphp
                            @foreach ($amenities as $amenity)
                                <label class="flex items-center gap-2 font-normal cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity['id'] }}"
                                        class="w-4 h-4 rounded text-accent-primary border-gray-300 focus:ring-accent-primary"
                                        @checked(in_array($amenity['id'], $propertyAmenitiesIds))>
                                    <span class="text-sm text-text-secondary">{{ $amenity['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-1 text-sm text-danger invalid-feedback" id="amenities_error"></div>
                    </div>
                </div>



            </div>
            {{-- END: Main Content Column --}}

            {{-- ====================================================================== --}}
            {{-- START: Sidebar Column (Sticky) --}}
            {{-- ====================================================================== --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="sticky top-6 space-y-6">

                    <!-- Publish Box -->
                    <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                        <h3 class="pb-4 mb-4 text-lg font-semibold border-b border-border-color text-text-primary">
                            {{ __('dashboard/property.edit.section_publish') }}</h3>
                        <div class="space-y-3">
                            <button type="submit" id="savePropertyBtn"
                                class="w-full px-6 py-2.5 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90 disabled:opacity-50">{{ __('dashboard/property.edit.button_update') }}</button>
                            <a href="{{ route('dashboard.properties.index') }}" id="cancelEditPropertyBtn"
                                class="block w-full text-center px-6 py-2 text-sm font-medium border rounded-lg border-border-color text-text-secondary hover:bg-page-bg">{{ __('dashboard/property.edit.button_cancel') }}</a>
                        </div>
                    </div>

                    <!-- Categorization Box -->
                    <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                        <h3 class="pb-4 mb-4 text-lg font-semibold border-b border-border-color text-text-primary">
                            {{ __('dashboard/property.edit.section_categorization') }}</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="propStatus"
                                    class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_status') }}
                                    <span class="text-danger">*</span></label>
                                <select id="propStatus" name="type_id"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                    required>
                                    @foreach ($types as $type)
                                        <option value="{{ $type['id'] }}" @selected(old('type_id', $property->type_id) == $type['id'])>
                                            {{ $type['name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="mt-1 text-sm text-danger" id="type_id_error"></div>
                            </div>
                            <div>
                                <label for="propCategory"
                                    class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_category') }}
                                    <span class="text-danger">*</span></label>
                                <select id="propCategory" name="category_id"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                    required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id'] }}" @selected(old('category_id', $property->category_id) == $category['id'])>
                                            {{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="mt-1 text-sm text-danger" id="category_id_error"></div>
                            </div>
                            <div>
                                <label for="available_from"
                                    class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_available_from') }}
                                    <span class="text-danger">*</span></label>
                                <input type="date" id="available_from" name="available_from"
                                    class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                    value="{{ old('available_from', $property->available_from ? \Carbon\Carbon::parse($property->available_from)->format('Y-m-d') : '') }}"
                                    required>
                                <div class="mt-1 text-sm text-danger" id="available_from_error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Box -->
                    <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                        <h3 class="pb-4 mb-4 text-lg font-semibold border-b border-border-color text-text-primary">
                            {{ __('dashboard/property.edit.label_price') }}</h3>
                        <div>
                            <label for="propPrice"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.edit.label_price') }}
                                <span class="text-danger">*</span></label>
                            <input type="text" id="propPrice"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary price-input @if ((bool) old('price_on_request', $property->price_on_request)) bg-gray-100 @endif"
                                data-hidden-target="#price_clean" value="{{ old('price', $property->price) }}"
                                @if ((bool) old('price_on_request', $property->price_on_request)) disabled @else required @endif>
                            <input type="hidden" name="price" id="price_clean"
                                value="{{ old('price', $property->price) }}">
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="price_error"></div>
                        </div>
                        <div class="mt-3">
                            <label for="propPriceOnRequest" class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="propPriceOnRequest" name="price_on_request" value="true"
                                    class="w-4 h-4 rounded text-accent-primary border-gray-300 focus:ring-accent-primary"
                                    @checked((bool) old('price_on_request', $property->price_on_request))>
                                <span
                                    class="text-sm text-text-secondary">{{ __('dashboard/property.edit.label_price_on_request') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Gallery Box -->
                    <div id="image-drop-area" class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                        <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                            {{ __('dashboard/property.edit.section_gallery') }}</h2>
                        @if ($property->images->isNotEmpty())
                            <h3 class="mb-2 text-sm font-medium text-text-primary">
                                {{ __('dashboard/property.edit.gallery_existing_images') }}</h3>
                            <div class="flex flex-wrap gap-4 p-4 mb-6 border-2 border-dashed rounded-lg border-border-color"
                                id="existingImagesGallery">
                                @foreach ($property->images as $image)
                                    <div class="relative w-28 h-28 group existing-image"
                                        data-image-name="{{ $image->name }}">
                                        <img src="{{ $image->image_url ?? asset('path/to/default-placeholder.jpg') }}"
                                            alt="Property Image {{ $image->id }}"
                                            class="object-cover w-full h-full rounded-md border border-border-color">
                                        <button type="button"
                                            class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 bg-danger rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none remove-existing-image-btn"
                                            title="{{ __('dashboard/property.edit.button_remove_image') }}">×</button>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="deleted_images" id="deleted_images_input">
                        @endif
                        <h3 class="mb-2 text-sm font-medium text-text-primary">
                            {{ __('dashboard/property.edit.gallery_add_new') }}</h3>
                        <div>
                            <label for="propImagesUpload"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg cursor-pointer bg-accent-primary hover:bg-opacity-90"><i
                                    class="fas fa-upload"></i>
                                {{ __('dashboard/property.edit.label_upload_button') }}</label>
                            <input type="file" id="propImagesUpload" class="hidden" multiple accept="image/*">
                            <p class="mt-2 text-xs text-text-secondary">{{ __('dashboard/property.edit.upload_note') }}
                            </p>
                        </div>
                        <div id="imageGalleryPreviewContainer"
                            class="flex flex-wrap gap-4 p-4 mt-4 border-2 border-dashed rounded-lg border-border-color min-h-[140px] transition-colors duration-300">
                        </div>
                        <div id="uploadProgressContainer" style="display:none;" class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-accent-primary h-2.5 rounded-full progress-bar-fill" style="width: 0%">
                                </div>
                            </div>
                            <small id="uploadStatusText" class="block mt-1 text-xs text-text-secondary"></small>
                        </div>
                    </div>



                </div>
            </div>
            {{-- END: Sidebar Column --}}

        </form>
    </div>
@endsection

@push('script')
    {{-- Page Data --}}
    @php
        $jsPreparedCustomAttributes = $property->propertyAttribute->mapWithKeys(
            fn($attr) => [$attr->attribute_id => ['value' => $attr->value]],
        );
    @endphp
    <script type="application/json" id="page-data">{!! json_encode([
        'property' => [
                'id' => $property->id, 
                'latitude' => (float) $property->latitude, 
                'longitude' => (float) $property->longitude, 
                'category_id' => (int) $property->category_id, 
                'custom_attributes' => (object) $jsPreparedCustomAttributes
            ], 
            'locales' => $langs->pluck('locale')->toArray(), 
            'csrf_token' => csrf_token()
            ]) !!}</script>

    {{-- Configs & Routes --}}
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = {
            'api.dashboard-properties.update': "{{ route('api.dashboard-properties.update', ['property' => $property->id]) }}",
            'api.customAttribute': "{{ route('api.customAttribute') }}",
            'dashboard.properties.index': "{{ route('dashboard.properties.index') }}",
            'api.dashboard-properties.images.store': "{{ route('api.dashboard-properties.images.store', ['property' => $property->id]) }}",
        };
        window.AppConfig.i18n = @json(__('dashboard/property.edit'));
        window.AppConfig.settings = {
            max_images: {{ config('media.max_images', 10) }},
            max_file_size_mb: {{ config('media.max_file_size_mb', 2) }}
        };
    </script>

    {{-- TinyMCE --}}
    <script src="{{ asset('tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: 'textarea.tinymce-editor',
                height: 400,
                plugins: 'code table lists image link autoresize fullscreen',
                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | table | image | link unlink | code fullscreen',
                link_default_target: '_blank',
                link_assume_external_targets: true,
                skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
                content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                    'default'),
                promotion: false,
            });
        });
    </script>

    {{-- Leaflet & Main Page Logic --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @vite(['resources/js/alpine/dashboard/property/edit.js'])
@endpush
