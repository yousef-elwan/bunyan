@extends('dashboard.layouts.default')

@section('title', __('dashboard/property.list.favorites_page_title'))

@php
    $direction = config('app.locale') == 'ar' ? 'rtl' : 'ltr';
@endphp

@push('css_or_js')
    <style>
        #loadingBar {
            transition: width 0.3s;
        }

        #propertiesDataTable th,
        #propertiesDataTable td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        #propertiesDataTable tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        #propertiesDataTable tbody tr:last-child {
            border-bottom: none;
        }

        @media (max-width: 767px) {
            #propertiesDataTable {
                display: none;
            }

            #propertiesCardsContainer {
                display: block;
            }
        }

        @media (min-width: 768px) {
            #propertiesCardsContainer {
                display: none;
            }
        }

        #propertiesCardsContainer .card {
            transition: box-shadow 0.3s ease;
        }

        #propertiesCardsContainer .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        #paginationControls button {
            min-width: 36px;
            transition: background-color 0.2s, color 0.2s;
        }

        #paginationControls button:hover:not(:disabled) {
            background-color: #e2e8f0;
        }

        #paginationControls button.current-page {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
    </style>
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        window.AppConfig.i18n = window.AppConfig.i18n || {};
        Object.assign(window.AppConfig.routes, {
            'api.dashboard-properties': "{{ route('api.properties') }}",
            'api.properties.toggle-favorite': "{{ route('api.properties.toggle-favorite', ['property' => ':property']) }}",
            'properties.details': "{{ route('properties.details', ['property' => ':property']) }}",
        });
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/property.list')), {
            'remove_from_favorites': '{{ __('dashboard/property.list.remove_from_favorites') }}',
            'confirm_remove_favorite_title': '{{ __('dashboard/property.list.confirm_remove_favorite_title') }}',
            'confirm_remove_favorite_text': '{{ __('dashboard/property.list.confirm_remove_favorite_text') }}',
            'remove_favorite_success_title': '{{ __('dashboard/property.list.remove_favorite_success_title') }}',
            'remove_favorite_success_text': '{{ __('dashboard/property.list.remove_favorite_success_text') }}',
            'error_loading_properties': '{{ __('dashboard/property.list.error_loading_properties') }}',
            'pagination_info': '{{ __('dashboard/property.list.pagination_info') }}',
            'view_details': '{{ __('dashboard/property.list.view_details') }}'
        });
    </script>
    @vite('resources/js/alpine/dashboard/property/favorites.js')
@endpush

@section('content')
    <div class="flex flex-col min-h-screen bg-page-bg" x-data="propertyFilters()">

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
                    {{ __('dashboard/property.list.favorites_page_title') }}
                </h1>

                <button @click="fetchProperties()"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90">
                    <i class="fas fa-refresh"></i>
                    {{ __('dashboard/property.list.refresh_button') }}
                </button>
            </div>
        </div>

        <!-- Results Area -->
        <div class="bg-card-bg rounded-lg shadow-sm">
            <!-- Pagination Info -->
            <div id="paginationInfo" class="p-4 text-sm text-text-secondary border-b border-border-color"></div>

            <!-- Table Container for Desktop -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-sm text-left text-text-secondary" id="propertiesDataTable">
                    <thead class="text-xs uppercase bg-gray-50 text-text-secondary">
                        <tr>
                            <th class="p-3">{{ __('dashboard/property.list.th_image') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_title') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_city') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_type') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_category') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_price') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-border-color">
                        <template x-if="isLoading">
                            <tr class="animate-pulse" x-ref="skeletonRow" x-init="$nextTick(() => { for (i = 0; i < 4; i++) $el.parentElement.appendChild($el.cloneNode(true)) })">
                                <td class="p-3">
                                    <div class="w-16 h-16 bg-gray-200 rounded"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                </td>
                                <td class="p-3">
                                    <div class="flex gap-2">
                                        <div class="w-6 h-6 bg-gray-200 rounded"></div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Cards Container for Mobile -->
            <div id="propertiesCardsContainer" class="p-3 space-y-3 md:hidden">
                <template x-if="isLoading">
                    <div class="p-4 space-y-3 bg-white border rounded-lg shadow-sm border-border-color animate-pulse"
                        x-ref="skeletonCard" x-init="$nextTick(() => { for (i = 0; i < 4; i++) $el.parentElement.appendChild($el.cloneNode(true)) })">
                        <div class="flex gap-3">
                            <div class="w-20 h-20 bg-gray-200 rounded"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- No Properties Message -->
            <p id="noPropertiesMessage" x-show="!isLoading && allPropertiesData.length === 0" x-cloak
                class="py-10 my-4 text-center text-gray-500">
                {{ __('dashboard/property.list.no_favorites_found') }}
            </p>
        </div>

        <!-- Pagination -->
        <div id="paginationControls" class="flex flex-wrap justify-center gap-1 py-4"
            x-show="!isLoading && allPropertiesPaginationData && allPropertiesPaginationData.last_page > 1" x-cloak>
        </div>
    </div>
@endsection
