@extends('dashboard.layouts.default')

@section('title', __('dashboard/category.create.page_title'))

@push('css_or_js')
    <style>
        .drag-over {
            border-color: #4f46e5;
            background-color: #eef2ff;
        }
    </style>
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.pageData = window.AppConfig.pageData || {};
        Object.assign(window.AppConfig.routes, {
            'api.category.store': "{{ route('api.category.store') }}",
            'dashboard.category.index': "{{ route('dashboard.category.index') }}",
        });
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/category.create')));
        window.AppConfig.pageData.locales = @json($langs->pluck('locale')->toArray());
    </script>
    @vite(['resources/js/alpine/dashboard/category/create.js'])
@endpush

@section('content')
    {{-- <div class="flex flex-col min-h-screen bg-page-bg"> --}}
    <div class="bg-page-bg">

        <!-- Header Section -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-4 p-4">

            <!-- =================================================================== -->
            <!-- START: BREADCRUMBS WITH TRUNCATION (THE FIX IS HERE)          -->
            <!-- =================================================================== -->
            <nav class="mb-2 max-w-[80vw]" aria-label="Breadcrumb">
                <!-- The container is now a flexbox that prevents wrapping and hides overflow -->
                <ol class="flex items-center flex-nowrap overflow-hidden text-sm text-text-secondary">
                    @isset($breadcrumbs)
                        @foreach ($breadcrumbs as $breadcrumb)
                            <!-- Each breadcrumb item is also a flex item that can shrink -->
                            <li class="flex items-center min-w-0 {{ $loop->last ? 'flex-shrink-0' : '' }}">
                                @if ($breadcrumb['url'])
                                    <!-- The link itself will be truncated if needed -->
                                    <a href="{{ $breadcrumb['url'] }}" class="truncate hover:text-accent-primary hover:underline"
                                        title="{{ $breadcrumb['name'] }}">
                                        <span>{{ $breadcrumb['name'] }}</span>
                                    </a>
                                    <!-- Separator icon -->
                                    @if (!$loop->last)
                                        <i class="fas fa-angle-left text-gray-400 mx-2 flex-shrink-0"></i>
                                    @endif
                                @else
                                    <!-- The last item (current page) will not be truncated and will be bold -->
                                    <span class="font-medium text-text-primary truncate" title="{{ $breadcrumb['name'] }}">
                                        {{ $breadcrumb['name'] }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    @endisset
                </ol>
            </nav>
            <!-- =================================================================== -->
            <!-- END: BREADCRUMBS FIX                                                -->
            <!-- =================================================================== -->

            <!-- Page Title & Main Action Button -->
            <div class="flex flex-wrap items-center justify-between gap-y-2">
                <h1 class="text-2xl font-bold text-text-primary">
                    {{ __('dashboard/category.create.page_title') }}
                </h1>
            </div>
        </div>

        <form id="createCategoryForm" class="space-y-6">
            @csrf
            <!-- Primary Info Section -->
            <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/category.create.primaryinfo') }}
                </h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    @foreach ($langs as $lang)
                        <div class="mb-5">
                            <label for="name_{{ $lang->locale }}" class="block mb-2 text-sm font-medium text-text-primary">
                                {{ __('dashboard/category.create.entername') }} ({{ $lang->name }}) <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="text" name="name_{{ $lang->locale }}" id="name_{{ $lang->locale }}"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                @if ($lang->direction === 'rtl') dir="rtl" @endif required>
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="name_{{ $lang->locale }}_error">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Photo Section -->
            <div id="image-drop-area" class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/property.create.section_gallery') }}
                    <span
                        class="text-sm font-normal text-text-secondary">({{ __('dashboard/property.create.gallery_info') }})</span>
                </h2>
                <div>
                    <label for="propImagesUpload"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg cursor-pointer bg-accent-primary hover:bg-opacity-90">
                        <i class="fas fa-upload"></i>
                        {{ __('dashboard/property.create.label_upload_button') }}
                    </label>
                    <input type="file" id="propImagesUpload" class="hidden" multiple accept="image/*">
                    <p class="mt-2 text-xs text-text-secondary">{{ __('dashboard/property.create.upload_note') }}</p>
                </div>
                <div id="imageGalleryPreviewContainer"
                    class="flex flex-wrap gap-4 p-4 mt-4 border-2 border-dashed rounded-lg border-border-color min-h-[140px]">
                    {{-- عروض الصور المصغرة سيتم حقنها هنا عبر JS --}}
                </div>
                <div id="uploadProgressContainer" style="display:none;" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-accent-primary h-2.5 rounded-full progress-bar-fill" style="width: 0%"></div>
                    </div>
                    <small id="uploadStatusText" class="block mt-1 text-xs text-text-secondary"></small>
                </div>
            </div>
            {{-- <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color" id="image-drop-area">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/category.create.photo') }}
                </h2>
                <div>
                    <label for="imageUpload"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg cursor-pointer bg-accent-primary hover:bg-opacity-90">
                        <i class="fas fa-upload"></i> {{ __('dashboard/category.create.chosePhoto') }}
                    </label>
                    <input type="file" name="image" id="imageUpload" class="hidden" accept="image/*">
                    <p class="mt-2 text-xs text-text-secondary">{{ __('dashboard/category.create.note') }}</p>
                    <div class="mt-1 text-sm text-danger invalid-feedback" id="image_error"></div>
                </div>
                <div id="imagePreviewContainer"
                    class="flex flex-wrap gap-4 p-4 mt-4 border-2 border-dashed rounded-lg border-border-color min-h-[140px] transition-colors duration-300">
                    <p class="self-center w-full text-sm text-center text-text-secondary">
                        {{ __('dashboard/category.create.no_image_preview') }}</p>
                </div>
                <div id="uploadProgressContainer" style="display:none;" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-accent-primary h-2.5 rounded-full progress-bar-fill" style="width: 0%"></div>
                    </div>
                    <small id="uploadStatusText" class="block mt-1 text-xs text-text-secondary"></small>
                </div>
            </div> --}}

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-5">
                <a href="{{ route('dashboard.category.index') }}"
                    class="px-6 py-2 text-sm font-medium border rounded-lg border-border-color text-text-secondary hover:bg-page-bg">
                    {{ __('dashboard/category.create.cancel') }}
                </a>
                <button type="submit" id="createCategoryBtn"
                    class="px-6 py-2 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90 disabled:opacity-50">
                    {{ __('dashboard/category.create.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection
