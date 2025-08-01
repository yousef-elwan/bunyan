<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

$user = Auth::user();

$menuItems = [
    [
        'section' => 'main',
        'user_type' => 'user,admin',
        'label' => __('dashboard/layouts.aside.dashboard'),
        'icon' => 'fas fa-tachometer-alt',
        'url' => route('dashboard.home'),
        'active' => Request::routeIs('dashboard.home'),
    ],
    [
        'section' => 'main',
        'user_type' => 'admin',
        'label' => __('dashboard/layouts.aside.config'),
        'icon' => 'fas fa-tools',
        'url' => '#',
        'submenu' => [
            [
                'label' => __('dashboard/layouts.aside.floor'),
                'icon' => 'fas fa-layer-group',
                'url' => route('dashboard.floor.index'),
                'active' => Request::routeIs('dashboard.floor.index'),
            ],
            [
                'label' => __('dashboard/layouts.aside.condition'),
                'icon' => 'fas fa-tools',
                'url' => route('dashboard.condition.index'),
                'active' => Request::routeIs('dashboard.condition.index'),
            ],
            [
                'label' => __('dashboard/layouts.aside.orientation'),
                'icon' => 'fas fa-compass',
                'url' => route('dashboard.orientation.index'),
                'active' => Request::routeIs('dashboard.orientation.index'),
            ],
            [
                'label' => __('dashboard/layouts.aside.category'),
                'icon' => 'fas fa-th-large',
                'url' => route('dashboard.category.index'),
                'active' => Request::routeIs('dashboard.category.index'),
            ],
            [
                'label' => __('dashboard/layouts.aside.type'),
                'icon' => 'fas fa-house-user',
                'url' => route('dashboard.type.index'),
                'active' => Request::routeIs('dashboard.type.index'),
            ],
        ],
        'active' => Request::routeIs('dashboard.floor.*', 'dashboard.condition.*', 'dashboard.condition.*', 'dashboard.orientation.*', 'dashboard.category.*', 'dashboard.type.*'),
    ],

    [
        'section' => 'main',
        'user_type' => 'admin',
        'label' => __('dashboard/layouts.aside.properties'),
        'icon' => 'fas fa-list-ul',
        'url' => route('dashboard.properties.index'),
        'active' => Request::routeIs('dashboard.properties.index'),
    ],

    [
        'section' => 'main',
        'user_type' => 'user',
        'label' => __('dashboard/layouts.aside.properties'),
        'icon' => 'fas fa-building',
        'url' => '#',
        'submenu' => [
            [
                'label' => $user->isAdmin() ? __('dashboard/layouts.aside.all_Properties') : __('dashboard/layouts.aside.my_Properties'),
                'icon' => 'fas fa-list-ul',
                'url' => route('dashboard.properties.index'),
                'active' => Request::routeIs('dashboard.properties.index'),
            ],
            [
                'label' => __('dashboard/layouts.aside.addProp'),
                'icon' => 'fas fa-plus-circle',
                'url' => route('dashboard.properties.create'),
                'active' => Request::routeIs('dashboard.properties.create'),
            ],
            [
                'label' => __('dashboard/layouts.aside.blacklist'),
                'icon' => 'fas fa-plus-circle',
                'url' => route('dashboard.properties.blacklist'),
                'active' => Request::routeIs('dashboard.properties.blacklist'),
            ],
            [
                'label' => __('dashboard/layouts.aside.favorite'),
                'icon' => 'fas fa-plus-circle',
                'url' => route('dashboard.properties.favorite'),
                'active' => Request::routeIs('dashboard.properties.favorite'),
            ],
        ],
        'active' => Request::routeIs('dashboard.properties.*'),
    ],
    [
        'section' => 'main',
        'user_type' => 'admin',
        'label' => __('dashboard/layouts.aside.users'),
        'icon' => 'fas fa-users',
        'url' => route('dashboard.users.index'),
        'active' => Request::routeIs('dashboard.users.index'),
    ],
    [
        'section' => 'main',
        'user_type' => 'admin',
        'label' => __('dashboard/layouts.aside.subscriptions'),
        'icon' => 'fas fa-id-card',
        'url' => route('dashboard.subscriptions.index'),
        'active' => Request::routeIs('dashboard.subscriptions.index'),
    ],
    [
        'section' => 'main',
        'user_type' => 'user',
        'label' => __('dashboard/layouts.aside.chatting'),
        'icon' => 'fas fa-comments',
        'url' => route('dashboard.chat'),
        'active' => Request::routeIs('dashboard.chat'),
    ],
    [
        'section' => 'main',
        'user_type' => 'user,admin',
        'label' => __('dashboard/layouts.aside.notifications'),
        'icon' => 'fas fa-bell',
        'url' => route('dashboard.notifications'),
        'active' => Request::routeIs('dashboard.notifications'),
    ],
    [
        'section' => 'main',
        'user_type' => 'admin',
        'label' => __('dashboard/layouts.aside.reports'),
        'icon' => 'fas fa-flag',
        'url' => route('dashboard.reports.index'),
        'active' => Request::routeIs('dashboard.reports.index'),
    ],
    [
        'section' => 'main',
        'user_type' => 'user,admin',
        'label' => __('dashboard/layouts.aside.profile'),
        'icon' => 'fas fa-user',
        'url' => route('dashboard.profile'),
        'active' => Request::routeIs('dashboard.profile'),
    ],
];

function renderMenu($menu, $user)
{
    $userType = $user->type;
    foreach ($menu as $item) {
        if (!in_array($userType, explode(',', $item['user_type']))) {
            continue;
        }

        $hasSubmenu = !empty($item['submenu']);
        $submenuItems = $hasSubmenu ? array_filter($item['submenu'], fn($sub) => in_array($userType, explode(',', $sub['user_type'] ?? 'user,admin'))) : [];

        if ($hasSubmenu && count($submenuItems) === 0) {
            continue; // لا تعرض العنصر إذا كانت القائمة الفرعية فارغة بعد الفلترة
        }

        $isParentOfActive = false;
        if ($hasSubmenu) {
            foreach ($submenuItems as $sub) {
                if ($sub['active'] ?? false) {
                    $isParentOfActive = true;
                    break;
                }
            }
        }

        $isActive = ($item['active'] ?? false && !$hasSubmenu) || $isParentOfActive;
        $linkClasses = 'flex items-center px-4 py-3 text-gray-300 hover:bg-sidebar-hover hover:text-white transition-colors duration-200' . ($isActive ? ' bg-sidebar-active' : '');

        if ($hasSubmenu) {
            echo '<li x-data="{ open: ' . ($isParentOfActive ? 'true' : 'false') . ' }">';
            echo '<a href="#" @click.prevent="open = !open" class="' . htmlspecialchars($linkClasses) . '">';
            echo '<i class="' . htmlspecialchars($item['icon']) . ' w-8 text-center text-lg flex-shrink-0"></i>';
            echo '<span class="ms-2 flex-1 whitespace-nowrap" x-show="sidebarOpen || sidebarHover" x-transition>' . htmlspecialchars($item['label']) . '</span>';
            echo '<i class="fas fa-chevron-down text-xs ms-auto transition-transform duration-300" x-show="sidebarOpen || sidebarHover" :class="{ \'rotate-180\': open }"></i>';
            echo '</a>';

            echo '<ul x-show="open && (sidebarOpen || sidebarHover)" x-transition x-cloak class="bg-sidebar-submenu">';
            foreach ($submenuItems as $subitem) {
                $subLinkClasses = 'flex items-center w-full ps-14 pe-4 py-2.5 text-sm text-gray-300 hover:text-white transition-colors' . ($subitem['active'] ?? false ? ' text-blue-400 font-semibold' : '');
                $iconClass = $subitem['active'] ?? false ? 'fa-dot-circle text-blue-400' : 'fa-circle';
                echo '<li><a href="' . htmlspecialchars($subitem['url']) . '" class="' . htmlspecialchars($subLinkClasses) . '"><i class="far ' . $iconClass . ' text-[8px] me-3"></i><span>' . htmlspecialchars($subitem['label']) . '</span></a></li>';
            }
            echo '</ul>';
        } else {
            echo '<li>';
            echo '<a href="' . htmlspecialchars($item['url']) . '" class="' . htmlspecialchars($linkClasses) . '">';
            echo '<i class="' . htmlspecialchars($item['icon']) . ' w-8 text-center text-lg flex-shrink-0"></i>';
            echo '<span class="ms-2 flex-1 whitespace-nowrap" x-show="sidebarOpen || sidebarHover" x-transition>' . htmlspecialchars($item['label']) . '</span>';
            echo '</a>';
        }
        echo '</li>';
    }
}
?>

<aside id="sidebar"
    class="fixed top-0 left-0 rtl:left-auto rtl:right-0 h-screen bg-sidebar-bg text-white z-40 flex flex-col transition-all duration-300 ease-in-out"
    @mouseenter="sidebarHover = true" @mouseleave="sidebarHover = false"
    :class="{
        'w-sidebar-open': sidebarOpen || sidebarHover,
        'w-sidebar-closed': !sidebarOpen && !sidebarHover,

        'translate-x-0': sidebarOpen,
        '-translate-x-full rtl:translate-x-full lg:translate-x-0 rtl:lg:translate-x-0': !sidebarOpen
    }">

    {{-- Logo and Close Button (لا تغيير هنا) --}}
    <div class="h-header-height flex items-center justify-center px-4 border-b border-gray-700 flex-shrink-0 relative">
            <div class="font-bold whitespace-nowrap overflow-hidden transition-opacity duration-200"
        :class="(sidebarOpen || sidebarHover) ? 'text-xl' : 'text-lg'">
        <a href="{{ url('/') }}" target="_blank" class="block text-gray-50 hover:text-white transition-colors duration-200">
            <span x-show="sidebarOpen || sidebarHover">{{ $web_config['company_name'] ?? 'Dashboard' }}</span>
            <span x-show="!sidebarOpen && !sidebarHover">{{ mb_substr($web_config['company_name'] ?? 'D', 0, 1) }}</span>
        </a>

    </div>
        <button @click="sidebarOpen = false"
            class="absolute top-1/2 -translate-y-1/2 end-4 text-gray-400 hover:text-white lg:hidden"
            aria-label="Close sidebar" x-cloak>
            <i class="fas fa-times text-2xl"></i>
        </button>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden sidebar-scrollbar">
        <ul class="py-2">
            <?php
            // ==========================================================
            // الآن نقوم باستدعاء الدالة هنا لبناء القائمة الشجرية
            // بدلاً من تكرار الكود باستخدام @foreach
            // ==========================================================
            renderMenu($menuItems, $user);
            ?>
        </ul>
    </nav>

    {{-- User Profile Section (لا تغيير هنا) --}}
    <div class="border-t border-gray-700" x-data="{ userMenuOpen: false }">
        <div @click="userMenuOpen = !userMenuOpen"
            class="p-4 flex items-center cursor-pointer hover:bg-sidebar-hover transition-colors">
            <img id="slideBarUserAvatar" src="{{ $user->image_url ?? 'https://via.placeholder.com/40' }}" alt="User Avatar"
                class="w-10 h-10 rounded-full object-cover flex-shrink-0">
            <div class="ms-3 flex-1 overflow-hidden whitespace-nowrap" x-show="sidebarOpen || sidebarHover"
                x-transition>
                <span id="sidebarUserName" class="font-medium text-white">{{ $user->name }}</span>
            </div>
            <i class="fas fa-chevron-up text-xs transition-transform duration-300" x-show="sidebarOpen || sidebarHover"
                :class="{ 'rotate-180': userMenuOpen }" x-transition></i>
        </div>
        <div x-show="userMenuOpen && (sidebarOpen || sidebarHover)" x-transition x-cloak class="bg-sidebar-submenu">
            <ul>
                <li>
                    <a href="{{ url('/') }}" target="_blank"
                        class="flex items-center px-4 py-2.5 text-gray-300 hover:text-white transition-colors">
                        <i class="fas fa-home me-3 w-8 text-center"></i>
                        <span>{{ __('dashboard/layouts.aside.back_to_website') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.profile') }}"
                        class="flex items-center px-4 py-2.5 text-gray-300 hover:text-white transition-colors">
                        <i class="fas fa-user-circle me-3 w-8 text-center"></i>
                        <span>{{ __('dashboard/layouts.aside.my_profile') }}</span>
                    </a>
                </li>
                <li>
                    <button
                        class="js-logoutButton flex items-center px-4 py-2.5 text-gray-300 hover:text-white transition-colors w-full cursor-pointer">
                        <i class="fas fa-sign-out-alt me-3 w-8 text-center"></i>
                        <span>{{ __('dashboard/layouts.aside.logout') }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</aside>
