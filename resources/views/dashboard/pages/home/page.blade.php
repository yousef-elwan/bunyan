@extends('dashboard.layouts.default')

@php
    $direction = config('app.locale') == 'ar' ? 'rtl' : 'ltr';
@endphp

@push('css_or_js')
    {{-- Scripts and translations setup for AlpineJS --}}
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        window.AppConfig.i18n = window.AppConfig.i18n || {};

        Object.assign(window.AppConfig.routes, {
            'api.reports': "{{ route('api.reports') }}",
            'api.reports.update_status': "{{ route('api.reports.update_status', ['report' => ':reportId']) }}",
            'dashboard.properties.edit': "{{ route('dashboard.properties.edit', ['property' => ':propertyId']) }}",
            'properties.details': "{{ route('properties.details', ['property' => ':id']) }}"
        });

        Object.assign(window.AppConfig.i18n, @json(__('dashboard/reports.list')));
        // The controller must pass this variable
        window.reportStatuses = @json($report_statuses ?? []);
    </script>

    {{-- Include the JS file which contains `dashboardReports` component --}}
    @vite('resources/js/alpine/dashboard/report/list.js')

    {{-- Inline styles for status badges and progress bars --}}
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
            background-color: #fffbeb;
            color: #f59e0b;
        }

        .status-in_progress {
            background-color: #ebffff;
            color: #0bc6f5;
        }

        .status-resolved {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .status-rejected {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .progress-bar {
            height: 8px;
            border-radius: 9999px;
            transition: width 0.3s ease-in-out;
        }
    </style>
@endpush


@section('content')
    <div class="p-4 sm:p-6 space-y-6 bg-gray-50 min-h-screen">

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @isset($stats)
                @foreach ($stats as $stat)
                    <div
                        class="flex justify-between items-center p-6 bg-white rounded-xl border border-gray-200 shadow-sm transition duration-200 ease-in-out hover:-translate-y-1 hover:shadow-lg">
                        <div
                            class="w-14 h-14 rounded-full flex items-center justify-center text-2xl
                        @if ($stat['color'] === 'blue') bg-indigo-100 text-indigo-500 @endif
                        @if ($stat['color'] === 'green') bg-green-100 text-green-500 @endif
                        @if ($stat['color'] === 'purple') bg-purple-100 text-purple-500 @endif
                        @if ($stat['color'] === 'yellow') bg-yellow-100 text-yellow-500 @endif
                        @if ($stat['color'] === 'red') bg-red-100 text-red-500 @endif">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-500">{{ __($stat['title']) }}</p>
                            <p class="text-3xl font-bold text-gray-800 leading-tight my-1">{{ $stat['metric'] }}</p>
                            <p class="text-xs text-gray-400">{{ __($stat['trend_text']) }}</p>
                        </div>
                    </div>
                @endforeach
            @endisset
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Charts and stats -->
            <div class="space-y-6 lg:col-span-1">
                {{-- //@if ($user->type == 'admin') --}}

                @if ($user->isAdmin())
                    <div class="bg-card-bg border border-border-color rounded-xl shadow-custom">
                        <a href="{{ route('dashboard.reports.index') }}">
                            <div class="flex justify-between items-center px-5 py-4 border-b border-border-color">
                                <span
                                    class="text-lg font-semibold text-text-primary">{{ __('dashboard/dashboard.charts.report_statuses') }}</span>
                                <span
                                    class="text-text-secondary font-medium">{{ $stats['total_reports']['metric'] ?? '' }}</span>
                            </div>
                        </a>
                        <div class="p-5">
                            <div class="flex flex-col gap-5 pt-2">
                                @php $statusColors = ['pending' => 'yellow', 'resolved' => 'green', 'rejected' => 'gray', 'in_progress' => 'blue']; @endphp
                                @forelse ($reportStatusesData ?? [] as $status)
                                    {{-- <a href="{{ route('dashboard.reports.index', ['report_status_id' => $status->id]) }}">
                                    <div class="w-full">
                                        <div class="flex justify-between items-center mb-2">
                                            <span
                                                class="text-sm font-medium text-text-primary">{{ __('dashboard/dashboard.status.' . $status->status) }}</span>
                                            <span
                                                class="text-sm font-medium text-text-secondary">%{{ $status->id }}</span>
                                        </div>
                                        <div class="w-full h-2 bg-border-color rounded-full overflow-hidden">
                                            <div class="h-full rounded-full
                                            @if ($statusColors[$status->status] === 'yellow') bg-yellow-500 @endif
                                            @if ($statusColors[$status->status] === 'green') bg-green-500 @endif
                                            @if ($statusColors[$status->status] === 'gray') bg-gray-500 @endif
                                            @if ($statusColors[$status->status] === 'blue') bg-blue-500 @endif"
                                                style="width: {{ $status->percentage }}%"></div>
                                        </div>
                                    </div>
                                </a> --}}
                                    <a href="{{ route('dashboard.reports.index', ['report_status_id' => $status->status]) }}"
                                        class="block p-2 -m-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                        <div class="w-full">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm font-medium text-text-primary">
                                                    {{ __('dashboard/dashboard.status.' . $status->status) }}
                                                </span>
                                                <span class="text-sm font-medium text-text-secondary">
                                                    {{ number_format($status->percentage, 0) }}%
                                                </span>
                                            </div>
                                            <div class="w-full h-2 bg-border-color rounded-full overflow-hidden">
                                                <div class="h-full rounded-full
                                                @if ($statusColors[$status->status] === 'yellow') bg-yellow-500 @endif
                                                @if ($statusColors[$status->status] === 'green') bg-green-500 @endif
                                                @if ($statusColors[$status->status] === 'gray') bg-gray-500 @endif
                                                @if ($statusColors[$status->status] === 'blue') bg-blue-500 @endif"
                                                    style="width: {{ $status->percentage }}%"></div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-center text-text-secondary">
                                        {{ __('dashboard/dashboard.general.no_data') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif


                {{-- Property Types Widget --}}
                <div class="p-5 bg-white rounded-xl shadow-sm">
                    <a href="{{ route('dashboard.properties.index') }}">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">
                            {{ __('dashboard/dashboard.charts.property_types') }}</h3>
                    </a>
                    <div class="space-y-4">
                        @php $propertyTypeColors = ['blue-500', 'green-500', 'yellow-500', 'purple-500', 'red-500']; @endphp
                        @forelse ($propertyTypesData ?? [] as $index => $type)
                            <a href="{{ route('dashboard.properties.index', ['type_id' => $type->id]) }}"
                                class="block hover:bg-gray-50 p-1 -m-1 rounded-md">
                                <div>
                                    <div class="flex justify-between mb-1 text-sm">
                                        <span class="font-medium text-gray-600">{{ $type->type_name }}</span>
                                        <span class="text-gray-500">{{ number_format($type->percentage, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full">
                                        <div class="progress-bar bg-{{ $propertyTypeColors[$index % count($propertyTypeColors)] }}"
                                            style="width: {{ $type->percentage }}%"></div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-center text-gray-500">{{ __('dashboard/dashboard.general.no_data') }}
                            </p>
                        @endforelse
                    </div>
                </div>
                {{-- //@endif --}}

            </div>

            <!-- Right Column: Tables -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Latest Properties Widget -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="flex justify-between items-center p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ __('dashboard/dashboard.properties.latest_title') }}</h2>
                        <a href="{{ route('dashboard.properties.index') }}"
                            class="text-sm font-medium text-indigo-600 hover:underline">{{ __('dashboard/dashboard.general.view_all') }}</a>
                    </div>

                    <!-- Desktop Table for Properties -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-left text-xs uppercase text-gray-500 bg-gray-50">
                                <tr>
                                    <th class="p-3 font-semibold">
                                        {{ __('dashboard/dashboard.properties.table_header.property') }}</th>
                                    @if ($user->isAdmin())
                                        <th class="p-3 font-semibold">
                                            {{ __('dashboard/dashboard.properties.table_header.owner') }}</th>
                                    @endif
                                    <th class="p-3 font-semibold">
                                        {{ __('dashboard/dashboard.properties.table_header.price') }}</th>
                                    <th class="p-3 font-semibold">
                                        {{ __('dashboard/dashboard.properties.table_header.date') }}</th>
                                    <th class="p-3 font-semibold">
                                        {{ __('dashboard/dashboard.properties.table_header.status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse (array_slice($latestProperties ?? [], 0, 5) as $property)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-3">
                                            <a href="{{ route('properties.details', ['property' => $property['id']]) }}"
                                                target="_blank" class="flex items-center gap-3">
                                                <img src="{{ $property['image_url'] ?? asset('path/to/default.jpg') }}"
                                                    alt="Property" class="w-16 h-16 object-cover rounded-md flex-shrink-0">
                                                <div>
                                                    <div class="font-semibold text-gray-800">
                                                        {{ Str::limit($property['location'], 40) }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">{{ $property['city'] }}</div>
                                                </div>
                                            </a>
                                        </td>
                                        @if ($user->isAdmin())
                                            <td class="p-3 text-gray-600">{{ $property['owner']['name'] ?? 'N/A' }}</td>
                                        @endif
                                        <td class="p-3 font-semibold text-gray-800">
                                            {{ number_format($property['price'], 0) }}
                                            {{ $property['currency_symbol'] ?? '' }}</td>
                                        <td class="p-3 text-gray-500">
                                            {{ \Carbon\Carbon::parse($property['created_at'])->diffForHumans() }}</td>
                                        <td class="p-3">
                                            <span
                                                class="text-xs font-semibold px-2 py-1 rounded-full @if ($property['status'] ?? true) bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">
                                                {{ $property['status'] ?? true ? __('dashboard/dashboard.status.active') : __('dashboard/dashboard.status.inactive') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $user->isAdmin() ? 5 : 4 }}"
                                            class="text-center text-gray-500 py-10">
                                            {{ __('dashboard/dashboard.properties.no_recent') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards for Properties -->
                    <div class="block md:hidden p-4 space-y-4">
                        @forelse (array_slice($latestProperties ?? [], 0, 5) as $property)
                            <a href="{{ route('properties.details', ['property' => $property['id']]) }}" target="_blank"
                                class="block bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:border-indigo-400 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start gap-4">
                                    <img src="{{ $property['image_url'] ?? asset('path/to/default.jpg') }}" alt="Property"
                                        class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                                    <div class="flex-grow text-right">
                                        <div class="flex justify-start mb-2">
                                            <span
                                                class="text-xs font-semibold px-2 py-1 rounded-full @if ($property['status'] ?? true) bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">
                                                {{ $property['status'] ?? true ? __('dashboard/dashboard.status.active') : __('dashboard/dashboard.status.inactive') }}
                                            </span>
                                        </div>
                                        <h3 class="font-bold text-gray-800 text-base leading-snug">
                                            {{ Str::limit($property['location'], 50) }}</h3>
                                        <p class="text-indigo-600 font-bold text-lg mt-2">
                                            {{ number_format($property['price'], 0) }} <span
                                                class="text-sm font-medium">{{ $property['currency_symbol'] ?? '' }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-user text-gray-400"></i>
                                        {{ $property['owner']['name'] ?? 'N/A' }}</span>
                                    <span class="flex items-center gap-1.5"><i
                                            class="fas fa-calendar-alt text-gray-400"></i>
                                        {{ \Carbon\Carbon::parse($property['created_at'])->diffForHumans() }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-center text-gray-500 py-10">{{ __('dashboard/dashboard.properties.no_recent') }}
                            </p>
                        @endforelse
                    </div>
                </div>
                @if ($user->type != 'user')
                    <!-- Latest Reports Widget -->
                    <div x-data="dashboardReports()" x-init="report_statuses = window.reportStatuses;
                    init();" class="bg-white rounded-xl shadow-sm">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-800">
                                {{ __('dashboard/dashboard.general.last_repo') }}</h2>
                            <a href="{{ route('dashboard.reports.index') }}"
                                class="text-sm font-medium text-indigo-600 hover:underline">{{ __('dashboard/dashboard.general.view_all') }}</a>
                        </div>

                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs uppercase bg-gray-50 text-gray-500">
                                    <tr>
                                        <th scope="col" class="p-3">المُبلّغ</th>
                                        <th scope="col" class="p-3">الموضوع</th>
                                        <th scope="col" class="p-3">الحالة</th>
                                        <th scope="col" class="p-3 text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-if="isLoading"><template x-for="i in 4" :key="i">
                                            <tr class="animate-pulse">
                                                <td class="p-3">
                                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                                </td>
                                                <td class="p-3">
                                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                                </td>
                                                <td class="p-3">
                                                    <div class="h-6 bg-gray-200 rounded-full w-24"></div>
                                                </td>
                                                <td class="p-3 text-center">
                                                    <div class="h-6 w-6 bg-gray-200 rounded-full mx-auto"></div>
                                                </td>
                                            </tr>
                                        </template></template>
                                    <template x-if="!isLoading && allData.length === 0">
                                        <tr>
                                            <td colspan="4" class="p-10 text-center text-gray-500">لا توجد إبلاغات.
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="report in allData.slice(0, 5)" :key="report.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3"><img
                                                        :src="report.reporter.image_url ||
                                                            `https://ui-avatars.com/api/?name=${encodeURIComponent(report.reporter.name)}&background=random`"
                                                        :alt="report.reporter.name"
                                                        class="w-10 h-10 object-cover rounded-full flex-shrink-0">
                                                    <div>
                                                        <div class="font-medium text-gray-800"
                                                            x-text="report.reporter.name"></div>
                                                        <div class="text-xs text-gray-500 mt-1"
                                                            x-text="report.reporter.email"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                <div class="text-gray-600" x-text="report.type.name"></div>
                                                <div class="text-xs text-gray-500 mt-1"><a
                                                        :href="`{{ route('properties.details', ['property' => ':id']) }}`
                                                        .replace(':id', report.property.id)"
                                                        target="_blank" class="hover:underline"
                                                        x-text="report.property.location.substring(0, 35) + '...'"></a>
                                                </div>
                                            </td>
                                            <td class="p-3"><span class="status-badge"
                                                    :class="'status-' + report.status.id"
                                                    x-text="report.status.name"></span></td>
                                            <td class="p-3 text-center"><button @click="openActionModal(report)"
                                                    class="p-2 text-gray-500 hover:text-gray-800 rounded-full"><i
                                                        class="fas fa-ellipsis-h"></i></button></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="block md:hidden p-4 space-y-4">
                            <template x-if="isLoading"><template x-for="i in 4" :key="i">
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 animate-pulse shadow-sm">
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="h-5 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-6 w-6 bg-gray-200 rounded-full"></div>
                                        </div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                            <div class="flex-grow space-y-2">
                                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                            <div class="h-6 bg-gray-200 rounded-full w-20"></div>
                                        </div>
                                    </div>
                                </template></template>
                            <template x-if="!isLoading && allData.length === 0">
                                <p class="text-center text-gray-500 py-10">لا توجد إبلاغات.</p>
                            </template>
                            <template x-for="report in allData.slice(0, 5)" :key="report.id">
                                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-bold text-gray-800 text-base" x-text="report.type.name"></h3>
                                        <button @click="openActionModal(report)"
                                            class="p-2 text-gray-500 hover:text-gray-800 -m-2"><i
                                                class="fas fa-ellipsis-v"></i></button>
                                    </div>
                                    <div class="flex items-center gap-3 mb-4">
                                        <img :src="report.reporter.image_url ||
                                            `https://ui-avatars.com/api/?name=${encodeURIComponent(report.reporter.name)}&background=random`"
                                            :alt="report.reporter.name"
                                            class="w-10 h-10 object-cover rounded-full flex-shrink-0">
                                        <div>
                                            <p class="font-medium text-gray-700" x-text="report.reporter.name"></p>
                                            <p class="text-xs text-gray-500" x-text="report.reporter.email"></p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                        <div class="text-xs text-gray-500">
                                            <a :href="`{{ route('properties.details', ['property' => ':id']) }}`.replace(':id',
                                                report.property.id)"
                                                target="_blank"
                                                class="text-indigo-600 hover:underline flex items-center gap-1.5"><i
                                                    class="fas fa-home"></i><span>عرض العقار</span></a>
                                        </div>
                                        <span class="status-badge" :class="'status-' + report.status.id"
                                            x-text="report.status.name"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-1050 flex items-center justify-center"
                            aria-hidden="true">
                            <div @click="closeModal()" class="absolute inset-0 bg-slate-900/70 cursor-pointer"
                                x-show="isModalOpen" x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"></div>
                            <div class="relative bg-white rounded-xl w-[95%] max-w-4xl shadow-xl transition-transform duration-300 flex flex-col max-h-[90vh]"
                                x-show="isModalOpen" x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95">
                                <!-- Modal Header -->
                                <div class="flex items-center justify-between p-4 border-b border-gray-200 flex-shrink-0">
                                    <h3 class="text-lg font-semibold text-gray-800"
                                        x-text="selectedReport ? `{{ __('dashboard/reports.list.report_details.title') }} #${selectedReport.id}` : ''">
                                    </h3>
                                    <button type="button" @click="closeModal()"
                                        class="text-xl text-gray-500 hover:text-gray-800">×</button>
                                </div>
                                <!-- Modal Body -->
                                <div class="flex-grow overflow-hidden">
                                    <div class="grid grid-cols-1 lg:grid-cols-5 h-full">
                                        <!-- Right Column: Property Details -->
                                        <div
                                            class="lg:col-span-3 p-6 border-b lg:border-b-0 lg:border-r border-gray-200 overflow-y-auto">
                                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                                تفاصيل العقار</h4>
                                            <div x-show="selectedReport">
                                                <h5 class="text-lg font-bold text-gray-900 mb-3"
                                                    x-text="selectedReport?.property?.location || '...'"></h5>
                                                <div class="grid grid-cols-2 gap-x-4 gap-y-3 mb-4 text-sm">
                                                    <div class="flex items-center gap-2"><i
                                                            class="fas fa-money-bill-wave text-green-500 w-5 text-center"></i><span
                                                            class="font-semibold text-gray-800"
                                                            x-text="selectedReport?.property?.price_display || '-'"></span>
                                                    </div>
                                                    <div class="flex items-center gap-2"><i
                                                            class="fas fa-tag text-gray-400 w-5 text-center"></i><span
                                                            class="text-gray-600"
                                                            x-text="selectedReport?.property?.category?.name || '-'"></span>
                                                    </div>
                                                    <div class="flex items-center gap-2"><i
                                                            class="fas fa-building text-gray-400 w-5 text-center"></i><span
                                                            class="text-gray-600"
                                                            x-text="selectedReport?.property?.type?.name || '-'"></span>
                                                    </div>
                                                    <div class="flex items-center gap-2"><i
                                                            class="fas fa-ruler-combined text-gray-400 w-5 text-center"></i><span
                                                            class="text-gray-600"
                                                            x-text="selectedReport?.property?.size ? `${selectedReport.property.size} m²` : '-'"></span>
                                                    </div>
                                                </div>
                                                <h6
                                                    class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">
                                                    وصف العقار</h6>
                                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-600 text-sm leading-relaxed max-h-[200px] overflow-y-auto"
                                                    x-text="selectedReport?.property?.content || 'لا يوجد وصف متاح.'">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Left Column: Report Details -->
                                        <div class="lg:col-span-2 p-6 bg-gray-50 overflow-y-auto">
                                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                                تفاصيل الإبلاغ</h4>
                                            <div x-show="selectedReport" class="space-y-4">
                                                <div>
                                                    <p class="font-semibold text-gray-600 text-sm mb-1">المُبلّغ</p>
                                                    <div class="flex items-center gap-2"><img
                                                            :src="selectedReport?.reporter?.image_url ||
                                                                `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedReport?.reporter?.name || 'U')}&background=random`"
                                                            alt="Reporter" class="w-9 h-9 rounded-full object-cover">
                                                        <div>
                                                            <p class="font-medium text-gray-800"
                                                                x-text="selectedReport?.reporter?.name || '...'"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-600 text-sm mb-1">موضوع الإبلاغ</p>
                                                    <p class="font-bold text-gray-800"
                                                        x-text="selectedReport?.type?.name || '...'"></p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-600 text-sm mb-1">رسالة الإبلاغ</p>
                                                    <div class="p-3 bg-white border border-gray-200 rounded-md text-sm text-gray-700 whitespace-pre-wrap max-h-[200px] overflow-y-auto"
                                                        x-text="selectedReport?.message || '-'"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Footer -->
                                <div
                                    class="flex flex-col sm:flex-row sm:justify-between items-center gap-3 p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                                    <div class="flex flex-wrap gap-2"><a
                                            :href="selectedReport ? `{{ route('properties.details', ['property' => ':id']) }}`
                                                .replace(':id', selectedReport.property.id) : '#'"
                                            target="_blank"
                                            class="text-sm font-medium text-indigo-600 hover:underline flex items-center gap-2"
                                            x-show="selectedReport?.property"><i
                                                class="fas fa-external-link-alt text-xs"></i><span>فتح صفحة
                                                العقار</span></a></div>
                                    <div class="flex items-center gap-2 w-full sm:w-auto"><select
                                            class="form-select flex-grow sm:flex-grow-0 sm:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            x-model="selectedStatus" @change="updateSaveButtonState()"><template
                                                x-for="status in reportStatuses" :key="status.id">
                                                <option :value="status.id" x-text="status.name"></option>
                                            </template></select><button type="button" @click="saveReportChanges()"
                                            :disabled="isSaveDisabled"
                                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:bg-green-300 disabled:cursor-not-allowed"><i
                                                class="fas fa-save"></i><span>حفظ</span></button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
