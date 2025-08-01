@extends('dashboard.layouts.default')

@section('title', __('dashboard/condition.list.page_title'))

@push('css_or_js')
    {{-- لم نعد بحاجة لملفات CSS خارجية --}}
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        window.AppConfig.i18n = window.AppConfig.i18n || {};
        Object.assign(window.AppConfig.routes, {
            'dashboard.condition.edit': "{{ route('dashboard.condition.edit', ['condition' => ':condition']) }}",
            'api.condition.destroy': "{{ route('api.condition.destroy', ['condition' => ':condition']) }}",
            'api.condition': "{{ route('api.condition') }}",
        });
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/condition.list')), {
            'pagination_info': '{{ __('dashboard/condition.list.pagination_info') }}',
            'edit_tooltip': '{{ __('dashboard/condition.list.edit_tooltip') }}',
            'delete_tooltip': '{{ __('dashboard/condition.list.delete_tooltip') }}',
            'cancel_button': '{{ __('dashboard/condition.list.cancel_button') }}',
        });
    </script>
    @vite(['resources/js/alpine/dashboard/condition/list.js'])
@endpush

@section('content')
    {{-- <div class="p-4 sm:p-6 lg:p-8 bg-page-bg" x-data="conditionList()"> --}}
    <div class="flex flex-col min-h-screen bg-page-bg" x-data="conditionList()">

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
                    {{ __('dashboard/condition.list.page_title') }}
                </h1>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-4">
            {{-- <div class="flex flex-wrap items-center gap-3 p-4"> --}}
            <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                <!-- Left Side: Filters -->
                <div class="flex-grow flex flex-wrap items-center gap-3">
                    {{-- refresh --}}
                    <button
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90"
                        @click="fetchConditions()">
                        <i class="fas fa-refresh"></i>
                        {{ __('dashboard/condition.list.refresh_button') }}
                    </button>
                </div>
                <!-- Right Side: Add New Button -->
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard.condition.create') }}"
                        class="flex items-center justify-center gap-2 h-10 w-10 sm:w-auto sm:px-4 text-white rounded-lg bg-accent-primary hover:bg-opacity-90 transition-all duration-200">
                        <i class="fas fa-plus"></i>
                        <span
                            class="hidden sm:inline font-medium text-sm">{{ __('dashboard/condition.list.add_new_button') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="bg-card-bg rounded-xl shadow-custom border border-border-color">
            <div id="paginationInfo" class="p-4 text-sm text-text-secondary border-b border-border-color"></div>

            <!-- Desktop Table -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-sm text-left" id="conditionsDataTable">
                    <thead class="text-xs uppercase bg-gray-50 text-text-secondary">
                        <tr>
                            <th class="p-4">{{ __('dashboard/condition.list.name') }}</th>
                            <th class="p-4">{{ __('dashboard/condition.list.addon') }}</th>
                            <th class="p-4 text-center">{{ __('dashboard/condition.list.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-color">
                        <template x-if="isLoading">
                            <tr class="animate-pulse" x-ref="skeletonRow" x-init="$nextTick(() => { for (i = 0; i < 4; i++) $el.parentElement.appendChild($el.cloneNode(true)) })">
                                <td class="p-4">
                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                </td>
                                <td class="p-4">
                                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                                </td>
                                <td class="p-4">
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center gap-3">
                                        <div class="w-6 h-6 bg-gray-200 rounded"></div>
                                        <div class="w-6 h-6 bg-gray-200 rounded"></div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div id="conditionCardsContainer" class="p-4 space-y-4 md:hidden">
                <template x-if="isLoading">
                    <div class="p-4 bg-white border rounded-xl shadow-sm border-border-color animate-pulse"
                        x-ref="skeletonCard" x-init="$nextTick(() => { for (i = 0; i < 4; i++) $el.parentElement.appendChild($el.cloneNode(true)) })">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="!isLoading && allConditionData.length === 0" x-cloak class="py-10 text-center">
                <p class="text-text-secondary">{{ __('dashboard/condition.list.no_data_found') }}</p>
            </div>
        </div>

        <div id="paginationControls" class="flex flex-wrap justify-center gap-2 py-4"
            x-show="!isLoading && paginationData && paginationData.total > paginationData.per_page" x-cloak></div>

        <!-- Action Sheet for Mobile -->
        <div x-show="isActionSheetOpen" x-cloak class="fixed inset-0 z-30 flex items-end">
            <div x-show="isActionSheetOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="isActionSheetOpen = false"
                class="fixed inset-0 bg-black/40"></div>
            <div x-show="isActionSheetOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full" class="relative w-full bg-page-bg rounded-t-2xl shadow-lg p-4">
                <div class="space-y-2">
                    <template x-for="action in conditionActions" :key="action.label">
                        <button @click="action.handler(); isActionSheetOpen = false" :class="action.classes"
                            class="w-full flex items-center gap-4 p-3 text-lg rounded-lg text-left">
                            <i :class="action.icon" class="w-6 text-center"></i><span x-text="action.label"></span>
                        </button>
                    </template>
                </div>
                <div class="mt-4">
                    <button @click="isActionSheetOpen = false"
                        class="w-full bg-card-bg p-3 text-lg rounded-lg font-semibold text-text-primary">{{ __('dashboard/condition.list.cancel_button') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
