@extends('dashboard.layouts.default')

@section('title', __('dashboard/user.list.page_title'))

@php
    $direction = config('app.locale') == 'ar' ? 'rtl' : 'ltr';
@endphp

@push('css_or_js')
    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eef2f7;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-active {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .status-inactive {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .blacklisted-badge {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .not-blacklisted {
            color: #4b5563;
        }

        .subscription-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .subscription-active {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .subscription-expired {
            background-color: #fffbeb;
            color: #f59e0b;
        }

        .subscription-none {
            background-color: #f1f5f9;
            color: #475569;
        }
    </style>

    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.routes = window.AppConfig.routes || {};
        window.AppConfig.i18n = window.AppConfig.i18n || {};
        Object.assign(window.AppConfig.routes, {
            'api.users': "{{ route('api.users') }}",
            'api.users.destroy': "{{ route('api.users.destroy', ['user' => ':user']) }}",
            'api.users.show': "{{ route('api.users.show', ['user' => ':user']) }}",
            'api.users.blacklist': "{{ route('api.users.blacklist', ['user' => ':user']) }}",
            'api.users.unblacklist': "{{ route('api.users.unblacklist', ['user' => ':user']) }}",
            'api.users.update_status': "{{ route('api.users.update_status', ['user' => ':user']) }}"
        });
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/user.list')));
    </script>
    @vite('resources/js/alpine/dashboard/user/list.js')
@endpush

@section('content')
    <div class="flex flex-col min-h-screen bg-page-bg" x-data="userManagement()"
        @open-action-modal.window="openActionModal($event.detail)" @open-action-sheet.window="openActionSheet($event.detail)"
        @open-user-modal.window="openUserModal($event.detail)"
        @prompt-for-status.window="promptForStatusChange($event.detail)"
        @prompt-for-blacklist.window="promptForBlacklist($event.detail)"
        @confirm-delete-user.window="deleteUserWithConfirmation($event.detail)">

        <!-- Header Section -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-4 p-4">
            <nav class="mb-2 max-w-[80vw]" aria-label="Breadcrumb">
                <ol class="flex items-center flex-nowrap overflow-hidden text-sm text-text-secondary">
                    @isset($breadcrumbs)
                        @foreach ($breadcrumbs as $breadcrumb)
                            <li class="flex items-center min-w-0 {{ $loop->last ? 'flex-shrink-0' : '' }}">
                                @if ($breadcrumb['url'])
                                    <a href="{{ $breadcrumb['url'] }}"
                                        class="truncate hover:text-accent-primary hover:underline"
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
                <h1 class="text-2xl font-bold text-text-primary">{{ __('dashboard/user.list.page_title') }}</h1>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-4 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex-grow flex items-center gap-3">
                    <!-- Search Bar - Always Visible -->
                    <div class="relative flex-1 max-w-xs">
                        <input type="text" x-model.debounce.300ms="filters.search" @input="applyFilters()"
                            placeholder="{{ __('dashboard/user.list.filter_name') }}"
                            class="w-full p-2 pl-10 text-sm border rounded-lg border-border-color">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>

                    <!-- Desktop-Only Filters -->
                    <div class="hidden md:flex items-center gap-3">
                        <select class="w-32 p-2 text-sm border rounded-lg border-border-color" x-model="filters.is_active"
                            @change="applyFilters()">
                            <option value="">{{ __('dashboard/user.list.select_status') }}</option>
                            <option value="1">{{ __('dashboard/user.list.status_active') }}</option>
                            <option value="0">{{ __('dashboard/user.list.status_inactive') }}</option>
                        </select>
                        <select class="w-32 p-2 text-sm border rounded-lg border-border-color"
                            x-model="filters.is_blacklisted" @change="applyFilters()">
                            <option value="">{{ __('dashboard/user.list.select_blacklist') }}</option>
                            <option value="1">{{ __('dashboard/user.list.blacklisted') }}</option>
                            <option value="0">{{ __('dashboard/user.list.not_blacklisted') }}</option>
                        </select>
                    </div>

                    <!-- Advanced Search Button - Always Visible -->
                    <button @click="showAdvancedFilters = true"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium border rounded-lg text-accent-primary border-accent-primary hover:bg-accent-primary-light">
                        <i class="fas fa-filter"></i>
                        <span class="hidden md:inline">{{ __('dashboard/user.list.advanced_search_button') }}</span>
                    </button>
                </div>

                <div class="flex-shrink-0">
                    <button @click="fetchUsers(currentPage)"
                        class="flex items-center justify-center gap-2 h-10 w-10 sm:w-auto sm:px-4 text-white rounded-lg bg-accent-primary hover:bg-opacity-90">
                        <i class="fas fa-sync-alt" :class="{ 'animate-spin': isLoading }"></i>
                        <span
                            class="hidden sm:inline font-medium text-sm">{{ __('dashboard/user.list.refresh_button') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Filter Drawer (MODIFIED to include mobile filters) -->
        <div x-show="showAdvancedFilters" x-cloak class="fixed inset-0 z-40 bg-black/50"
            @click="showAdvancedFilters = false"></div>
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
                <!-- Mobile-only filters appear here first -->
                <div class="space-y-4 md:hidden">
                    <div class="mb-4">
                        <label
                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/user.list.th_status') }}</label>
                        <select class="w-full p-2 text-sm border rounded-lg border-border-color"
                            x-model="filters.is_active">
                            <option value="">{{ __('dashboard/user.list.select_status') }}</option>
                            <option value="active">{{ __('dashboard/user.list.status_active') }}</option>
                            <option value="inactive">{{ __('dashboard/user.list.status_inactive') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label
                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/user.list.th_blacklist') }}</label>
                        <select class="w-full p-2 text-sm border rounded-lg border-border-color"
                            x-model="filters.is_blacklisted">
                            <option value="">{{ __('dashboard/user.list.select_blacklist') }}</option>
                            <option value="1">{{ __('dashboard/user.list.blacklisted') }}</option>
                            <option value="0">{{ __('dashboard/user.list.not_blacklisted') }}</option>
                        </select>
                    </div>
                    <hr class="border-border-color">
                </div>

                <!-- Common advanced filters -->
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/user.list.filter_email') }}</label>
                    <input type="email" x-model="filters.email" placeholder="user@example.com"
                        class="w-full p-2 text-sm border rounded-lg border-border-color">
                </div>
                <div class="mb-4">
                    <label
                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/user.list.filter_phone') }}</label>
                    <input type="tel" x-model="filters.mobile" placeholder="+966500000000"
                        class="w-full p-2 text-sm border rounded-lg border-border-color">
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-text-primary">تاريخ الانضمام</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" x-model="filters.subscription_start"
                            class="w-full p-2 text-sm border rounded-lg border-border-color">
                        <input type="date" x-model="filters.subscription_end"
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
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 text-text-secondary">
                        <tr>
                            <th class="p-4">{{ __('dashboard/user.list.th_user') }}</th>
                            <th class="p-4">{{ __('dashboard/user.list.th_subscription_end') }}</th>
                            <th class="p-4">{{ __('dashboard/user.list.th_status') }}</th>
                            <th class="p-4">{{ __('dashboard/user.list.th_blacklist') }}</th>
                            <th class="p-4">{{ __('dashboard/user.list.th_join_date') }}</th>
                            <th class="p-4 text-center">{{ __('dashboard/user.list.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-color">
                        {{-- JS will populate this section --}}
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div id="userCardsContainer" class="p-4 space-y-4 md:hidden">
                {{-- JS will populate this section --}}
            </div>

            <div x-show="!isLoading && allData.length === 0" x-cloak class="py-10 text-center">
                <p class="text-text-secondary">{{ __('dashboard/user.list.no_users_found') }}</p>
            </div>

            <div x-show="isLoading" class="p-4" x-cloak>
                <div class="hidden md:block">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="flex items-center p-4 space-x-4 animate-pulse">
                            <div class="flex items-center flex-1 gap-3">
                                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            </div>
                            <div class="flex-1 h-6 bg-gray-200 rounded-full"></div>
                            <div class="flex-1 h-6 bg-gray-200 rounded-full"></div>
                            <div class="flex-1 h-4 bg-gray-200 rounded"></div>
                            <div class="flex-1 h-4 bg-gray-200 rounded"></div>
                            <div class="flex-1 h-8 w-8 bg-gray-200 rounded-full mx-auto"></div>
                        </div>
                    @endfor
                </div>
                <div class="md:hidden space-y-4">
                    @for ($i = 0; $i < 3; $i++)
                        <div
                            class="flex items-center gap-4 p-4 bg-white border rounded-xl shadow-sm border-border-color animate-pulse">
                            <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div id="paginationControls" class="flex flex-wrap justify-center gap-2 py-4"
            x-show="!isLoading && paginationData && paginationData.total > paginationData.per_page" x-cloak></div>

        <!-- Actions Modal (for Desktop) -->
        <div x-show="isActionModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="isActionModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="isActionModalOpen = false" class="fixed inset-0 bg-black/50">
            </div>
            <div x-show="isActionModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-sm bg-card-bg rounded-xl shadow-xl p-5">
                <template x-if="selectedUserForAction">
                    <div>
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-bold text-text-primary" x-text="selectedUserForAction.name"></h3>
                            <p class="text-sm text-text-secondary" x-text="selectedUserForAction.email"></p>
                        </div>
                        <div class="space-y-2">
                            <button
                                @click="$dispatch('open-user-modal', selectedUserForAction.id); isActionModalOpen = false"
                                class="flex items-center w-full gap-3 p-3 text-left rounded-lg hover:bg-page-bg">
                                <i class="fas fa-eye fa-fw text-gray-500"></i>
                                <span>{{ __('dashboard/user.list.view_tooltip') }}</span>
                            </button>
                            <button
                                @click="$dispatch('prompt-for-status', selectedUserForAction); isActionModalOpen = false"
                                class="flex items-center w-full gap-3 p-3 text-left rounded-lg hover:bg-page-bg">
                                <i class="fas fa-power-off fa-fw text-gray-500"></i>
                                <span
                                    x-text="selectedUserForAction.is_active	? '{{ __('dashboard/user.list.deactivate_user') }}' : '{{ __('dashboard/user.list.activate_user') }}'"></span>
                            </button>
                            <button
                                @click="$dispatch('prompt-for-blacklist', selectedUserForAction); isActionModalOpen = false"
                                class="flex items-center w-full gap-3 p-3 text-left rounded-lg hover:bg-page-bg">
                                <i class="fas fa-user-slash fa-fw text-gray-500"></i>
                                <span
                                    x-text="selectedUserForAction.is_blacklisted ? '{{ __('dashboard/user.list.unblacklist_button') }}' : '{{ __('dashboard/user.list.blacklist_button') }}'"></span>
                            </button>
                            <div class="border-t border-border-color !my-3"></div>
                            <button
                                @click="$dispatch('confirm-delete-user', selectedUserForAction.id); isActionModalOpen = false"
                                class="flex items-center w-full gap-3 p-3 text-left rounded-lg text-danger hover:bg-red-50">
                                <i class="fas fa-trash fa-fw"></i>
                                <span>{{ __('dashboard/user.list.delete_tooltip') }}</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Action Sheet (for Mobile) -->
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
                    <template x-for="action in userActions" :key="action.label">
                        <button @click="action.handler(); isActionSheetOpen = false" :class="action.classes"
                            class="w-full flex items-center gap-4 p-3 text-lg rounded-lg text-left">
                            <i :class="action.icon" class="w-6 text-center"></i><span x-text="action.label"></span>
                        </button>
                    </template>
                </div>
                <div class="mt-4">
                    <button @click="isActionSheetOpen = false"
                        class="w-full bg-card-bg p-3 text-lg rounded-lg font-semibold text-text-primary">{{ __('dashboard/user.list.cancel_button') }}</button>
                </div>
            </div>
        </div>

        <!-- User Details Modal -->
        <div x-show="isUserModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="isUserModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="isUserModalOpen = false" class="fixed inset-0 bg-black/50"></div>
            <div x-show="isUserModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-2xl bg-card-bg rounded-lg shadow-xl p-6">
                <template x-if="selectedUser">
                    <div>
                        <div class="flex items-start justify-between">
                            <h3 class="text-xl font-semibold text-text-primary">
                                {{ __('dashboard/user.list.user_details_title') }}</h3>
                            <button @click="isUserModalOpen = false"
                                class="p-1 -m-1 text-text-secondary hover:text-text-primary"><i
                                    class="fas fa-times"></i></button>
                        </div>
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1 flex flex-col items-center">
                                <img :src="selectedUser.image_url ||
                                    'https://ui-avatars.com/api/?name=' + selectedUser.name"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-border-color mb-4"
                                    :alt="selectedUser.name">
                                <h4 class="text-lg font-bold text-text-primary" x-text="selectedUser.name"></h4>
                                <p class="text-text-secondary" x-text="selectedUser.email"></p>
                                <div class="mt-4 w-full text-sm">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-text-secondary">
                                            {{ __('dashboard/user.list.th_phone') }}:
                                        </span>
                                        <span x-text="selectedUser.mobile || ``"></span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-text-secondary">
                                            {{ __('dashboard/user.list.th_join_date') }}
                                            :
                                        </span>
                                        <span x-text="new Date(selectedUser.created_at).toLocaleDateString()"></span>
                                    </div>

                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-text-secondary">
                                            {{ __('dashboard/user.list.th_subscription_end') }}
                                            :
                                        </span>
                                        <span x-text="new Date(selectedUser.subscription_end).toLocaleDateString()"></span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-text-secondary">
                                            {{ __('dashboard/user.list.th_blacklist') }}
                                            :
                                        </span>
                                        <span class="blacklisted-badge" x-show="selectedUser.is_blacklisted"><i
                                                class="fas fa-user-slash"></i>
                                            {{ __('dashboard/user.list.blacklisted') }}</span>
                                        <span x-show="!selectedUser.is_blacklisted"
                                            class="text-sm text-text-secondary">{{ __('dashboard/user.list.not_blacklisted') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2 space-y-6">
                                <div>
                                    <h5 class="text-lg font-semibold text-text-primary mb-3">
                                        {{ __('dashboard/user.list.properties_label') }}</h5>
                                    <div class="bg-page-bg rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-text-secondary">
                                            </span>
                                            <span class="text-lg font-bold text-text-primary"
                                                x-text="selectedUser.properties_count"></span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="bg-card-bg rounded-lg p-3 text-center">
                                                <div class="text-2xl font-bold text-accent-primary"
                                                    x-text="selectedUser.properties_active || 0"></div>
                                                <div class="text-sm text-text-secondary">
                                                    {{ __('dashboard/user.list.active') }}
                                                </div>
                                            </div>
                                            <div class="bg-card-bg rounded-lg p-3 text-center">
                                                <div class="text-2xl font-bold text-warning"
                                                    x-text="selectedUser.properties_pending || 0"></div>
                                                <div class="text-sm text-text-secondary">
                                                    {{ __('dashboard/user.list.inactive') }}
                                                </div>
                                            </div>
                                            <div class="bg-card-bg rounded-lg p-3 text-center">
                                                <div class="text-2xl font-bold text-danger"
                                                    x-text="selectedUser.properties_rejected || 0"></div>
                                                <div class="text-sm text-text-secondary">
                                                    {{ __('dashboard/user.list.rejected') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="!selectedUser">
                    <div class="flex justify-center items-center p-8">
                        <i class="fas fa-spinner fa-spin text-3xl text-accent-primary"></i>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
