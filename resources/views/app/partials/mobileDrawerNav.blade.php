<!-- Mobile Drawer Navigation (Repurposed Sidebar) -->
<aside class="mobile-drawer-nav" id="mainMobileDrawerNav" aria-hidden="true">
    <div class="mobile-drawer-header">
        <div class="sidebar-logo-container">
            <img src="{{ $web_config['company_logo'] }}" alt="logo" class="app-logo-img-drawer">
        </div>
        <button class="btn-icon btn-close-drawer-main" id="closeMainMobileDrawerBtn" aria-label="Close Menu">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <nav class="main-nav-drawer" id="mainNavDrawerContent">
        <ul class="drawer-main-menu">
            <li>
                <a href="{{ route('home') }}" class="active-header-link">
                    {{ __('app/layouts.header.home') }}
                </a>
            </li>

            {{-- <li class="has-submenu-header">
                <a href="#" aria-haspopup="true" aria-expanded="false">
                    {{ __('app/layouts.header.aboutUs') }}
                    <i class="fas fa-chevron-down nav-submenu-chevron"></i>
                </a>
                <ul class="drawer-submenu-list">
                    <li><a href="#">تاريخ النادي</a>
                    </li>
                    <li><a href="#">الإدارة</a></li>
                    <li class="has-submenu-header">
                        <a href="#" aria-haspopup="true" aria-expanded="false">
                            الفرق <i class="fas fa-chevron-right nav-submenu-chevron nested-chevron"></i>
                        </a>
                        <ul class="drawer-submenu-list nested-drawer-submenu">
                            <li><a href="#">الفريق الأول</a>
                            </li>
                            <li><a href="#">الشباب</a></li>
                            <li><a href="#">الناشئين</a></li>
                        </ul>
                    </li>
                </ul>
            </li> --}}

            @foreach ($types ?? [] as $type)
                <li class="has-submenu-header">
                    <a href="#" aria-haspopup="true" aria-expanded="false" class="link">
                        <?= $type['name'] ?>
                        <i class="fas fa-chevron-down nav-submenu-chevron"></i>
                    </a>
                    <ul class="drawer-submenu-list">
                        @foreach ($categories as $category)
                            <li>
                                <a
                                    href="{{ route('search', ['type_id' => $type['id'], 'category_id' => $category['id']]) }}">
                                    {{ $category['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
            <li>
                <a href="{{ route('contact-us') }}"></a>
            </li>
        </ul>

        <li class="drawer-nav-divider" id="drawerNavDivider" style="display:none;"></li>

        @auth
            <ul class="drawer-static-links">
                <li>
                    <a href="{{ route('dashboard.profile') }}">
                        <i class="fas fa-user-circle"></i>
                        {{ __('app/layouts.header.profile') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.notifications') }}">
                        <i class="fas fa-bell"></i>
                        {{ __('app/layouts.header.notifications') }}
                    </a>
                </li>
            </ul>
        @endauth
    </nav>
</aside>
