@extends('dashboard.layouts.default')

@section('title', __('dashboard/profile.page_title'))

@push('css_or_js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <style>
        .iti {
            width: 100%;
        }

        .iti__input {
            direction: ltr;
            text-align: left;
        }
    </style>
@endpush

@section('content')

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
                                {{-- UPDATED: Added focus state for accessibility --}}
                                <a href="{{ $breadcrumb['url'] }}"
                                    class="truncate hover:text-accent-primary hover:underline focus:outline-none focus:ring-2 focus:ring-accent-primary rounded"
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
                {{ __('dashboard/profile.page_title') }}
            </h1>
        </div>
    </div>


    {{-- sm:p-6 lg:p-8  --}}
    <div class="bg-page-bg" x-data="profilePage()">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: Profile Card & Tabs (becomes main content on mobile) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Profile Card -->
                <div class="bg-card-bg p-6 rounded-xl shadow-custom border border-border-color text-center">
                    {{-- <div class="relative w-28 h-28 mx-auto mb-4 group">
                        <img src="{{ $user->image_url }}" alt="{{ $user->first_name[0] }}" id="profileAvatarPreview"
                            class="w-full h-full rounded-full object-cover border-4 border-white shadow-md">
                        <label for="avatarUpload"
                            class="absolute bottom-1 right-1 flex items-center justify-center w-8 h-8 bg-accent-primary text-white rounded-full cursor-pointer border-2 border-white hover:bg-opacity-90 transition-all"
                            title="{{ __('dashboard/profile.changeAvatar') }}">
                            <i class="fas fa-camera text-sm"></i>
                        </label>
                        <input type="file" id="avatarUpload" class="hidden" accept="image/*">
                    </div> --}}

                    <div class="relative w-28 h-28 mx-auto mb-4 group rounded-full focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-accent-primary"
                        x-data="{ showDeleteButton: {{ $user->hasCustomAvatar() ? 'true' : 'false' }} }">
                        <img src="{{ $user->image_url }}" alt="{{ $user->first_name[0] }}" id="profileAvatarPreview"
                            class="w-full h-full rounded-full object-cover border-4 border-white shadow-md">
                        <label for="avatarUpload"
                            class="absolute bottom-1 right-1 flex items-center justify-center w-8 h-8 bg-accent-primary text-white rounded-full cursor-pointer border-2 border-white hover:bg-opacity-90 transition-all"
                            title="{{ __('dashboard/profile.changeAvatar') }}">
                            <i class="fas fa-camera text-sm"></i>
                        </label>
                        <input type="file" id="avatarUpload" class="hidden" accept="image/*">
                        <button type="button" id="deleteAvatarBtn" x-show="showDeleteButton"
                            class="delete-avatar-button absolute bottom-1 left-1 flex items-center justify-center w-8 h-8 bg-red-600 text-white rounded-full cursor-pointer border-2 border-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all"
                            title="{{ __('dashboard/profile.deleteAvatar') }}">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>

                    {{-- UPDATED: Added a container for name and status badge --}}
                    {{-- <div class="flex items-center justify-center gap-2"> --}}
                    <h2 id="profilePageNameDisplay" class="text-xl font-bold text-text-primary">{{ $user->name }}</h2>

                    {{-- FINAL REVISED DESIGN: Alert box with the thick side border style --}}
                    @if ($user->is_blacklisted)
                        <div class="mt-4 p-4 text-left bg-red-50 border-s-4 border-red-400 rounded-r-lg" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-ban fa-lg text-red-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-red-800">
                                        {{ __('dashboard/profile.blacklisted_alert_title') }}</h4>
                                    <p class="mt-1 text-sm text-red-700">
                                        {{ __('dashboard/profile.blacklisted_alert_message') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-border-color text-left space-y-3">
                        <p class="flex items-center text-sm text-text-secondary"><i
                                class="fas fa-envelope fa-fw w-5 me-2 text-accent-primary"></i> <span
                                class="truncate">{{ $user->email ?? '' }}</span></p>
                        <p class="flex items-center text-sm text-text-secondary"><i
                                class="fas fa-phone fa-fw w-5 me-2 text-accent-primary"></i> <span
                                id="profilePagePhoneDisplay">{{ $user->mobile ?? '' }}</span></p>
                    </div>
                </div>

                <!-- Tabs Navigation for Desktop -->
                <div class="hidden lg:block bg-card-bg rounded-xl shadow-custom border border-border-color p-2">
                    <nav class="flex flex-col space-y-1">
                        {{-- UPDATED: Added focus state to all nav buttons --}}
                        <button @click="setActiveTab('tab-personal-info')"
                            :class="{ 'bg-accent-primary-light text-accent-primary': activeTab === 'tab-personal-info' }"
                            class="w-full text-left cursor-pointer flex items-center gap-3 px-4 py-2.5 rounded-lg font-medium text-sm text-text-secondary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-accent-primary transition-colors">
                            <i class="fas fa-user-circle fa-fw w-5"></i>
                            {{ __('dashboard/profile.Personal_information') }}
                        </button>
                        <button @click="setActiveTab('tab-security')"
                            :class="{ 'bg-accent-primary-light text-accent-primary': activeTab === 'tab-security' }"
                            class="w-full text-left cursor-pointer flex items-center gap-3 px-4 py-2.5 rounded-lg font-medium text-sm text-text-secondary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-accent-primary transition-colors">
                            <i class="fas fa-shield-alt fa-fw w-5"></i>
                            {{ __('dashboard/profile.Privacy') }}
                        </button>
                        <button @click="setActiveTab('tab-notifications')"
                            :class="{ 'bg-accent-primary-light text-accent-primary': activeTab === 'tab-notifications' }"
                            class="w-full text-left cursor-pointer flex items-center gap-3 px-4 py-2.5 rounded-lg font-medium text-sm text-text-secondary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-accent-primary transition-colors">
                            <i class="fas fa-bell fa-fw w-5"></i>
                            {{ __('dashboard/profile.notification_settings') }}
                        </button>
                        <button @click="setActiveTab('tab-preferences')"
                            :class="{ 'bg-accent-primary-light text-accent-primary': activeTab === 'tab-preferences' }"
                            class="w-full text-left cursor-pointer flex items-center gap-3 px-4 py-2.5 rounded-lg font-medium text-sm text-text-secondary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-accent-primary transition-colors">
                            <i class="fas fa-palette fa-fw w-5"></i>
                            {{ __('dashboard/profile.them') }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Right Column: Tabs Content -->
            <div class="lg:col-span-8">
                <div class="bg-card-bg rounded-xl shadow-custom border border-border-color">
                    <!-- Tabs Navigation for Mobile (Scrollable) -->
                    <div class="lg:hidden border-b border-border-color">
                        <nav class="flex overflow-x-auto -mb-px" aria-label="Tabs">
                            {{-- UPDATED: Added focus state to mobile nav buttons --}}
                            <button @click="setActiveTab('tab-personal-info')"
                                :class="{ 'text-accent-primary border-accent-primary': activeTab === 'tab-personal-info', 'border-transparent text-text-secondary hover:text-accent-primary hover:border-gray-300': activeTab !== 'tab-personal-info' }"
                                class="whitespace-nowrap cursor-pointer py-4 px-6 border-b-2 font-medium text-sm focus:outline-none focus:z-10 focus:ring-2 focus:ring-accent-primary rounded-t-md">
                                {{ __('dashboard/profile.Personal_information') }}
                            </button>
                            <button @click="setActiveTab('tab-security')"
                                :class="{ 'text-accent-primary border-accent-primary': activeTab === 'tab-security', 'border-transparent text-text-secondary hover:text-accent-primary hover:border-gray-300': activeTab !== 'tab-security' }"
                                class="whitespace-nowrap cursor-pointer py-4 px-6 border-b-2 font-medium text-sm focus:outline-none focus:z-10 focus:ring-2 focus:ring-accent-primary rounded-t-md">
                                {{ __('dashboard/profile.Privacy') }}
                            </button>
                            <button @click="setActiveTab('tab-notifications')"
                                :class="{ 'text-accent-primary border-accent-primary': activeTab === 'tab-notifications', 'border-transparent text-text-secondary hover:text-accent-primary hover:border-gray-300': activeTab !== 'tab-notifications' }"
                                class="whitespace-nowrap cursor-pointer py-4 px-6 border-b-2 font-medium text-sm focus:outline-none focus:z-10 focus:ring-2 focus:ring-accent-primary rounded-t-md">
                                {{ __('dashboard/profile.notification_settings') }}
                            </button>
                            <button @click="setActiveTab('tab-preferences')"
                                :class="{ 'text-accent-primary border-accent-primary': activeTab === 'tab-preferences', 'border-transparent text-text-secondary hover:text-accent-primary hover:border-gray-300': activeTab !== 'tab-preferences' }"
                                class="whitespace-nowrap cursor-pointer py-4 px-6 border-b-2 font-medium text-sm focus:outline-none focus:z-10 focus:ring-2 focus:ring-accent-primary rounded-t-md">
                                {{ __('dashboard/profile.them') }}
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content Panels -->
                    <div class="p-6">
                        <div x-show="activeTab === 'tab-personal-info'" x-cloak>
                            <h3 class="text-lg font-semibold text-text-primary mb-4 pb-4 border-b border-border-color">
                                {{ __('dashboard/profile.edit_info_personal') }}</h3>
                            <form id="updateInfoForm" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name"
                                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.fName') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name"
                                            value="{{ old('first_name', $user->first_name) }}"
                                            class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                            required>
                                        <div class="text-sm text-danger mt-1" id="first_name_error"></div>
                                    </div>
                                    <div>
                                        <label for="last_name"
                                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.lName') }}</label>
                                        <input type="text" id="last_name" name="last_name"
                                            class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                            value="{{ old('last_name', $user->last_name) }}">
                                        <div class="text-sm text-danger mt-1" id="last_name_error"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="email"
                                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.email') }}</label>
                                        <input type="email" id="email" name="email"
                                            class="block w-full text-sm border-gray-300 rounded-md shadow-sm bg-gray-100"
                                            value="{{ old('email', $user->email) }}" readonly disabled>
                                    </div>
                                    <div>
                                        <label for="mobile"
                                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.phone') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="tel" name="mobile"
                                            class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary"
                                            value="{{ old('mobile', $user->mobile) }}" required id="mobile">
                                        <div class="text-sm text-danger mt-1" id="mobile_error"></div>
                                    </div>
                                </div>
                                <div class="pt-4 flex justify-end gap-x-3">
                                    {{-- UPDATED: Added focus states to form buttons --}}
                                    <button type="reset"
                                        class="inline-flex items-center justify-center px-4 py-2 cursor-pointer text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                                        {{ __('common.cancel') }}
                                    </button>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 cursor-pointer text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-primary transition-colors duration-200">
                                        {{ __('dashboard/profile.save') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div x-show="activeTab === 'tab-security'" x-cloak>
                            <h3 class="text-lg font-semibold text-text-primary mb-4 pb-4 border-b border-border-color">
                                {{ __('dashboard/profile.privacy_settings') }}</h3>
                            <form id="updatePasswordForm" class="space-y-4">
                                <div>
                                    <label for="old_password"
                                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.oldPass') }}</label>
                                    <div class="relative">
                                        <input type="password" id="old_password" name="old_password"
                                            class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary pe-10">
                                        {{-- ACCESSIBILITY & UX UPGRADE: Changed from <i> to <button> --}}
                                        <button type="button"
                                            class="password-toggle-icon absolute top-1/2 -translate-y-1/2 end-0 flex items-center justify-center h-full w-10 text-gray-400 hover:text-accent-primary focus:outline-none focus:ring-2 focus:ring-accent-primary rounded-e-md"
                                            aria-label="Toggle password visibility">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="text-sm text-danger mt-1" id="old_password_error"></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="new_password"
                                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.newPass') }}</label>
                                        <div class="relative">
                                            <input type="password" id="new_password" name="new_password"
                                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary pe-10">
                                            <button type="button"
                                                class="password-toggle-icon absolute top-1/2 -translate-y-1/2 end-0 flex items-center justify-center h-full w-10 text-gray-400 hover:text-accent-primary focus:outline-none focus:ring-2 focus:ring-accent-primary rounded-e-md"
                                                aria-label="Toggle password visibility">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="text-sm text-danger mt-1" id="new_password_error"></div>
                                    </div>
                                    <div>
                                        <label for="new_password_confirmation"
                                            class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.conPass') }}</label>
                                        <div class="relative">
                                            <input type="password" id="new_password_confirmation"
                                                name="new_password_confirmation"
                                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary pe-10">
                                            <button type="button"
                                                class="password-toggle-icon absolute top-1/2 -translate-y-1/2 end-0 flex items-center justify-center h-full w-10 text-gray-400 hover:text-accent-primary focus:outline-none focus:ring-2 focus:ring-accent-primary rounded-e-md"
                                                aria-label="Toggle password visibility">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4 flex justify-end gap-x-3">
                                    <button type="reset"
                                        class="inline-flex items-center justify-center px-4 py-2 cursor-pointer text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">{{ __('common.cancel') }}</button>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 cursor-pointer text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-primary transition-colors duration-200">{{ __('dashboard/profile.save_settings') }}</button>
                                </div>
                            </form>
                        </div>

                        <div x-show="activeTab === 'tab-notifications'" x-cloak>
                            <h3 class="text-lg font-semibold text-text-primary mb-4 pb-4 border-b border-border-color">
                                {{ __('dashboard/profile.notification_settings') }}</h3>
                            <form id="notificationSettingsForm" class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-text-primary">
                                        {{ __('dashboard/profile.notification_types') }}
                                    </label>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="email_notifications" name="email_notifications"
                                                value="1" @checked(old('email_notifications', $user->email_notifications))
                                                class="h-4 w-4 text-accent-primary rounded border-gray-300 focus:ring-accent-primary  cursor-pointer">
                                            <label for="email_notifications" class="ms-2 text-sm text-text-secondary  cursor-pointer">
                                                {{ __('dashboard/profile.email_notifications') }}
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            {{-- تم تصحيح الاسم هنا ليتطابق مع قاعدة البيانات --}}
                                            <input type="checkbox" id="newsletter_notifications"
                                                name="newsletter_notifications" value="1"
                                                @checked(old('newsletter_notifications', $user->newsletter_notifications))
                                                class="h-4 w-4 text-accent-primary rounded border-gray-300 focus:ring-accent-primary  cursor-pointer">
                                            <label for="newsletter_notifications"
                                                class="ms-2 text-sm text-text-secondary  cursor-pointer">
                                                {{-- يمكنك تغيير هذا النص إذا أردت --}}
                                                {{ __('dashboard/profile.newsletter_subscription') }}
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="pt-4 flex justify-end gap-x-3">
                                    <button type="reset"
                                        class="inline-flex items-center justify-center px-4 py-2 cursor-pointer text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">{{ __('common.cancel') }}</button>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 cursor-pointer text-sm font-medium text-white rounded-lg bg-accent-primary hover:bg-opacity-90 disabled:opacity-50 transition-colors duration-200">{{ __('dashboard/profile.save_settings') }}</button>
                                </div>
                            </form>
                        </div>

                        <div x-show="activeTab === 'tab-preferences'" x-cloak>
                            <h3 class="text-lg font-semibold text-text-primary mb-4 pb-4 border-b border-border-color">
                                {{ __('dashboard/profile.them_setting') }}</h3>
                            <form id="languageSwitchForm">
                                <div>
                                    <label for="languageSelect"
                                        class="block mb-2 text-sm font-medium text-text-primary">{{ __('dashboard/profile.lan') }}</label>
                                    <select id="languageSelect"
                                        class="block w-full md:w-1/2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-accent-primary focus:border-accent-primary">
                                        @foreach ($langs as $lang)
                                            <option value="{{ route('language.switch', ['locale' => $lang['locale']]) }}"
                                                @selected(session('locale') == $lang['locale'])>{{ $lang['name'] }}
                                                ({{ strtoupper($lang['locale']) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        // window.AppConfig = {
        //     translations: @json(__('dashboard/profile')),
        //     user: {
        //         mobile: "{{ $user->mobile }}"
        //     }
        // };
        // window.AppConfig.i18n = {
        //     ...(window.AppConfig.i18n ?? {}),
        //     json_encode(__('dashboard/profile'))
        // }

        window.AppConfig = window.AppConfig || {};
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/profile')));

        window.AppConfig.routes = {
            ...(window.AppConfig.routes ?? {}),
            'api.auth.updateInfo': "{{ route('api.auth.updateInfo') }}",
            'api.auth.updatePassword': "{{ route('api.auth.updatePassword') }}",
            'api.auth.updateAvatar': "{{ route('api.auth.updateAvatar') }}",
            'api.auth.deleteAvatar': "{{ route('api.auth.deleteAvatar') }}",
            'api.auth.updateNotifications': "{{ route('api.auth.updateNotifications') }}",
        }
    </script>
    @vite(['resources/js/alpine/dashboard/profile/main.js'])
@endpush
