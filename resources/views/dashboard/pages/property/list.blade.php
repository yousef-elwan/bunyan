@extends('dashboard.layouts.default')

@section('title', $user->isAdmin() ? __('dashboard/property.list.page_title_admin') :
    __('dashboard/property.list.page_title'))

    @php
        $direction = config('app.locale') == 'ar' ? 'rtl' : 'ltr';
    @endphp

    @push('css_or_js')
        <style>
            /* استثناء للرسوم المتحركة لشريط التحميل */
            #loadingBar {
                transition: width 0.3s;
            }

            /* تنسيق الجدول */
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

            /* بطاقات الجوال */
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

            /* تنسيق البطاقات */
            #propertiesCardsContainer .card {
                transition: box-shadow 0.3s ease;
            }

            #propertiesCardsContainer .card:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }

            /* تنسيق أزرار التصفح */
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
                'api.properties-statuses': "{{ route('api.properties-statuses') }}",
                'api.dashboard-properties': "{{ route('api.dashboard-properties') }}",
                'dashboard.properties.edit': "{{ route('dashboard.properties.edit', ['property' => ':property']) }}",
                'api.dashboard-properties.destroy': "{{ route('api.dashboard-properties.destroy', ['property' => ':property']) }}",
                'api.dashboard-properties.update': "{{ route('api.dashboard-properties.update', ['property' => ':property']) }}",
                'api.users.show': "{{ route('api.users.show', ['user' => ':user']) }}",
                'api.users.blacklist': "{{ route('api.users.blacklist', ['user' => ':user']) }}",
                'api.users.unblacklist': "{{ route('api.users.unblacklist', ['user' => ':user']) }}"
            });
            // Object.assign(window.AppConfig.i18n, @json(__('dashboard/property.list')));
            Object.assign(window.AppConfig.i18n, @json(__('dashboard/property.list')), {
                'confirm_status_change_title': '{{ __('dashboard/property.list.confirm_status_change_title') }}',
                'confirm_status_change_text': '{{ __('dashboard/property.list.confirm_status_change_text') }}',
                'confirm_button': '{{ __('dashboard/property.list.confirm_button') }}',
                'cancel_button': '{{ __('dashboard/property.list.cancel_button') }}',
                'update_success_title': '{{ __('dashboard/property.list.update_success_title') }}',
                'update_success_text': '{{ __('dashboard/property.list.update_success_text') }}',
                'confirm_delete_title': '{{ __('dashboard/property.list.confirm_delete_title') }}',
                'confirm_delete_text': '{{ __('dashboard/property.list.confirm_delete_text') }}',
                'confirm_delete_button': '{{ __('dashboard/property.list.confirm_delete_button') }}',
                'delete_success_title': '{{ __('dashboard/property.list.delete_success_title') }}',
                'delete_success_text': '{{ __('dashboard/property.list.delete_success_text') }}',
                'error_loading_properties': '{{ __('dashboard/property.list.error_loading_properties') }}',
                'pagination_info': '{{ __('dashboard/property.list.pagination_info') }}'
            });
        </script>
        @vite('resources/js/alpine/dashboard/property/list.js')
    @endpush

@section('content')
    <div class="flex flex-col min-h-screen bg-page-bg" x-data="propertyFilters()">

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
                    {{ $user->isAdmin() ? __('dashboard/property.list.page_title_admin') : __('dashboard/property.list.page_title') }}
                </h1>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-4">
            {{-- <div class="flex flex-wrap items-center gap-3 p-4"> --}}
            <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                <!-- Left Side: Filters -->
                <div class="flex-grow flex flex-wrap items-center gap-3">
                    {{-- Quick Filters --}}
                    <select class="hidden sm:block w-40 p-2 text-sm border rounded-lg border-border-color"
                        x-model="filters.city_id">
                        <option value="">{{ __('dashboard/property.list.select_city') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                        @endforeach
                    </select>

                    <select class="hidden sm:block w-40 p-2 text-sm border rounded-lg border-border-color"
                        x-model="filters.category_id">
                        <option value="">{{ __('dashboard/property.list.select_category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>

                    <select class="hidden sm:block w-40 p-2 text-sm border rounded-lg border-border-color"
                        x-model="filters.type_id">
                        <option value="">{{ __('dashboard/property.list.select_type') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                        @endforeach
                    </select>

                    @if ($user->is_admin)
                        <select class="hidden sm:block w-40 p-2 text-sm border rounded-lg border-border-color"
                            x-model="filters.user_id">
                            <option value="">{{ __('dashboard/property.list.select_owner') }}</option>
                            @foreach ($owners as $owner)
                                <option value="{{ $owner['id'] }}">{{ $owner['name'] }}</option>
                            @endforeach
                        </select>
                    @endif

                    {{-- search --}}
                    <button
                        class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90"
                        @click="applyFilters()">
                        <i class="fas fa-search"></i>
                        {{ __('dashboard/property.list.search_button') }}
                    </button>

                    {{-- refresh --}}
                    <button
                        class="flex md:hidden items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90"
                        @click="applyFilters()">
                        <i class="fas fa-refresh"></i>
                        {{ __('dashboard/property.list.refresh_button') }}
                    </button>

                    <button
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium border rounded-lg text-accent-primary border-accent-primary hover:bg-accent-primary-light"
                        @click="showAdvancedFilters = true">
                        <i class="fas fa-filter"></i>
                        <span class="sm:hidden">{{ __('dashboard/property.list.advanced_search_button') }}</span>
                        <span class="hidden sm:inline">{{ __('dashboard/property.list.advanced_search_button') }}</span>
                    </button>
                </div>
                <!-- Right Side: Add New Button -->
                @if (!$user->isAdmin())
                    <div class="flex-shrink-0">
                        <a href="{{ route('dashboard.properties.create') }}"
                            class="flex items-center justify-center gap-2 h-10 w-10 sm:w-auto sm:px-4 text-white rounded-lg bg-accent-primary hover:bg-opacity-90 transition-all duration-200">
                            <i class="fas fa-plus"></i>
                            <span
                                class="hidden sm:inline font-medium text-sm">{{ __('dashboard/property.list.add_new_button') }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Advanced Filter Drawer -->
        <d0iv x-show="showAdvancedFilters" x-cloak class="fixed inset-0 z-40 bg-black/50"
            @click="showAdvancedFilters = false"></d0iv>

        <div class="fixed top-0 {{ $direction === 'rtl' ? 'left-0' : 'right-0' }} z-50 flex flex-col w-full h-full max-w-md bg-card-bg shadow-xl"
            x-show="showAdvancedFilters" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="{{ $direction === 'rtl' ? '-translate-x-full' : 'translate-x-full' }}"
            x-transition:enter-end="{{ $direction === 'rtl' ? '-translate-x-0' : 'translate-x-0' }}"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="{{ $direction === 'rtl' ? 'translate-x-0' : 'translate-x-0' }}"
            x-transition:leave-end="{{ $direction === 'rtl' ? '-translate-x-full' : 'translate-x-full' }}">

            <div class="flex items-center justify-between p-4 border-b border-border-color">
                <h2 class="text-lg font-semibold text-text-primary">
                    {{ __('dashboard/property.list.advanced_search_title') }}</h2>
                <button @click="showAdvancedFilters = false" class="p-2 rounded-lg hover:bg-page-bg">
                    <i class="text-xl text-text-secondary fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 p-4 overflow-y-auto">
                <!-- Price Range -->
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.price_range') }}</label>
                    <div class="flex gap-2">
                        <input type="number" x-model="filters.min_price"
                            placeholder="{{ __('dashboard/property.list.min_price') }}"
                            class="w-full p-2 text-sm border rounded-lg border-border-color">
                        <input type="number" x-model="filters.max_price"
                            placeholder="{{ __('dashboard/property.list.max_price') }}"
                            class="w-full p-2 text-sm border rounded-lg border-border-color">
                    </div>
                </div>


                <!-- Owner -->
                <div class="mb-4 block sm:hidden">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_owner') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.user_id">
                        <option value="">{{ __('dashboard/property.list.select_owner') }}</option>
                        @foreach ($owners as $owner)
                            <option value="{{ $owner['id'] }}">{{ $owner['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- City -->
                <div class="mb-4 block sm:hidden">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_city') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.city_id">
                        <option value="">{{ __('dashboard/property.list.select_city') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div class="mb-4 block sm:hidden">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_category') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.category_id">
                        <option value="">{{ __('dashboard/property.list.select_category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div class="mb-4 block sm:hidden">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_type') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.type_id">
                        <option value="">{{ __('dashboard/property.list.select_type') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Floor -->
                {{--  block sm:hidden --}}
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_floor') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.floor_id">
                        <option value="">{{ __('dashboard/property.list.select_floor') }}</option>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor['id'] }}">{{ $floor['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_status') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.status_id">
                        <option value="">{{ __('dashboard/property.list.select_status') }}</option>
                        @foreach ($propertyStatus as $status)
                            <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Orientation -->
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_orientation') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color"
                        x-model="filters.orientation_id">
                        <option value="">{{ __('dashboard/property.list.select_orientation') }}</option>
                        @foreach ($orientations as $orientation)
                            <option value="{{ $orientation['id'] }}">{{ $orientation['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Contract Type -->
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_contract_type') }}</label>
                    <select class="w-full p-2 text-sm border rounded-lg border-border-color"
                        x-model="filters.contract_type_id">
                        <option value="">{{ __('dashboard/property.list.select_contract_type') }}</option>
                        @foreach ($contractTypes as $contractType)
                            <option value="{{ $contractType['id'] }}">{{ $contractType['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Amenities -->
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/property.list.filter_amenities') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($amenities as $amenity)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="{{ $amenity['id'] }}" x-model="filters.amenities"
                                    class="rounded border-border-color text-accent-primary focus:ring-accent-primary">
                                <span class="text-sm text-text-secondary">{{ $amenity['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 p-4 border-t border-border-color">
                <button class="px-4 py-2 font-medium rounded-lg text-text-secondary hover:bg-page-bg"
                    @click="resetFilters()">
                    {{ __('dashboard/property.list.reset_button') }}
                </button>
                <button class="px-4 py-2 font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90"
                    @click="applyFilters(); showAdvancedFilters = false;">
                    {{ __('dashboard/property.list.apply_button') }}
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
                            {{-- ... header cells ... --}}
                            <th class="p-3"><input type="checkbox" id="selectAllPropertiesCheckbox"></th>
                            <th class="p-3">{{ __('dashboard/property.list.th_image') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_title') }}</th>
                            @if ($user->is_admin)
                                <th class="p-3">{{ __('dashboard/property.list.th_owner') }}</th>
                            @endif
                            <th class="p-3">{{ __('dashboard/property.list.th_city') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_type') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_category') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_status') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_views') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_price') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_date') }}</th>
                            <th class="p-3">{{ __('dashboard/property.list.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-border-color">
                        {{-- Skeleton Loader for Table --}}
                        <template x-if="isLoading">
                            <tr class="animate-pulse" x-ref="skeletonRow" x-init="$nextTick(() => { for (i = 0; i < 4; i++) $el.parentElement.appendChild($el.cloneNode(true)) })">
                                <td class="p-3">
                                    <div class="w-4 h-4 bg-gray-200 rounded"></div>
                                </td>
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
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-8 bg-gray-200 rounded w-full"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                </td>
                                <td class="p-3">
                                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                                </td>
                                <td class="p-3">
                                    <div class="flex gap-2">
                                        <div class="w-6 h-6 bg-gray-200 rounded"></div>
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
                {{-- Skeleton Loader for Cards --}}
                <template x-if="isLoading">
                    <div class="p-4 space-y-3 bg-white border rounded-lg shadow-sm border-border-color animate-pulse"
                        x-ref="skeletonCard" x-init="$nextTick(() => { for (i = 0; i < 4; i++) $el.parentElement.appendChild($el.cloneNode(true)) })">
                        <div class="flex gap-3">
                            <div class="w-20 h-20 bg-gray-200 rounded"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- No Properties Message -->
            <p id="noPropertiesMessage" x-show="!isLoading && allPropertiesData.length === 0" x-cloak
                class="py-10 my-4 text-center text-gray-500">
                {{ __('dashboard/property.list.no_properties_found') }}
            </p>
        </div>

        <!-- Pagination -->
        <div id="paginationControls" class="flex flex-wrap justify-center gap-1 py-4"
            x-show="!isLoading && allPropertiesPaginationData && allPropertiesPaginationData.last_page > 1" x-cloak>
        </div>

        @if (!$user->isAdmin())
            <div x-show="isActionSheetOpen" x-cloak class="fixed inset-0 z-30 flex items-end">
                <!-- Backdrop -->
                <div x-show="isActionSheetOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="isActionSheetOpen = false"
                    class="fixed inset-0 bg-black/40">
                </div>

                <!-- Sheet Content -->
                <div x-show="isActionSheetOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                    class="relative w-full bg-page-bg rounded-t-2xl shadow-lg p-4">

                    <div class="space-y-2">
                        <!-- Dynamic Actions will be rendered here -->
                        <template x-for="action in propertyActions" :key="action.label">
                            <button @click="action.handler(); isActionSheetOpen = false" :class="action.classes"
                                class="w-full flex items-center gap-4 p-3 text-lg rounded-lg text-left">
                                <i :class="action.icon" class="w-6 text-center"></i>
                                <span x-text="action.label"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Cancel Button -->
                    <div class="mt-4">
                        <button @click="isActionSheetOpen = false"
                            class="w-full bg-card-bg p-3 text-lg rounded-lg font-semibold text-text-primary">
                            {{ __('dashboard/property.list.cancel_button') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div x-show="isOwnerModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div x-show="isOwnerModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="isOwnerModalOpen = false" class="fixed inset-0 bg-black/50">
            </div>

            <!-- Modal Content -->
            <div x-show="isOwnerModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md bg-card-bg rounded-lg shadow-xl p-6">

                <!-- Header -->
                <div class="flex items-start justify-between">
                    <h3 class="text-xl font-semibold text-text-primary" x-text="translate('owner_details_title')"></h3>
                    <button @click="isOwnerModalOpen = false"
                        class="p-1 -m-1 text-text-secondary hover:text-text-primary">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="mt-4">
                    <!-- Loading Spinner -->
                    <template x-if="isOwnerLoading">
                        <div class="flex justify-center items-center h-48">
                            <i class="fas fa-spinner fa-spin fa-2x text-accent-primary"></i>
                        </div>
                    </template>

                    <!-- Owner Details -->
                    <template x-if="!isOwnerLoading && selectedOwner">
                        <div class="space-y-4">
                            <!-- Blacklisted Status Alert -->
                            <div x-show="selectedOwner.is_blacklisted"
                                class="p-4 text-sm text-danger bg-danger-light rounded-lg flex items-center gap-3"
                                role="alert">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                <div>
                                    <p class="font-bold">
                                        {{ __('dashboard/property.list.user_is_blacklisted', [], 'ar') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <img :src="selectedOwner.image_url || 'https://via.placeholder.com/80'" alt="Owner Avatar"
                                    class="w-20 h-20 rounded-full object-cover border-2 border-border-color">
                                <div>
                                    <p class="text-lg font-bold text-text-primary" x-text="selectedOwner.name"></p>
                                    <p class="text-sm text-text-secondary" x-text="selectedOwner.email"></p>
                                </div>
                            </div>
                            <!-- يمكنك إضافة المزيد من التفاصيل هنا -->
                            <div class="text-sm text-text-secondary border-t border-border-color pt-3">
                                <p><strong>{{ __('dashboard/property.list.phone_number') }}:</strong> <span
                                        x-text="selectedOwner.mobile || 'N/A'"></span></p>
                                <p><strong>{{ __('dashboard/property.list.member_since') }}:</strong> <span
                                        x-text="new Date(selectedOwner.created_at).toLocaleDateString()"></span></p>
                            </div>
                        </div>
                    </template>
                </div>


                <!-- Footer -->
                <div class="mt-6 flex justify-end gap-3" x-show="!isOwnerLoading && selectedOwner">
                    {{-- <button @click="isOwnerModalOpen = false"
                        class="px-4 py-2 text-sm font-medium rounded-lg text-text-secondary hover:bg-page-bg">
                        {{ __('dashboard/property.list.cancel_button') }}
                    </button>
                    <button @click="blacklistOwner()"
                        class="px-4 py-2 text-sm font-medium text-white bg-danger rounded-lg hover:bg-opacity-90 flex items-center gap-2">
                        <i class="fas fa-ban"></i>
                        <span x-text="translate('blacklist_owner_button')"></span>
                    </button> --}}
                    <button type="button" @click="isOwnerModalOpen = false"
                        class="px-5 py-2 text-sm font-medium rounded-lg text-text-secondary bg-white border border-border-color hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                        {{ __('dashboard/property.list.cancel_button', [], 'ar') }}
                    </button>

                    <!-- Blacklist Button (Conditional) -->
                    <button type="button" x-show="!selectedOwner.is_blacklisted" @click="blacklistOwner()"
                        class="px-5 py-2 text-sm font-medium text-white bg-danger rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-danger flex items-center gap-2">
                        <i class="fas fa-ban"></i>
                        <span x-text="translate('blacklist_owner_button')"></span>
                    </button>

                    <!-- Reactivate Button (Conditional) -->
                    <button type="button" x-show="selectedOwner.is_blacklisted" @click="unblacklistOwner()"
                        class="px-5 py-2 text-sm font-medium text-white bg-success rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span x-text="translate('reactivate_owner_button')"></span>
                    </button>
                </div>
            </div>
        </div>




    </div>

@endsection
