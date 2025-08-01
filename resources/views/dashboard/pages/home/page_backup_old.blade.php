@extends('dashboard.layouts.default')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* For Modal visibility controlled by JS */
        .sa-modal {
            transition: opacity 0.2s ease, visibility 0s 0.2s;
        }

        .sa-modal.is-visible {
            transition: opacity 0.2s ease, visibility 0s;
            visibility: visible;
            opacity: 1;
        }

        .sa-modal.is-visible .sa-modal-content {
            transform: scale(1);
        }

        /* Responsive CSS for the reports table on mobile */
        @media (max-width: 767px) {
            .reports-responsive-table thead {
                display: none;
            }

            .reports-responsive-table tr {
                display: block;
                margin-bottom: 1rem;
                border-radius: 12px;
                border: 1px solid #eef2f7;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
                overflow: hidden;
            }

            .reports-responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                border-bottom: 1px solid #eef2f7;
            }

            .reports-responsive-table td:last-child {
                border-bottom: none;
            }

            .reports-responsive-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #64748b;
                padding-inline-end: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportsDashboard', () => ({
                // State Properties
                isModalOpen: false,
                selectedReport: null,
                isActionSheetOpen: false,

                // Methods
                openActionModal(report) {
                    this.selectedReport = report;
                    this.isModalOpen = true;
                },
                openActionSheet(report) {
                    this.selectedReport = report;
                    this.isActionSheetOpen = true;
                },
                closeModal() {
                    this.isModalOpen = false;
                },
                closeActionSheet() {
                    this.isActionSheetOpen = false;
                },
                async updateStatus(reportId, newStatusId) {
                    // Implement status update logic
                    console.log(`Updating report ${reportId} to status ${newStatusId}`);
                    // Add your API call here
                }
            }));
        });
    </script>
@endpush

@section('content')
    <div class="max-w-[1600px] mx-auto p-4 md:p-6" x-data="reportsDashboard()">
        <div class="w-full">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-text-primary m-0">{{ __('dashboard/dashboard.general.dashboard') }}</h1>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @isset($stats)
                    @foreach ($stats as $stat)
                        <div
                            class="flex justify-between items-center p-6 bg-card-bg rounded-xl border border-border-color shadow-custom transition duration-200 ease-in-out hover:-translate-y-1 hover:shadow-custom-hover">
                            <div
                                class="w-14 h-14 rounded-full flex items-center justify-center text-2xl 
                                @if ($stat['color'] === 'blue') bg-accent-primary-light text-accent-primary @endif
                                @if ($stat['color'] === 'green') bg-success-light text-success @endif
                                @if ($stat['color'] === 'purple') bg-purple-light text-purple @endif
                                @if ($stat['color'] === 'yellow') bg-warning-light text-warning @endif
                                @if ($stat['color'] === 'red') bg-danger-light text-danger @endif">
                                <i class="{{ $stat['icon'] }}"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-text-secondary">{{ __($stat['title']) }}</p>
                                <p class="text-[2.2rem] font-bold text-text-primary leading-tight my-1">{{ $stat['metric'] }}
                                </p>
                                <p class="text-sm text-text-secondary">{{ __($stat['trend_text']) }}</p>
                            </div>
                        </div>
                    @endforeach
                @endisset
            </div>

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                <main class="flex-1 min-w-0">
                    <!-- Latest Properties -->
                    <div class="bg-card-bg border border-border-color rounded-xl shadow-custom mb-6">
                        <div class="flex justify-between items-center px-5 py-4 border-b border-border-color">
                            <span
                                class="text-lg font-semibold text-text-primary">{{ __('dashboard/dashboard.properties.latest_title') }}</span>
                            <a href="{{ route('dashboard.properties.index') }}"
                                class="text-sm font-medium text-accent-primary hover:text-text-primary transition-colors">{{ __('dashboard/dashboard.general.view_all') }}</a>
                        </div>

                        <!-- Desktop View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-text-secondary bg-slate-50">
                                        <th class="p-3 font-semibold">
                                            {{ __('dashboard/dashboard.properties.table_header.property') }}
                                        </th>
                                        @if ($user->isAdmin())
                                            <th class="p-3 font-semibold">
                                                {{ __('dashboard/dashboard.properties.table_header.owner') }}
                                            </th>
                                        @endif
                                        <th class="p-3 font-semibold">
                                            {{ __('dashboard/dashboard.properties.table_header.price') }}
                                        </th>
                                        <th class="p-3 font-semibold">
                                            {{ __('dashboard/dashboard.properties.table_header.date') }}
                                        </th>
                                        <th class="p-3 font-semibold">
                                            {{ __('dashboard/dashboard.properties.table_header.status') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border-color">
                                    @forelse ($latestProperties ?? [] as $property)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $property['image_url'] ?? asset('path/to/default.jpg') }}"
                                                        alt="Property"
                                                        class="w-16 h-16 object-cover rounded-md flex-shrink-0">
                                                    <div>
                                                        <div class="font-semibold text-text-primary">
                                                            {{ Str::limit($property['location'], 40) }}</div>
                                                        <div class="text-xs text-text-secondary mt-1">
                                                            {{ $property['city'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            @if ($user->isAdmin())
                                                <td class="p-3 text-text-secondary">
                                                    {{ $property['owner']['name'] ?? 'N/A' }}
                                                </td>
                                            @endif
                                            <td class="p-3 font-semibold text-text-primary">
                                                {{ number_format($property['price'], 0) }}
                                                {{ $property['currency_symbol'] ?? '' }}</td>
                                            <td class="p-3 text-text-secondary">
                                                {{ \Carbon\Carbon::parse($property['created_at'])->diffForHumans() }}</td>
                                            <td class="p-3">
                                                <span
                                                    class="text-xs font-semibold px-2 py-1 rounded-full @if ($property['status'] ?? true) bg-success-light text-success @else bg-danger-light text-danger @endif">
                                                    {{ $property['status'] ?? true ? __('dashboard/dashboard.status.active') : __('dashboard/dashboard.status.inactive') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-text-secondary py-10">
                                                {{ __('dashboard/dashboard.properties.no_recent') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="block md:hidden p-4 space-y-4">
                            @forelse ($latestProperties ?? [] as $property)
                                <div class="bg-card-bg border border-border-color rounded-lg p-4">
                                    <div class="flex items-start gap-4">
                                        <img src="{{ $property['image_url'] ?? asset('path/to/default.jpg') }}"
                                            alt="Property" class="w-20 h-20 object-cover rounded-md flex-shrink-0">
                                        <div class="flex-grow">
                                            <div class="flex justify-between items-center">
                                                <span
                                                    class="text-xs font-semibold px-2 py-1 rounded-full @if ($property['status'] ?? true) bg-success-light text-success @else bg-danger-light text-danger @endif">
                                                    {{ $property['status'] ?? true ? __('dashboard/dashboard.status.active') : __('dashboard/dashboard.status.inactive') }}
                                                </span>
                                            </div>
                                            <h3 class="font-bold text-text-primary mt-1">
                                                {{ Str::limit($property['location'], 40) }}</h3>
                                            <p class="text-sm font-semibold text-accent-primary mt-1">
                                                {{ number_format($property['price'], 0) }}
                                                {{ $property['currency_symbol'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between mt-3 pt-3 border-t border-border-color text-xs text-text-secondary">
                                        <span class="flex items-center gap-1.5"><i class="fas fa-user-circle"></i>
                                            {{ $property['user']['name'] ?? 'N/A' }}</span>
                                        <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($property['created_at'])->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-text-secondary py-10">
                                    {{ __('dashboard/dashboard.properties.no_recent') }}
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- @if ($user->isAdmin())
                        <div class="bg-card-bg border border-border-color rounded-xl shadow-custom">
                            <div class="flex justify-between items-center px-5 py-4 border-b border-border-color">
                                <span
                                    class="text-lg font-semibold text-text-primary">{{ __('dashboard/dashboard.reports.latest_title') }}</span>
                                <a href="#"
                                    class="text-sm font-medium text-accent-primary hover:text-text-primary transition-colors">{{ __('dashboard/dashboard.general.view_all') }}</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm reports-responsive-table">
                                    <thead class="hidden md:table-header-group">
                                        <tr class="text-left text-xs uppercase text-text-secondary bg-slate-50">
                                            <th class="p-3 font-semibold">
                                                {{ __('dashboard/dashboard.reports.table_header.subject') }}</th>
                                            <th class="p-3 font-semibold w-32 text-center">
                                                {{ __('dashboard/dashboard.reports.table_header.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border-color">
                                        @forelse ($latestReports ?? [] as $report)
                                            <tr class="cursor-pointer hover:bg-slate-50/50 transition-colors"
                                                data-modal-target="#reportDetailModal" data-report-id="{{ $report['id'] }}"
                                                data-report-subject="{{ $report['type']['name'] ?? '' }}"
                                                data-report-message="{{ $report['message'] }}"
                                                data-report-status-key="{{ $report['status'] }}"
                                                data-property='@json($report['property'])'>
                                                <td class="p-3"
                                                    data-label="{{ __('dashboard/dashboard.reports.table_header.subject') }}">
                                                    <div>
                                                        <div class="font-semibold text-text-primary">
                                                            {{ $report['type']['name'] }}</div>
                                                        <div class="text-xs text-text-secondary mt-1">
                                                            {{ __('dashboard/dashboard.reports.on_property') }}
                                                            {{ Str::limit($report['property']['location'] ?? '...', 30) }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-3 text-center"
                                                    data-label="{{ __('dashboard/dashboard.reports.table_header.status') }}">
                                                    <span id="report-badge-{{ $report['id'] }}"
                                                        class="text-xs font-semibold px-2 py-1 rounded-full @if ($report['status'] == 'pending') bg-warning-light text-warning @else bg-success-light text-success @endif">
                                                        {{ __('dashboard/dashboard.status.' . ($report['status'] ?? 'pending')) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-text-secondary py-10">
                                                    {{ __('dashboard/dashboard.reports.no_recent') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif --}}

                    @if ($user->isAdmin())
                        <!-- Latest Reports - New Design -->
                        <div class="bg-card-bg border border-border-color rounded-xl shadow-custom mb-6">
                            <div class="flex justify-between items-center px-5 py-4 border-b border-border-color">
                                <span
                                    class="text-lg font-semibold text-text-primary">{{ __('dashboard/dashboard.reports.latest_title') }}</span>
                                <a href="{{ route('dashboard.reports.index') }}"
                                    class="text-sm font-medium text-accent-primary hover:text-text-primary transition-colors">{{ __('dashboard/dashboard.general.view_all') }}</a>
                            </div>

                            <!-- Desktop View -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs uppercase bg-gray-50 text-text-secondary">
                                        <tr>
                                            <th scope="col" class="p-4">
                                                {{ __('dashboard/reports.list.th_reporter') }}</th>
                                            <th scope="col" class="p-4">
                                                {{ __('dashboard/reports.list.th_subject') }}</th>
                                            <th scope="col" class="p-4">
                                                {{ __('dashboard/reports.list.th_property') }}</th>
                                            <th scope="col" class="p-4">
                                                {{ __('dashboard/reports.list.th_report_date') }}</th>
                                            <th scope="col" class="p-4">
                                                {{ __('dashboard/reports.list.th_status') }}</th>
                                            <th scope="col" class="p-4 text-center">
                                                {{ __('dashboard/reports.list.th_actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border-color">
                                        @forelse ($latestReports ?? [] as $report)
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="p-4">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $report['reporter']['image_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($report['reporter']['name']) . '&background=random' }}"
                                                            alt="{{ $report['reporter']['name'] }}"
                                                            class="w-10 h-10 object-cover rounded-full">
                                                        <div>
                                                            <div class="font-medium text-text-primary">
                                                                {{ $report['reporter']['name'] }}</div>
                                                            <div class="text-xs text-text-secondary">
                                                                {{ $report['reporter']['email'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-4 font-medium text-text-primary">
                                                    {{ $report['type']['name'] }}</td>
                                                <td class="p-4">
                                                    <a href="{{ route('dashboard.properties.edit', $report['property']['id']) }}"
                                                        target="_blank" class="text-accent-primary hover:underline">
                                                        {{ Str::limit($report['property']['location'] ?? '...', 30) }}
                                                    </a>
                                                </td>
                                                <td class="p-4 text-text-secondary">
                                                    {{ \Carbon\Carbon::parse($report['created_at'])->format('d M Y') }}
                                                </td>
                                                <td class="p-4">
                                                    <span class="status-badge status-{{ $report['report_status_id'] }}">
                                                        {{ $report['status']['name'] }}
                                                    </span>
                                                </td>
                                                <td class="p-4 text-center">
                                                    <button @click="openActionModal(@json($report))"
                                                        class="p-2 text-text-secondary hover:text-text-primary rounded-full">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-text-secondary py-10">
                                                    {{ __('dashboard/dashboard.reports.no_recent') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile View -->
                            <div class="block md:hidden p-4 space-y-4">
                                @forelse ($latestReports ?? [] as $report)
                                    <div class="bg-white border rounded-xl shadow-sm border-border-color p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="font-bold text-text-primary">{{ $report['type']['name'] }}</div>
                                            <button @click="openActionSheet(@json($report))"
                                                class="p-2 text-text-secondary hover:text-text-primary -m-2">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                        </div>
                                        <p class="text-sm text-text-secondary mb-3">
                                            {{ __('dashboard/reports.list.on_property') }}
                                            <a href="{{ route('dashboard.properties.edit', $report['property']['id']) }}"
                                                target="_blank" class="text-accent-primary hover:underline">
                                                {{ Str::limit($report['property']['location'] ?? '...', 30) }}
                                            </a>
                                        </p>

                                        <div
                                            class="flex items-center justify-between mt-3 pt-3 border-t border-border-color text-xs text-text-secondary">
                                            @if ($user->isAdmin())
                                                <span class="flex items-center gap-1.5"
                                                    title="{{ __('dashboard/reports.list.th_property_owner') }}">
                                                    <i class="fas fa-user-shield"></i>
                                                    <span>{{ $report['property']['owner']['name'] ?? 'N/A' }}</span>
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1.5"
                                                title="{{ __('dashboard/reports.list.th_report_date') }}">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>{{ \Carbon\Carbon::parse($report['created_at'])->format('d M Y') }}</span>
                                            </span>
                                        </div>
                                        <div
                                            class="flex items-center justify-between mt-3 pt-3 border-t border-border-color">
                                            <div class="flex items-center gap-2 text-sm">
                                                <img src="{{ $report['reporter']['image_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($report['reporter']['name']) . '&background=random' }}"
                                                    alt="{{ $report['reporter']['name'] }}"
                                                    class="w-8 h-8 object-cover rounded-full">
                                                <span>{{ $report['reporter']['name'] }}</span>
                                            </div>
                                            <span class="status-badge status-{{ $report['report_status_id'] }}">
                                                {{ $report['status']['name'] }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-text-secondary py-10">
                                        {{ __('dashboard/dashboard.reports.no_recent') }}
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </main>

                <!-- Sidebar -->
                <aside
                    class="w-full lg:w-[400px] lg:flex-shrink-0 lg:sticky lg:top-sticky-top self-start lg:max-h-[calc(100vh-theme(spacing.header-height)-3rem)] lg:overflow-y-auto">
                    @if ($user->type == 'user')
                        <div class="bg-card-bg border border-border-color rounded-xl shadow-custom mb-6">
                            <div class="px-5 py-4 border-b border-border-color text-lg font-semibold text-text-primary">
                                {{ __('dashboard/dashboard.appointments.upcoming_title') }}</div>
                            <div class="p-5">
                                <ul class="list-none p-0 m-0">
                                    @forelse ($upcomingAppointments ?? [] as $appointment)
                                        <li class="flex justify-between items-center gap-5 py-4 border-b border-border-color last:border-b-0 cursor-pointer hover:bg-slate-50"
                                            data-modal-target="#appointmentDetailModal"
                                            data-appointment-title="{{ $appointment->title }}"
                                            data-appointment-client="{{ $appointment->client->name ?? 'غير محدد' }}"
                                            data-appointment-date="{{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('l, j F Y') }}"
                                            data-appointment-time="{{ \Carbon\Carbon::parse($appointment->date)->format('g:i A') }}"
                                            data-appointment-notes="{{ $appointment->notes ?? __('dashboard/dashboard.modals.appointment_details.no_notes') }}">
                                            <div class="text-right">
                                                <p class="font-semibold text-text-primary mb-1">{{ $appointment->title }}
                                                </p>
                                                <p class="text-sm text-text-secondary">
                                                    {{ __('dashboard/dashboard.appointments.with_client') }}
                                                    {{ $appointment->client->name ?? '...' }}</p>
                                            </div>
                                            <div class="text-left">
                                                <div class="text-sm font-semibold text-text-primary">
                                                    {{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('j F') }}
                                                </div>
                                                <div class="text-xs text-text-secondary">
                                                    {{ \Carbon\Carbon::parse($appointment->date)->format('g:i A') }}</div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-center text-text-secondary py-3">
                                            {{ __('dashboard/dashboard.appointments.no_upcoming') }}</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endif
                    {{-- <div class="bg-card-bg border border-border-color rounded-xl shadow-custom mb-6">
                        <div class="flex justify-between items-center px-5 py-4 border-b border-border-color">
                            <span
                                class="text-lg font-semibold text-text-primary">{{ __('dashboard/dashboard.charts.property_types') }}</span>
                            <span
                                class="text-text-secondary font-medium">{{ $stats['total_properties']['metric'] ?? '' }}</span>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-col gap-5 pt-2">
                                @php $colors = ['blue', 'green', 'yellow', 'purple', 'red']; @endphp
                                @forelse ($propertyTypesData ?? [] as $index => $type)
                                    <div class="w-full">
                                        <div class="flex justify-between items-center mb-2">
                                            <span
                                                class="text-sm font-medium text-text-primary">{{ $type->type_name }}</span>
                                            <span
                                                class="text-sm font-medium text-text-secondary">%{{ $type->percentage }}</span>
                                        </div>
                                        <div class="w-full h-2 bg-border-color rounded-full overflow-hidden">
                                            <div class="h-full rounded-full @if ($colors[$index % count($colors)] === 'blue') bg-blue-500 @endif @if ($colors[$index % count($colors)] === 'green') bg-green-500 @endif @if ($colors[$index % count($colors)] === 'yellow') bg-yellow-500 @endif @if ($colors[$index % count($colors)] === 'purple') bg-purple-500 @endif @if ($colors[$index % count($colors)] === 'red') bg-red-500 @endif"
                                                style="width: {{ $type->percentage }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-text-secondary">
                                        {{ __('dashboard/dashboard.general.no_data') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div> --}}
                    <div class="bg-card-bg border border-border-color rounded-xl shadow-custom mb-6">
                        <a href="{{ route('dashboard.properties.index') }}">
                            <div class="flex justify-between items-center px-5 py-4 border-b border-border-color">
                                <span
                                    class="text-lg font-semibold text-text-primary">{{ __('dashboard/dashboard.charts.property_types') }}</span>
                                <span
                                    class="text-text-secondary font-medium">{{ $stats['total_properties']['metric'] ?? '' }}</span>
                            </div>
                        </a>
                        <div class="p-5">
                            <div class="flex flex-col gap-5 pt-2">
                                @php $colors = ['blue', 'green', 'yellow', 'purple', 'red']; @endphp
                                @forelse ($propertyTypesData ?? [] as $index => $type)
                                    <a href="{{ route('dashboard.properties.index', ['type_id' => $type->id]) }}">
                                        <div class="w-full">
                                            <div class="flex justify-between items-center mb-2">
                                                <span
                                                    class="text-sm font-medium text-text-primary">{{ $type->type_name }}</span>
                                                <span
                                                    class="text-sm font-medium text-text-secondary">%{{ $type->percentage }}</span>
                                            </div>
                                            <div class="w-full h-2 bg-border-color rounded-full overflow-hidden">

                                                <div class="h-full rounded-full 
                                                @if ($colors[$index % count($colors)] === 'blue') bg-blue-500 @endif 
                                                @if ($colors[$index % count($colors)] === 'green') bg-green-500 @endif 
                                                @if ($colors[$index % count($colors)] === 'yellow') bg-yellow-500 @endif 
                                                @if ($colors[$index % count($colors)] === 'purple') bg-purple-500 @endif 
                                                @if ($colors[$index % count($colors)] === 'red') bg-red-500 @endif"
                                                    style="width: {{ $type->percentage }}%">
                                                </div>
                                                {{-- <div class="h-full rounded-full @if ($colors[$index % count($colors)] === 'blue') bg-blue-500 @endif ..."
                                                    style="width: {{ $type->percentage }}%"></div> --}}
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
                                        <a href="{{ route('dashboard.reports.index', ['report_status_id' => $status->id]) }}">
                                            <div class="w-full">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span
                                                        class="text-sm font-medium text-text-primary">{{ __('dashboard/dashboard.status.' . $status->status) }}</span>
                                                    <span
                                                        class="text-sm font-medium text-text-secondary">%{{ $status->id }}</span>
                                                </div>
                                                <div class="w-full h-2 bg-border-color rounded-full overflow-hidden">
                                                    {{-- <div class="h-full rounded-full @if ($statusColors[$status->status] === 'yellow') bg-yellow-500 @endif ..."
                                                        style="width: {{ $status->percentage }}%"></div> --}}

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
                    {{-- @if ($user->isAdmin())
                        <div class="bg-card-bg border border-border-color rounded-xl shadow-custom">
                            <div class="flex justify-between items-center px-5 py-4 border-b border-border-color">
                                <span
                                    class="text-lg font-semibold text-text-primary">{{ __('dashboard/dashboard.charts.report_statuses') }}</span>
                                <span
                                    class="text-text-secondary font-medium">{{ $stats['total_reports']['metric'] ?? '' }}</span>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-col gap-5 pt-2">
                                    @php $statusColors = ['pending' => 'yellow', 'resolved' => 'green', 'rejected' => 'gray', 'in_progress' => 'blue']; @endphp
                                    @forelse ($reportStatusesData ?? [] as $status)
                                        <div class="w-full">
                                            <div class="flex justify-between items-center mb-2">
                                                <span
                                                    class="text-sm font-medium text-text-primary">{{ __('dashboard/dashboard.status.' . $status->status) }}</span>
                                                <span
                                                    class="text-sm font-medium text-text-secondary">%{{ $status->percentage }}</span>
                                            </div>
                                            <div class="w-full h-2 bg-border-color rounded-full overflow-hidden">
                                                <div class="h-full rounded-full @if ($statusColors[$status->status] === 'yellow') bg-yellow-500 @endif @if ($statusColors[$status->status] === 'green') bg-green-500 @endif @if ($statusColors[$status->status] === 'gray') bg-gray-500 @endif @if ($statusColors[$status->status] === 'blue') bg-blue-500 @endif"
                                                    style="width: {{ $status->percentage }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-text-secondary">
                                            {{ __('dashboard/dashboard.general.no_data') }}
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif --}}
                </aside>
            </div>
        </div>
    </div>

    <!-- Modals -->
    {{-- <div class="sa-modal fixed inset-0 z-1050 flex items-center justify-center invisible opacity-0" id="reportDetailModal"
        aria-hidden="true">
        <div class="sa-modal-overlay absolute inset-0 bg-slate-900/70 cursor-pointer" tabindex="-1" data-modal-close>
        </div>
        <div class="sa-modal-content relative bg-card-bg rounded-xl w-[95%] max-w-4xl shadow-xl transition-transform duration-200 scale-95 flex flex-col max-h-[90vh]"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-between p-5 border-b border-border-color">
                <h5 class="text-xl font-bold text-text-primary">
                    {{ __('dashboard/dashboard.modals.report_details.title') }}</h5>
                <button type="button" class="text-2xl text-text-secondary hover:text-text-primary"
                    data-modal-close>×</button>
            </div>
            <div class="overflow-hidden flex-grow">
                <div class="grid grid-cols-1 md:grid-cols-2 h-full">
                    <div class="p-6 overflow-y-auto md:border-e md:border-border-color">
                        <h6 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-4">
                            {{ __('dashboard/dashboard.modals.report_details.related_property_title') }}</h6>
                        <h4 class="text-lg font-bold text-text-primary mb-4" id="modal_property_location"></h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                            <div class="flex items-center gap-2"><i
                                    class="fas fa-money-bill-wave text-text-secondary w-5 text-center"></i><span
                                    class="text-base font-semibold text-success" id="modal_property_price"></span></div>
                            <div class="flex items-center gap-2"><i
                                    class="fas fa-tag text-text-secondary w-5 text-center"></i><span
                                    class="text-sm text-text-primary font-medium" id="modal_property_category"></span>
                            </div>
                            <div class="flex items-center gap-2"><i
                                    class="fas fa-building text-text-secondary w-5 text-center"></i><span
                                    class="text-sm text-text-primary font-medium" id="modal_property_type"></span></div>
                            <div class="flex items-center gap-2"><i
                                    class="fas fa-ruler-combined text-text-secondary w-5 text-center"></i><span
                                    class="text-sm text-text-primary font-medium" id="modal_property_size"></span></div>
                            <div class="flex items-center gap-2"><i
                                    class="fas fa-bed text-text-secondary w-5 text-center"></i><span
                                    class="text-sm text-text-primary font-medium" id="modal_property_rooms"></span></div>
                            <div class="flex items-center gap-2"><i
                                    class="fas fa-layer-group text-text-secondary w-5 text-center"></i><span
                                    class="text-sm text-text-primary font-medium" id="modal_property_floor"></span></div>
                        </div>
                        <h6 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">
                            {{ __('dashboard/dashboard.modals.report_details.property_description_label') }}</h6>
                        <div class="p-3 bg-page-bg rounded-lg border border-border-color text-text-secondary text-sm leading-relaxed max-h-[150px] overflow-y-auto"
                            id="modal_property_content"></div>
                        <a href="#" id="modal_property_url" target="_blank"
                            class="block w-full text-center mt-4 p-3 rounded-lg bg-accent-primary-light text-accent-primary font-semibold hover:bg-indigo-200 transition-colors">{{ __('dashboard/dashboard.modals.report_details.open_property_page_btn') }}</a>
                    </div>
                    <div class="p-6 flex flex-col bg-slate-50/50">
                        <form id="updateReportStatusForm" class="flex flex-col h-full">
                            <input type="hidden" name="report_id" id="modal_report_id">
                            <div class="mb-4">
                                <label
                                    class="block font-semibold text-text-secondary mb-1">{{ __('dashboard/dashboard.modals.report_details.report_subject_label') }}</label>
                                <p id="modal_report_subject" class="text-lg font-bold text-text-primary"></p>
                            </div>
                            <div class="mb-4 flex-grow flex flex-col">
                                <label
                                    class="block font-semibold text-text-secondary mb-1">{{ __('dashboard/dashboard.modals.report_details.report_description_label') }}</label>
                                <div class="p-3 bg-white rounded-lg border border-border-color text-text-secondary text-sm leading-relaxed flex-grow overflow-y-auto"
                                    id="modal_report_message"></div>
                            </div>
                            <div class="mt-auto">
                                <label for="modal_report_status_select"
                                    class="block font-semibold text-text-secondary mb-2">{{ __('dashboard/dashboard.modals.report_details.action_label') }}</label>
                                <select name="status" id="modal_report_status_select"
                                    class="form-select w-full p-3 border-gray-300 rounded-lg bg-white text-base focus:ring-accent-primary focus:border-accent-primary">
                                    <option value="pending">{{ __('dashboard/dashboard.status.pending') }}</option>
                                    <option value="resolved">{{ __('dashboard/dashboard.status.resolved') }}</option>
                                    <option value="rejected">{{ __('dashboard/dashboard.status.rejected') }}</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <button type="submit"
                                    class="w-full p-3 rounded-lg border-none bg-accent-primary text-white font-semibold cursor-pointer transition hover:bg-indigo-700 disabled:opacity-70 disabled:cursor-not-allowed"
                                    id="saveReportStatusBtn">{{ __('dashboard/dashboard.modals.report_details.save_btn') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

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
                <button type="button" @click="closeModal()" class="text-xl text-text-secondary hover:text-text-primary"
                    data-modal-close>×</button>
            </div>

            <!-- Modal Body -->
            <div class="flex-grow overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-5 h-full">

                    <!-- Right Column: Property Details -->
                    <div
                        class="lg:col-span-3 p-4 md:p-6 border-b lg:border-b-0 lg:border-r border-border-color overflow-y-auto">
                        <h4 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-3">
                            {{ __('dashboard\reports.list.report_details.related_property_title') }}</h4>
                        <div>
                            <h5 class="text-lg font-bold text-text-primary mb-3"
                                x-text="selectedReport?.property?.location || 'Untitled Property'"></h5>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-3 mb-4 text-sm">
                                <!-- Property details fields -->
                            </div>
                            <h6 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2 mt-4">
                                {{ __('dashboard\reports.list.report_details.property_description_label') }}</h6>
                            <div class="p-3 bg-page-bg rounded-lg border border-border-color text-text-secondary text-sm leading-relaxed max-h-[200px] overflow-y-auto"
                                x-text="selectedReport?.property?.content || '{{ __('dashboard/reports.list.no_description_available') }}'">
                            </div>
                        </div>
                    </div>

                    <!-- Left Column: Report Details & Actions -->
                    <div class="lg:col-span-2 p-4 md:p-6 bg-slate-50/50 overflow-y-auto">
                        <!-- Report details fields -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div
                class="flex flex-col sm:flex-row sm:justify-between gap-3 p-4 md:p-5 border-t border-border-color bg-slate-50/50 flex-shrink-0">
                <!-- Action buttons -->
            </div>
        </div>
    </div>

    <div class="sa-modal fixed inset-0 z-1050 flex items-center justify-center invisible opacity-0"
        id="appointmentDetailModal" aria-hidden="true">
        <div class="sa-modal-overlay absolute inset-0 bg-slate-900/70 cursor-pointer" tabindex="-1" data-modal-close>
        </div>
        <div class="sa-modal-content relative bg-card-bg rounded-xl w-[95%] max-w-lg shadow-xl transition-transform duration-200 scale-95 flex flex-col"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-between p-5 border-b border-border-color">
                <h5 class="text-xl font-bold text-text-primary">
                    {{ __('dashboard/dashboard.modals.appointment_details.title') }}
                </h5>
                <button type="button" class="text-2xl text-text-secondary hover:text-text-primary"
                    data-modal-close>×</button>
            </div>
            <div class="p-6 sm:p-8">
                <div class="flex flex-col gap-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-primary-light text-accent-primary flex items-center justify-center mt-1">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-text-secondary mb-0.5">
                                {{ __('dashboard/dashboard.modals.appointment_details.title_label') }}</div>
                            <div class="text-base font-semibold text-text-primary" id="modal_appointment_title"></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-primary-light text-accent-primary flex items-center justify-center mt-1">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-text-secondary mb-0.5">
                                {{ __('dashboard/dashboard.modals.appointment_details.client_label') }}</div>
                            <div class="text-base font-semibold text-text-primary" id="modal_appointment_client"></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-primary-light text-accent-primary flex items-center justify-center mt-1">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-text-secondary mb-0.5">
                                {{ __('dashboard/dashboard.modals.appointment_details.datetime_label') }}</div>
                            <div class="text-base font-semibold text-text-primary" id="modal_appointment_datetime"></div>
                        </div>
                    </div>
                    <div class="hidden items-start gap-4" id="notes-container">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-accent-primary-light text-accent-primary flex items-center justify-center mt-1">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-medium text-text-secondary mb-0.5">
                                {{ __('dashboard/dashboard.modals.appointment_details.notes_label') }}</div>
                            <div class="text-base font-semibold text-text-primary leading-relaxed"
                                id="modal_appointment_notes"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- Pass JS translations to the script --}}
    <script>
        const lang = @json(__('dashboard/dashboard.js'));
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clickable rows logic... (unchanged)
            document.querySelectorAll('tr.clickable-row[data-href]').forEach(row => {
                row.addEventListener('click', (e) => {
                    if (e.target.closest('a, button')) return;
                    const href = row.dataset.href;
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                });
                row.style.cursor = 'pointer';
            });

            // Modal handling logic... (unchanged)
            const openModal = (modal) => {
                if (!modal) return;
                modal.classList.add('is-visible');
                document.body.style.overflow = 'hidden';
            };
            const closeModal = (modal) => {
                if (!modal) return;
                modal.classList.remove('is-visible');
                document.body.style.overflow = '';
            };
            document.querySelectorAll('[data-modal-close]').forEach(button => {
                button.addEventListener('click', () => closeModal(button.closest('.sa-modal')));
            });
            window.addEventListener('keydown', e => {
                if (e.key === "Escape") closeModal(document.querySelector('.sa-modal.is-visible'));
            });

            // Modal data population logic... (unchanged)
            document.addEventListener('click', event => {
                const trigger = event.target.closest('[data-modal-target]');
                if (!trigger) return;
                event.preventDefault();
                const modal = document.querySelector(trigger.dataset.modalTarget);
                if (!modal) return;
                if (modal.id === 'reportDetailModal') {
                    modal.querySelector('#modal_report_id').value = trigger.dataset.reportId || '';
                    modal.querySelector('#modal_report_subject').textContent = trigger.dataset
                        .reportSubject || 'بلا عنوان';
                    modal.querySelector('#modal_report_message').textContent = trigger.dataset
                        .reportMessage || 'لا يوجد وصف مفصل عن الإبلاغ';
                    modal.querySelector('#modal_report_status_select').value = trigger.dataset
                        .reportStatusKey || 'pending';
                    const propertyData = JSON.parse(trigger.dataset.property || '{}');
                    if (propertyData && Object.keys(propertyData).length > 0) {
                        modal.querySelector('#modal_property_location').textContent = propertyData
                            .location || 'عقار غير محدد';
                        modal.querySelector('#modal_property_type').textContent = (propertyData.type ?
                            propertyData.type.name : null) || 'نوع غير محدد';
                        modal.querySelector('#modal_property_category').textContent = (propertyData
                            .category ? propertyData.category.name : null) || 'فئة غير محددة';
                        modal.querySelector('#modal_property_floor').textContent = propertyData.floor ||
                            'غير محدد';
                        modal.querySelector('#modal_property_price').textContent = propertyData
                            .price_display || 'السعر غير متاح';
                        modal.querySelector('#modal_property_size').textContent = (propertyData.size ||
                            'N/A') + ' م²';
                        modal.querySelector('#modal_property_rooms').textContent = (propertyData
                            .rooms_count || 0) + ' غرف';
                        modal.querySelector('#modal_property_content').textContent = propertyData.content ||
                            'لا يوجد وصف متاح.';
                        const propertyUrl = propertyData.slug ? `/property/${propertyData.slug}` : '#';
                        modal.querySelector('#modal_property_url').href = propertyUrl;
                    } else {
                        ['#modal_property_location', '#modal_property_type', '#modal_property_category',
                            '#modal_property_floor', '#modal_property_price', '#modal_property_size',
                            '#modal_property_rooms', '#modal_property_content'
                        ].forEach(id => {
                            const el = modal.querySelector(id);
                            if (el) el.textContent = 'غير متوفر';
                        });
                        modal.querySelector('#modal_property_url').href = '#';
                    }
                } else if (modal.id === 'appointmentDetailModal') {
                    modal.querySelector('#modal_appointment_title').textContent = trigger.dataset
                        .appointmentTitle || 'بلا عنوان';
                    modal.querySelector('#modal_appointment_client').textContent = trigger.dataset
                        .appointmentClient || 'غير محدد';
                    modal.querySelector('#modal_appointment_datetime').textContent =
                        `${trigger.dataset.appointmentDate || ''}, ${trigger.dataset.appointmentTime || ''}`;
                    const notes = trigger.dataset.appointmentNotes;
                    const notesContainer = modal.querySelector('#notes-container');
                    const noNotesText =
                        "{{ __('dashboard/dashboard.modals.appointment_details.no_notes') }}";
                    if (notes && notes.trim() !== '' && notes.trim() !== noNotesText) {
                        notesContainer.classList.remove('hidden');
                        notesContainer.classList.add('flex');
                        notesContainer.querySelector('#modal_appointment_notes').textContent = notes;
                    } else {
                        notesContainer.classList.add('hidden');
                        notesContainer.classList.remove('flex');
                    }
                }
                openModal(modal);
            });

            // AJAX Form Submission with translated messages
            const reportForm = document.getElementById('updateReportStatusForm');
            if (reportForm) {
                reportForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const saveBtn = document.getElementById('saveReportStatusBtn');
                    const originalBtnText = saveBtn.textContent;
                    saveBtn.textContent = lang.saving; // Using translated text
                    saveBtn.disabled = true;
                    const formData = new FormData(this);
                    const reportId = formData.get('report_id');
                    const actionUrl = "{{-- route('api.reports.update_status') --}}";
                    if (!actionUrl.includes('/')) {
                        console.error('Action URL for updating report status is not defined.');
                        alert(lang.error_undefined_route); // Using translated text
                        saveBtn.textContent = originalBtnText;
                        saveBtn.disabled = false;
                        return;
                    }
                    fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData,
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            const badge = document.getElementById(`report-badge-${reportId}`);
                            if (badge) {
                                badge.textContent = data.new_status_text;
                                badge.className = 'text-xs font-semibold px-2 py-1 rounded-full ' +
                                    data.new_status_class;
                            }
                            closeModal(document.getElementById('reportDetailModal'));
                        } else {
                            alert(data.message || lang.error_generic); // Using translated text
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert(lang.error_connection_failed); // Using translated text
                    }).finally(() => {
                        saveBtn.textContent = originalBtnText;
                        saveBtn.disabled = false;
                    });
                });
            }
        });
    </script>
@endpush
