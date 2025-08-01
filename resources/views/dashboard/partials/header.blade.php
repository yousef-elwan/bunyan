<header class="bg-white border-b border-header-border px-4 h-header-height flex items-center sticky top-0 z-30">
    {{-- زر القائمة (Burger Menu) --}}
    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none" aria-label="Toggle Sidebar">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <div class="flex-1"></div>

    {{-- قسم معلومات المستخدم --}}
    <div class="organization-info-wrapper relative" x-data="{ dropdownOpen: false }">
        <div @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
            class="flex items-center cursor-pointer px-3 py-1 rounded hover:bg-gray-100 transition-colors"
            tabindex="0" role="button" aria-haspopup="true" :aria-expanded="dropdownOpen">
            <img id="headerUserAvatar"
                class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-2 rtl:mr-0 rtl:ml-2"
                src="{{ $user->image_url }}" alt="">
            {{-- <div
                class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-2 rtl:mr-0 rtl:ml-2">
                {{ mb_substr($user->name, 0, 1) }}
            </div> --}}
            <span id="headerUserName" class="font-medium text-gray-700 hidden sm:block">{{ $user->name }}</span>
            <i class="fas fa-chevron-down text-xs text-gray-500 ml-2 rtl:ml-0 rtl:mr-2 transition-transform duration-200"
                :class="{ 'rotate-180': dropdownOpen }"></i>
        </div>

        <div x-show="dropdownOpen" x-transition
            class="absolute right-0 rtl:right-auto rtl:left-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50"
            x-cloak>
            <ul>
                <li>
                    <a href="{{ route('dashboard.profile') }}"
                        class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user-circle text-gray-500 mr-3 rtl:mr-0 rtl:ml-3"></i>
                        {{ $user->name }}
                    </a>
                </li>
                <li>
                    <button
                        class="js-logoutButton flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 w-full cursor-pointer">
                        <i class="fas fa-sign-out-alt text-gray-500 mr-3 rtl:mr-0 rtl:ml-3"></i>
                        {{ __('dashboard/layouts.aside.logout') }}
                    </button>
                    {{-- <a href="{{ route('auth.logout') }}"
                        class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt text-gray-500 mr-3 rtl:mr-0 rtl:ml-3"></i>
                        {{ __('dashboard/layouts.aside.logout') }}
                    </a> --}}
                </li>
            </ul>
        </div>
    </div>
</header>
