@extends('dashboard.layouts.default')

@section('title', __('dashboard/subscriptions.page_title'))

@push('css_or_js')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <style>
        .ts-control {
            border-radius: 0.5rem !important;
            border-color: #e5e7eb !important;
            padding: 0.5rem 0.75rem !important;
        }

        .ts-dropdown {
            border-color: #e5e7eb !important;
        }

        .ts-dropdown .option {
            padding: 0.5rem 0.75rem !important;
        }
    </style>
    <script>
        Object.assign(window.AppConfig.routes, {
            'users.search': "{{ route('api.subscriptions.users.search') }}",
            'subscriptions.history': "{{ route('api.subscriptions.history', ['user' => ':user']) }}",
            'subscriptions.store': "{{ route('api.subscriptions.store', ['user' => ':user']) }}",
        });
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/subscriptions')));
    </script>
    @vite('resources/js/alpine/dashboard/subscriptions/list.js')
@endpush

@section('content')
    <div class="flex flex-col min-h-screen bg-page-bg" x-data="subscriptionManager()">
        <!-- Header -->
        <div class="bg-card-bg shadow-sm rounded-lg mb-6 p-4">
            {{-- Breadcrumbs --}}
            <nav class="mb-2" aria-label="Breadcrumb">
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
            <h1 class="text-2xl font-bold text-text-primary" x-text="translate('page_title')"></h1>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: User Search and Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-card-bg rounded-xl shadow-custom border border-border-color p-5">
                    <h2 class="text-lg font-bold text-text-primary mb-3" x-text="translate('select_user_title')"></h2>
                    {{-- <select id="user-select" :placeholder="translate('search_placeholder')"></select> --}}
                    <select x-ref="userSelect" :placeholder="translate('search_placeholder')"></select>
                </div>

                <!-- FIX: Use x-if to prevent accessing null properties -->
                <template x-if="selectedUser">
                    <div class="bg-card-bg rounded-xl shadow-custom border border-border-color p-5" x-transition>
                        <div class="flex items-center gap-4">
                            <img :src="selectedUser.avatar_url || `https://ui-avatars.com/api/?name=${selectedUser.name}`"
                                class="w-16 h-16 rounded-full bg-gray-200">
                            <div>
                                <h3 class="font-bold text-lg text-text-primary" x-text="selectedUser.name"></h3>
                                <p class="text-text-secondary" x-text="selectedUser.email"></p>
                            </div>
                        </div>
                        <hr class="my-4 border-border-color">
                        <div>
                            <h4 class="font-semibold mb-2" x-text="translate('current_subscription')"></h4>
                            <div x-show="selectedUser.subscription_end" class="text-sm space-y-1">
                                <p><strong x-text="translate('ends_on')"></strong>: <span
                                        x-text="formatDate(selectedUser.subscription_end)"></span></p>
                                <p :class="getRemainingTime(selectedUser.subscription_end).classes"
                                    x-text="getRemainingTime(selectedUser.subscription_end).text"></p>
                            </div>
                            <div x-show="!selectedUser.subscription_end" class="text-sm text-text-secondary"
                                x-text="translate('no_active_subscription')"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Right Side: Subscription History and Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-card-bg rounded-xl shadow-custom border border-border-color">
                    <div class="p-5 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-text-primary" x-text="translate('history')"></h2>
                        <button @click="isAddModalOpen = true" :disabled="!selectedUser"
                            class="bg-accent-primary text-white px-4 py-2 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-opacity-90 transition-all">
                            <i class="fas fa-plus mr-2"></i> <span x-text="translate('add_new')"></span>
                        </button>
                    </div>
                    {{-- <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left text-text-secondary">
                                <tr>
                                    <th class="p-3 font-semibold" x-text="translate('package')"></th>
                                    <th class="p-3 font-semibold" x-text="translate('start_date')"></th>
                                    <th class="p-3 font-semibold" x-text="translate('end_date')"></th>
                                    <th class="p-3 font-semibold" x-text="translate('added_by')"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="isLoadingHistory">
                                    <tr>
                                        <td colspan="4" class="p-8 text-center"><i
                                                class="fas fa-spinner fa-spin text-2xl text-accent-primary"></i></td>
                                    </tr>
                                </template>
                                <template x-if="!isLoadingHistory && history.length > 0">
                                    <template x-for="item in history" :key="item.id">
                                        <tr class="border-t border-border-color">
                                            <td class="p-3" x-text="item.package_name"></td>
                                            <td class="p-3" x-text="formatDate(item.start_date)"></td>
                                            <td class="p-3" x-text="formatDate(item.end_date)"></td>
                                            <td class="p-3" x-text="item.admin ? item.admin.name : 'N/A'"></td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="!isLoadingHistory && history.length === 0">
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-text-secondary">
                                            <div x-show="selectedUser" x-text="translate('no_history')"></div>
                                            <div x-show="!selectedUser" x-text="translate('select_user_to_view_history')">
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div> --}}
                    <!-- START: Responsive Table/Cards Container -->

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left text-text-secondary">
                                <tr>
                                    <th class="p-3 font-semibold" x-text="translate('package')"></th>
                                    <th class="p-3 font-semibold" x-text="translate('start_date')"></th>
                                    <th class="p-3 font-semibold" x-text="translate('end_date')"></th>
                                    <th class="p-3 font-semibold" x-text="translate('added_by')"></th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body">
                                {{-- JS will populate this --}}
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile Cards -->
                    <div id="history-cards-container" class="p-4 space-y-3 md:hidden">
                        {{-- JS will populate this --}}
                    </div>

                    <template x-if="isLoadingHistory">
                        <div class="p-8 text-center">
                            <i class="fas fa-spinner fa-spin text-2xl text-accent-primary"></i>
                        </div>
                    </template>

                    <template x-if="!isLoadingHistory && history.length === 0">
                        <div class="p-8 text-center text-text-secondary">
                            <span x-show="selectedUser" x-text="translate('no_history')"></span>
                            <span x-show="!selectedUser" x-text="translate('select_user_to_view_history')"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Add Subscription Modal -->
        <div x-show="isAddModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="isAddModalOpen = false" x-transition class="fixed inset-0 bg-black/50"></div>
            <div x-show="isAddModalOpen" x-transition class="relative bg-card-bg rounded-xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4"><span x-text="translate('add_new_subscription_for')"></span> <span
                        class="text-accent-primary" x-text="selectedUser?.name"></span></h3>
                <form @submit.prevent="addSubscription()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1" x-text="translate('package')"></label>
                            <select x-model="newSubscription.package" @change="updateFormFromPackage()"
                                class="w-full p-2 border border-border-color rounded-lg">
                                <option value="" x-text="translate('select_package')"></option>
                                @foreach ($packages as $package)
                                    <option value="{{ json_encode($package) }}">{{ $package['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- NEW: Start Date Field -->
                        <div>
                            <label class="block text-sm font-medium mb-1" x-text="translate('start_date')"></label>
                            <input type="date" x-model="newSubscription.start_date"
                                class="w-full p-2 border border-border-color rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1" x-text="translate('price')"></label>
                            <input type="number" step="0.01" x-model.number="newSubscription.price"
                                class="w-full p-2 border border-border-color rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" x-text="translate('notes')"></label>
                            <textarea x-model="newSubscription.notes" rows="3" class="w-full p-2 border border-border-color rounded-lg"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 bg-gray-200 rounded-lg"
                            x-text="translate('cancel')"></button>
                        <button type="submit" :disabled="isSubmitting"
                            class="px-4 py-2 bg-accent-primary text-white rounded-lg disabled:opacity-50">
                            <span x-show="!isSubmitting" x-text="translate('confirm_add')"></span>
                            <span x-show="isSubmitting"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- <div x-show="isAddModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="isAddModalOpen = false" x-transition class="fixed inset-0 bg-black/50"></div>
            <div x-show="isAddModalOpen" x-transition class="relative bg-card-bg rounded-xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4"><span x-text="translate('add_new_subscription_for')"></span> <span
                        class="text-accent-primary" x-text="selectedUser?.name"></span></h3>
                <form @submit.prevent="addSubscription()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1" x-text="translate('package')"></label>
                            <select x-model="newSubscription.package" @change="updateFormFromPackage()"
                                class="w-full p-2 border border-border-color rounded-lg">
                                <option value="" x-text="translate('select_package')"></option>
                                @foreach ($packages as $package)
                                    <option value="{{ json_encode($package) }}">{{ $package['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" x-text="translate('price')"></label>
                            <input type="number" step="0.01" x-model.number="newSubscription.price"
                                class="w-full p-2 border border-border-color rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" x-text="translate('notes')"></label>
                            <textarea x-model="newSubscription.notes" rows="3" class="w-full p-2 border border-border-color rounded-lg"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 bg-gray-200 rounded-lg"
                            x-text="translate('cancel')"></button>
                        <button type="submit" :disabled="isSubmitting"
                            class="px-4 py-2 bg-accent-primary text-white rounded-lg disabled:opacity-50">
                            <span x-show="!isSubmitting" x-text="translate('confirm_add')"></span>
                            <span x-show="isSubmitting"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div> --}}
    </div>
@endsection
