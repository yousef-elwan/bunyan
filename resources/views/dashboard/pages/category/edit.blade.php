@extends('dashboard.layouts.default')

@section('title', __('dashboard/category.edit.page_title'))

@push('css_or_js')
    <style>
        .drag-over {
            border-color: #4f46e5;
            background-color: #eef2ff;
        }

        .marked-for-deletion img {
            opacity: 0.4;
            border: 2px solid #dc2626;
        }
    </style>
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.pageData = window.AppConfig.pageData || {};
        Object.assign(window.AppConfig.routes, {
            'api.category.update': "{{ route('api.category.update', ['category' => $category->id]) }}",
            'dashboard.category.index': "{{ route('dashboard.category.index') }}",
        });
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/category.edit')));
        window.AppConfig.pageData.locales = @json($langs->pluck('locale')->toArray());
    </script>
    @vite(['resources/js/alpine/dashboard/category/edit.js'])
@endpush

@section('content')
    <div class="bg-page-bg">
        {{-- <div class="p-4 sm:p-6 lg:p-8 bg-page-bg"> --}}
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
                    {{ __('dashboard/category.edit.page_title') }}
                </h1>
            </div>
        </div>

        <form id="editCategoryForm" class="space-y-6">
            @csrf
            <!-- Primary Info -->
            <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/category.edit.primaryinfo') }}</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    @foreach ($langs as $lang)
                        <div class="mb-5">
                            <label for="name_{{ $lang->locale }}"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/category.edit.entername') }}
                                ({{ $lang->name }})
                                <span class="text-danger">*</span></label>
                            <input type="text" name="name_{{ $lang->locale }}" id="name_{{ $lang->locale }}"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                value="{{ $category->translations->where('locale', $lang->locale)->first()?->name ?? '' }}"
                                @if ($lang->direction === 'rtl') dir="rtl" @endif required>
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="name_{{ $lang->locale }}_error">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Photo -->
            <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color" id="image-drop-area">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/category.edit.photo') }}</h2>
                @if ($category->image)
                    <div class="mb-4">
                        <h3 class="mb-2 text-sm font-medium text-text-primary">
                            {{ __('dashboard/category.edit.current_image') }}</h3>
                        <div id="existingImageContainer" class="relative inline-block w-28 h-28 group">
                            <img src="{{ $category->image_url }}" alt="Current Image"
                                class="object-cover w-full h-full rounded-md border border-border-color transition-opacity">
                            <button type="button"
                                class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 bg-danger rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none"
                                title="{{ __('dashboard/category.edit.delete_image_tooltip') }}">×</button>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="imageUpload"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg cursor-pointer bg-accent-primary hover:bg-opacity-90">
                        <i class="fas fa-upload"></i>
                        {{ $category->image_url ? __('dashboard/category.edit.chosePhoto') : __('dashboard/category.create.chosePhoto') }}
                    </label>
                    <input type="file" name="image" id="imageUpload" class="hidden" accept="image/*">
                    <p class="mt-2 text-xs text-text-secondary">{{ __('dashboard/category.edit.note') }}</p>
                    <div class="mt-1 text-sm text-danger invalid-feedback" id="image_error"></div>
                </div>
                <div id="newImagePreviewContainer"
                    class="flex flex-wrap gap-4 p-4 mt-4 border-2 border-dashed rounded-lg border-border-color min-h-[140px] transition-colors duration-300">
                    <p class="self-center w-full text-sm text-center text-text-secondary">
                        {{ __('dashboard/category.edit.no_new_image_preview') }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-5">
                <a href="{{ route('dashboard.category.index') }}"
                    class="px-6 py-2 text-sm font-medium border rounded-lg border-border-color text-text-secondary hover:bg-page-bg">{{ __('dashboard/category.edit.cancel') }}</a>
                <button type="submit" id="updateCategoryBtn"
                    class="px-6 py-2 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90 disabled:opacity-50">{{ __('dashboard/category.edit.save') }}</button>
            </div>
        </form>
    </div>
@endsection
