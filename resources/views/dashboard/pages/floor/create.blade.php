@extends('dashboard.layouts.default')

@section('title', __('dashboard/floor.create.page_title'))

@push('css_or_js')
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.pageData = window.AppConfig.pageData || {};
        Object.assign(window.AppConfig.routes, {
            'api.floor.store': "{{ route('api.floor.store') }}",
            'dashboard.floor.index': "{{ route('dashboard.floor.index') }}",
        });
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/floor.create')));
        window.AppConfig.pageData.locales = @json($langs->pluck('locale')->toArray());
    </script>
    @vite(['resources/js/alpine/dashboard/floor/create.js'])
@endpush

@section('content')
    {{-- <div class="p-4 sm:p-6 lg:p-8 bg-page-bg"> --}}
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
                    {{ __('dashboard/floor.create.page_title') }}
                </h1>
            </div>
        </div>

        <form id="createFloorForm" class="space-y-6">
            @csrf
            {{-- <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/floor.create.primaryinfo') }}</h2>
                @foreach ($langs as $lang)
                    <div class="mb-5">
                        <label for="name_{{ $lang->locale }}"
                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/floor.create.entername') }}
                            ({{ strtoupper($lang->name) }})
                            <span class="text-danger">*</span></label>
                        <input type="text" name="name_{{ $lang->locale }}" id="name_{{ $lang->locale }}"
                            class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                            @if ($lang->direction === 'rtl') dir="rtl" @endif required>
                        <div class="mt-1 text-sm text-danger invalid-feedback" id="name_{{ $lang->locale }}_error"></div>
                    </div>
                @endforeach
            </div> --}}
            <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/floor.create.primaryinfo') }}</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    @foreach ($langs as $lang)
                        <div class="mb-5">
                            <label for="name_{{ $lang->locale }}"
                                class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/floor.create.entername') }}
                                ({{ strtoupper($lang->name) }})
                                <span class="text-danger">*</span></label>
                            <input type="text" name="name_{{ $lang->locale }}" id="name_{{ $lang->locale }}"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                @if ($lang->direction === 'rtl') dir="rtl" @endif required>
                            <div class="mt-1 text-sm text-danger invalid-feedback" id="name_{{ $lang->locale }}_error">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-5 bg-card-bg rounded-xl shadow-custom border border-border-color">
                <h2 class="pb-4 mb-6 text-lg font-semibold border-b border-border-color text-text-primary">
                    {{ __('dashboard/floor.create.details') }}</h2>
                <div>
                    <label for="value"
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/floor.create.value') }}</label>
                    <input type="number" id="value" name="value"
                        class="block w-full md:w-1/3 text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                        min="0">
                    <div class="mt-1 text-sm text-danger invalid-feedback" id="value_error"></div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-5">
                <a href="{{ route('dashboard.floor.index') }}"
                    class="px-6 py-2 text-sm font-medium border rounded-lg border-border-color text-text-secondary hover:bg-page-bg">{{ __('dashboard/floor.create.cancel') }}</a>
                <button type="submit" id="createFloorBtn"
                    class="px-6 py-2 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90 disabled:opacity-50">{{ __('dashboard/floor.create.save') }}</button>
            </div>
        </form>
    </div>
@endsection
