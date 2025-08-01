@extends('dashboard.layouts.default')
@php
    $direction = config('app.locale') == 'ar' ? 'rtl' : 'ltr';
@endphp

@push('css_or_js')
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        window.AppConfig.i18n = window.AppConfig.i18n || {};

        Object.assign(window.AppConfig.routes, {
            'api.reports': "{{ route('api.reports') }}",
            'api.reports.update_status': "{{ route('api.reports.update_status', ['report' => ':reportId']) }}",
            'dashboard.properties.edit': "{{ route('dashboard.properties.edit', ['property' => ':propertyId']) }}"
        });

        Object.assign(window.AppConfig.i18n, @json(__('dashboard/reports.list')));
        window.reportStatuses = @json($report_statuses);
    </script>
    @vite('resources/js/alpine/dashboard/report/list.js')

    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: var(--warning-light, #fffbeb);
            color: var(--warning, #f59e0b);
        }

        .status-in_progress {
            background-color: var(--warning-light, #ebffff);
            color: var(--warning, #0bc6f5);
        }

        .status-resolved {
            background-color: var(--success-light, #f0fdf4);
            color: var(--success, #16a34a);
        }

        .status-rejected {
            background-color: #f3f4f6;
            color: #6b7280;
        }


        .text-success {
            color: #16a34a;
        }

        .text-success-dark {
            color: #15803d;
        }

        .text-gray-700 {
            color: #374151;
        }

        .hover\:bg-green-100:hover {
            background-color: #dcfce7;
        }

        .hover\:bg-gray-100:hover {
            background-color: #f3f4f6;
        }

        .btn-primary {
            @apply bg-accent-primary text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center justify-center transition-colors hover:bg-opacity-90 disabled:opacity-50 disabled:cursor-not-allowed;
        }

        .btn-cancel {
            @apply bg-slate-600 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center justify-center transition-colors hover:bg-slate-700;
        }

        .btn-secondary-outline {
            @apply bg-transparent text-text-secondary font-semibold rounded-lg inline-flex items-center justify-center transition-colors border border-slate-300 hover:bg-slate-100;
        }

        .btn-sm {
            @apply py-1 px-3 text-xs;
        }
    </style>
@endpush

@section('content')

    <div class="flex flex-col min-h-screen bg-page-bg" x-data="reportsManagement()" x-init="report_statuses = window.reportStatuses;
    init();">

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
                    {{ __('dashboard/reports.list.page_title') }}
                </h1>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex-grow flex items-center gap-3">
                    <div class="relative flex-1 max-w-xs">
                        <input type="text" x-model.debounce.300ms="filters.search" @input="applyFilters()"
                            placeholder="{{ __('dashboard/reports.list.search_placeholder') }}"
                            class="w-full p-2 pl-10 text-sm border rounded-lg border-border-color">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>

                    <div class="hidden md:flex items-center gap-3">
                        {{-- <select class="w-32 p-2 text-sm border rounded-lg border-border-color" x-model="filters.status_id"
                            @change="applyFilters()">
                            <option value="">{{ __('dashboard/reports.list.all_statuses') }}</option>
                            @foreach ($report_statuses as $status)
                                <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                            @endforeach
                        </select> --}}
                        <select class="w-32 p-2 text-sm border rounded-lg border-border-color"
                            x-model="filters.report_status_id" @change="applyFilters()">
                            <option value="">{{ __('dashboard/reports.list.all_statuses') }}</option>
                            @foreach ($report_statuses as $status)
                                <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                            @endforeach
                        </select>
                        <select class="w-32 p-2 text-sm border rounded-lg border-border-color" x-model="filters.type_id"
                            @change="applyFilters()">
                            <option value="">{{ __('dashboard/reports.list.all_types') }}</option>
                            @foreach ($report_types as $type)
                                <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                            @endforeach
                        </select>

                        <select class="w-32 p-2 text-sm border rounded-lg border-border-color" x-model="filters.owner_id"
                            @change="applyFilters()">
                            <option value="">{{ __('dashboard/reports.list.th_owner') }}</option>
                            @foreach ($owners as $owner)
                                <option value="{{ $owner['id'] }}">{{ $owner['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button @click="showAdvancedFilters = true"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium border rounded-lg text-accent-primary border-accent-primary hover:bg-accent-primary-light">
                        <i class="fas fa-filter"></i>
                        <span class="hidden md:inline">{{ __('dashboard/reports.list.advanced_search_button') }}</span>
                    </button>
                </div>

                <div class="flex-shrink-0">
                    <button @click="fetchReports(currentPage)"
                        class="flex items-center justify-center gap-2 h-10 w-10 sm:w-auto sm:px-4 text-white rounded-lg bg-accent-primary hover:bg-opacity-90">
                        <i class="fas fa-sync-alt" :class="{ 'animate-spin': isLoading }"></i>
                        <span
                            class="hidden sm:inline font-medium text-sm">{{ __('dashboard/reports.list.refresh_button') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Filter Drawer -->
        <div x-show="showAdvancedFilters" x-cloak class="fixed inset-0 z-40 bg-black/50"
            @click="showAdvancedFilters = false">
        </div>
        <div class="fixed top-0 {{ $direction === 'rtl' ? 'left-0' : 'right-0' }} z-50 flex flex-col w-full h-full max-w-md bg-card-bg shadow-xl"
            x-show="showAdvancedFilters" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="{{ $direction === 'rtl' ? '-translate-x-full' : 'translate-x-full' }}"
            x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="{{ $direction === 'rtl' ? '-translate-x-full' : 'translate-x-full' }}">
            <div class="flex items-center justify-between p-4 border-b border-border-color">
                <h2 class="text-lg font-semibold text-text-primary">{{ __('dashboard/user.list.advanced_search_title') }}
                </h2>
                <button @click="showAdvancedFilters = false" class="p-2 rounded-lg hover:bg-page-bg"><i
                        class="text-xl text-text-secondary fas fa-times"></i></button>
            </div>
            <div class="flex-1 p-4 overflow-y-auto">
                <div class="space-y-4 md:hidden">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-text-primary">
                            {{ __('dashboard/user.list.th_status') }}
                        </label>
                        <select class="w-full p-2 text-sm border rounded-lg border-border-color"
                            x-model="filters.report_status_id">
                            <option value="">{{ __('dashboard/reports.list.all_statuses') }}</option>
                            @foreach ($report_statuses as $status)
                                <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-text-primary">
                            {{ __('dashboard/user.list.th_status') }}
                        </label>
                        <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.type_id">
                            <option value="">{{ __('dashboard/reports.list.th_subject') }}</option>
                            @foreach ($report_types as $type)
                                <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-text-primary">
                            {{ __('dashboard/user.list.th_status') }}
                        </label>
                        <select class="w-full p-2 text-sm border rounded-lg border-border-color" x-model="filters.owner_id">
                            <option value="">{{ __('dashboard/reports.list.th_owner') }}</option>
                            @foreach ($owners as $owner)
                                <option value="{{ $owner['id'] }}">{{ $owner['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr class="border-border-color">
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-text-primary">
                        {{ __('dashboard/reports.list.th_date') }}
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" x-model="filters.date_from"
                            class="w-full p-2 text-sm border rounded-lg border-border-color">
                        <input type="date" x-model="filters.date_to"
                            class="w-full p-2 text-sm border rounded-lg border-border-color">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-4 border-t border-border-color">
                <button class="px-4 py-2 font-medium rounded-lg text-text-secondary hover:bg-page-bg"
                    @click="resetFilters()">{{ __('dashboard/user.list.reset_button') }}</button>
                <button class="px-4 py-2 font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90"
                    @click="applyFilters(); showAdvancedFilters = false;">{{ __('dashboard/user.list.apply_button') }}</button>
            </div>
        </div>

        <!-- Results Area -->
        <div class="bg-card-bg rounded-xl shadow-custom border border-border-color">
            <div id="paginationInfo" class="p-4 text-sm text-text-secondary border-b border-border-color"></div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 text-text-secondary">
                        <tr>
                            <th scope="col" class="p-4">{{ __('dashboard/reports.list.th_reporter') }}</th>
                            <th scope="col" class="p-4">{{ __('dashboard/reports.list.th_subject') }}</th>
                            <th scope="col" class="p-4">{{ __('dashboard/reports.list.th_property') }}</th>
                            <th scope="col" class="p-4">{{ __('dashboard/reports.list.th_property_owner') }}</th>
                            <th scope="col" class="p-4">{{ __('dashboard/reports.list.th_report_date') }}</th>
                            <th scope="col" class="p-4">{{ __('dashboard/reports.list.th_status') }}</th>
                            <th scope="col" class="p-4 text-center">{{ __('dashboard/reports.list.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-color">
                        <template x-for="report in allData" :key="report.id">
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <img :src="report.reporter.image_url ||
                                            `https://ui-avatars.com/api/?name=${encodeURIComponent(report.reporter.name)}&background=random`"
                                            :alt="report.reporter.name" class="w-10 h-10 object-cover rounded-full">
                                        <div>
                                            <div class="font-medium text-text-primary" x-text="report.reporter.name">
                                            </div>
                                            <div class="text-xs text-text-secondary" x-text="report.reporter.email"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-medium text-text-primary" x-text="report.type.name"></td>
                                <td class="p-4">
                                    <a :href="propertyEditUrl(report.property.id)" @click.stop target="_blank"
                                        class="text-accent-primary hover:underline" x-text="report.property.location"></a>
                                </td>
                                <td class="p-4 text-text-secondary" x-text="report.property.owner.name"></td>
                                <td class="p-4 text-text-secondary" x-text="formatDate(report.created_at)"></td>
                                <td class="p-4"><span class="status-badge" :class="'status-' + report.status.id"
                                        x-text="report.status.name"></span></td>
                                <td class="p-4 text-center">
                                    <button @click="openActionModal(report)"
                                        class="p-2 text-text-secondary hover:text-text-primary rounded-full">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block md:hidden p-4 space-y-4">
                <template x-if="isLoading && allData.length === 0">
                    <template x-for="i in 3" :key="i">
                        <div class="bg-white border rounded-xl shadow-sm border-border-color p-4 animate-pulse">
                            <div class="flex justify-between items-center mb-2">
                                <div class="h-4 w-2/5 bg-gray-200 rounded"></div>
                                <div class="h-6 w-1/4 bg-gray-200 rounded-full"></div>
                            </div>
                            <div class="h-4 w-3/4 bg-gray-200 rounded my-3"></div>
                            <div class="flex justify-between items-center border-t border-border-color pt-3 mt-3">
                                <div class="h-8 w-2/5 bg-gray-200 rounded"></div>
                                <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
                            </div>
                        </div>
                    </template>
                </template>

                <template x-if="!isLoading && allData.length === 0">
                    <p class="p-10 text-center text-text-secondary">{{ __('dashboard/reports.list.no_reports_found') }}
                    </p>
                </template>

                <template x-for="report in allData" :key="report.id">
                    <div class="bg-white border rounded-xl shadow-sm border-border-color p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold text-text-primary" x-text="report.type.name"></div>
                            <button @click="openActionSheet(report)"
                                class="p-2 text-text-secondary hover:text-text-primary -m-2">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                        <p class="text-sm text-text-secondary mb-3">{{ __('dashboard/reports.list.on_property') }} <a
                                :href="propertyEditUrl(report.property.id)" @click.stop target="_blank"
                                class="text-accent-primary hover:underline" x-text="report.property.location"></a></p>

                        <div
                            class="flex items-center justify-between mt-3 pt-3 border-t border-border-color text-xs text-text-secondary">
                            <span class="flex items-center gap-1.5"
                                :title="'{{ __('dashboard/reports.list.th_property_owner') }}'">
                                <i class="fas fa-user-shield"></i>
                                <span x-text="report.property.owner.name"></span>
                            </span>
                            <span class="flex items-center gap-1.5"
                                :title="'{{ __('dashboard/reports.list.th_report_date') }}'">
                                <i class="fas fa-calendar-alt"></i>
                                <span x-text="formatDate(report.created_at)"></span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-border-color">
                            <div class="flex items-center gap-2 text-sm">
                                <img :src="report.reporter.image_url ||
                                    `https://ui-avatars.com/api/?name=${encodeURIComponent(report.reporter.name)}&background=random`"
                                    :alt="report.reporter.name" class="w-8 h-8 object-cover rounded-full">
                                <span x-text="report.reporter.name"></span>
                            </div>
                            <span class="status-badge" :class="'status-' + report.status.id"
                                x-text="report.status.name"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="!isLoading && allData.length === 0" x-cloak class="py-10 text-center">
                <p class="text-text-secondary">{{ __('dashboard/reports.list.no_reports_found') }}</p>
            </div>
        </div>

        <div id="paginationControls" class="flex flex-wrap justify-center gap-2 py-4"
            x-show="!isLoading && paginationData && paginationData.total > paginationData.per_page" x-cloak>
        </div>

        <!-- Report Detail Modal -->
        <div x-show="isModalOpen" x-cloak class="sa-modal fixed inset-0 z-1050 flex items-center justify-center"
            id="reportDetailModal" aria-hidden="true"
            :class="{ 'invisible opacity-0': !isModalOpen, 'visible opacity-100': isModalOpen }">

            <div @click="closeModal()" class="sa-modal-overlay absolute inset-0 bg-slate-900/70 cursor-pointer"
                tabindex="-1" data-modal-close></div>

            <div class="sa-modal-content relative bg-card-bg rounded-xl w-[95%] max-w-4xl shadow-xl transition-transform duration-200 flex flex-col max-h-[90vh]"
                :class="{ 'scale-95': !isModalOpen, 'scale-100': isModalOpen }" role="dialog" aria-modal="true">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b border-border-color flex-shrink-0">
                    <h3 class="text-lg font-semibold text-text-primary"
                        x-text="selectedReport ? `{{ __('dashboard/reports.list.report_details.title') }} #${selectedReport.id}` : ''">
                    </h3>
                    <button type="button" @click="closeModal()"
                        class="text-xl text-text-secondary hover:text-text-primary" data-modal-close>×</button>
                </div>

                <!-- Modal Body (with flex-grow to take available space) -->
                <div class="flex-grow overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-5 h-full">

                        <!-- Right Column: Property Details (with internal scroll) -->
                        <div
                            class="lg:col-span-3 p-4 md:p-6 border-b lg:border-b-0 lg:border-r border-border-color overflow-y-auto">
                            <h4 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-3">
                                {{ __('dashboard\reports.list.report_details.related_property_title') }}</h4>
                            <div>
                                <h5 class="text-lg font-bold text-text-primary mb-3"
                                    x-text="selectedReport?.property?.location || 'Untitled Property'"></h5>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-3 mb-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-money-bill-wave text-green-500 w-5 text-center"></i>
                                        <span class="font-semibold text-text-primary"
                                            x-text="selectedReport?.property?.price_display || 'N/A'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-tag text-gray-400 w-5 text-center"></i>
                                        <span x-text="selectedReport?.property?.category?.name || '-'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-building text-gray-400 w-5 text-center"></i>
                                        <span x-text="selectedReport?.property?.type?.name || '-'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-ruler-combined text-gray-400 w-5 text-center"></i>
                                        <span
                                            x-text="selectedReport?.property?.size ? `${selectedReport.property.size} m²` : '-'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-bed text-gray-400 w-5 text-center"></i>
                                        <span
                                            x-text="selectedReport?.property?.rooms_count ?
                                    `{{ __('dashboard/reports.list.rooms', ['count' => '']) }}${selectedReport.property.rooms_count}` : '-'">
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-layer-group text-gray-400 w-5 text-center"></i>
                                        <span x-text="selectedReport?.property?.floor?.name || '-'"></span>
                                    </div>
                                </div>
                                <h6 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2 mt-4">
                                    {{ __('dashboard\reports.list.report_details.property_description_label') }}</h6>
                                <div class="p-3 bg-page-bg rounded-lg border border-border-color text-text-secondary text-sm leading-relaxed max-h-[200px] overflow-y-auto"
                                    x-text="selectedReport?.property?.content || '{{ __('dashboard/reports.list.no_description_available') }}'">
                                </div>
                            </div>
                        </div>

                        <!-- Left Column: Report Details & Actions (with internal scroll) -->
                        <div class="lg:col-span-2 p-4 md:p-6 bg-slate-50/50 overflow-y-auto">
                            <h4 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-3">
                                {{ __('dashboard\reports.list.report_details.title') }}</h4>
                            <div class="mb-4">
                                <p class="font-semibold text-text-secondary text-sm mb-1">
                                    {{ __('dashboard/reports.list.th_reporter') }}</p>
                                <div class="flex items-center gap-2">
                                    <img :src="selectedReport?.reporter?.image_url ||
                                        `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedReport?.reporter?.name || 'U')}&background=random`"
                                        alt="Reporter" class="w-9 h-9 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-text-primary"
                                            x-text="selectedReport?.reporter?.name || 'Unknown'"></p>
                                        <p class="text-xs text-text-secondary"
                                            x-text="selectedReport?.reporter?.email || '-'"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <p class="font-semibold text-text-secondary text-sm mb-1">
                                    {{ __('dashboard\reports.list.report_details.report_subject_label') }}</p>
                                <p class="font-bold text-text-primary" x-text="selectedReport?.type?.name || 'N/A'"></p>
                            </div>
                            <div class="mb-4">
                                <p class="font-semibold text-text-secondary text-sm mb-1">
                                    {{ __('dashboard\reports.list.report_details.report_description_label') }}</p>
                                <div class="p-3 bg-white border border-border-color rounded-md text-sm text-text-secondary whitespace-pre-wrap max-h-[200px] overflow-y-auto"
                                    x-text="selectedReport?.message || '-'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div
                    class="flex flex-col sm:flex-row sm:justify-between gap-3 p-4 md:p-5 border-t border-border-color bg-slate-50/50 flex-shrink-0">
                    <!-- Left Side: View Property and View Lease Links -->
                    <div class="flex flex-wrap gap-2">
                        <!-- Property Link -->
                        <a :href="`{{ route('properties.details', ['property' => ':id']) }}`.replace(':id', selectedReport
                            ?.property.id)"
                            target="_blank"
                            class="text-sm font-medium text-accent-primary hover:underline flex items-center gap-2 px-3 py-1.5 bg-blue-50/70 rounded-md hover:bg-blue-100 transition-colors border border-blue-200"
                            x-show="selectedReport?.property">
                            <i class="fas fa-external-link-alt text-sm"></i>
                            <span
                                class="whitespace-nowrap">{{ __('dashboard\reports.list.report_details.open_property_page_btn') }}</span>
                        </a>

                        <!-- Lease Contract Link -->
                        <a :href="selectedReport?.property?.lease_contract_url || '#'" target="_blank"
                            class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-2 bg-blue-50/70 px-3 py-1.5 rounded-md hover:bg-blue-100 transition-colors border border-blue-200"
                            x-show="selectedReport?.property?.lease_contract_url">
                            <i class="fas fa-file-contract text-blue-500 text-sm"></i>
                            <span
                                class="whitespace-nowrap">{{ __('dashboard\reports.list.report_details.open_lease_contract_btn') }}</span>
                        </a>
                    </div>

                    <!-- Right Side: Actions -->
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        <!-- Cancel Button (Secondary) -->
                        {{-- <button type="button" @click="closeModal()"
                            class="btn-cancel flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg sm:rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors font-medium w-full sm:w-auto justify-center">
                            <i class="fas fa-times"></i>
                            <span>{{ __('dashboard\reports.list.report_details.cancel_btn') }}</span>
                        </button> --}}

                        <!-- Action Group (Select + Save) -->
                        <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2 sm:gap-0">
                            <select id="modal_report_status_select"
                                class="form-select w-full sm:w-40 rounded-lg sm:rounded-l-lg sm:rounded-r-none rtl:sm:rounded-l-none rtl:sm:rounded-r-lg h-10 border sm:border-r-0 focus:ring-2 focus:ring-blue-300"
                                x-model="selectedStatus" @change="updateSaveButtonState()">
                                @foreach ($report_statuses as $status)
                                    <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="modal_save_changes_btn" @click="saveReportChanges()"
                                class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium h-10 px-4 rounded-lg sm:rounded-l-none sm:rounded-r-lg rtl:sm:rounded-l-lg rtl:sm:rounded-r-none transition-all duration-200 disabled:bg-green-300 disabled:cursor-not-allowed transform hover:scale-[1.02] w-full sm:w-auto">
                                <i class="fas fa-save"></i>
                                <span>{{ __('dashboard\reports.list.report_details.save_btn') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Sheet -->
        <div x-show="isActionSheetOpen" x-cloak class="fixed inset-0 z-30 flex items-end md:hidden">
            <div x-show="isActionSheetOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="isActionSheetOpen = false" class="fixed inset-0 bg-black/40">
            </div>
            <div x-show="isActionSheetOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                class="relative w-full bg-page-bg rounded-t-2xl shadow-lg p-4">
                <div class="space-y-2">
                    <template x-for="action in reportActions" :key="action.label">
                        <button @click="action.handler" :class="action.classes"
                            class="w-full flex items-center gap-4 p-3 text-lg rounded-lg text-left">
                            <i :class="action.icon" class="w-6 text-center"></i>
                            <span x-text="action.label"></span>
                        </button>
                    </template>
                </div>
                <div class="mt-4">
                    <button @click="isActionSheetOpen = false"
                        class="w-full bg-card-bg p-3 text-lg rounded-lg font-semibold text-text-primary">
                        {{ __('dashboard/reports.list.cancel_button') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
