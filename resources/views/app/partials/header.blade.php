<header class="app-header modern-header-style-1" id="mainAppHeader">
    <div class="header-group header-group-start">

        {{-- user avatar --}}
        <div class="user-profile-minimal-wrapper">
            <div class="user-profile-minimal" id="userProfileMinimalHeader" tabindex="0" role="button"
                aria-haspopup="true" aria-expanded="false">

                @auth
                    <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-header">
                @endauth

                @guest
                    <img src="{{ asset('images/defaults/user.png') }}" alt="User Name" class="user-avatar-header">
                @endguest
            </div>
            <div class="header-dropdown user-avatar-dropdown" id="userAvatarDropdownHeader">
                <ul>
                    @auth
                        <li>
                            <a href="{{ route('dashboard.profile') }}">
                                <i class="fas fa-user-circle"></i>
                                {{ __('app/layouts.header.profile') }}
                            </a>
                        </li>
                        <li>
                            <button id="logoutButton">
                                <i class="fas fa-sign-out-alt"></i>
                                {{ __('app/layouts.header.logout') }}
                            </button>
                        </li>
                    @endauth
                    @guest
                        <li>
                            <button type="button" id="openAuthDialogBtn">
                                <i class="fas fa-sign-out-alt"></i>
                                {{ __('app/layouts.header.login') }}
                            </button>
                        </li>
                    @endguest

                </ul>
            </div>
        </div>

        <div class="language-switcher-header">
            <button class="lang-btn current-lang" id="toggleLangBtnHeader" aria-haspopup="true" aria-expanded="false">
                {{ $defaultLang }}
                <i class="fas fa-chevron-down lang-chevron"></i>
            </button>
            <div class="lang-dropdown header-dropdown" id="langDropdownHeader">
                @foreach ($language as $lang)
                    <a href="{{ route('language.switch', ['locale' => $lang['locale']]) }}">
                        ({{ $lang['name'] }})
                        {{ $lang['locale'] }}
                    </a>
                @endforeach
            </div>
        </div>

        @auth
            <div class="notification-wrapper-header">
                <button class="header-action-btn notification-link-header" id="notificationLinkHeader"
                    aria-label="Notifications" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="notification-dot" id="notificationDotHeader" style="display: block;"></span>
                    <!-- Example: dot visible -->
                </button>
                <div class="header-dropdown notifications-dropdown-header" id="notificationsDropdownContentHeader">
                    <div class="notifications-dropdown-header-title">
                        Notifications <a href="notifications.html" class="view-all-notifications">View All</a>
                    </div>
                    <ul class="notifications-dropdown-list" id="headerNotificationsList">
                        <!-- JS or server will populate this -->
                        <li class="unread-notification">
                            <a href="#">
                                <strong>
                                    New Lead!
                                </strong>
                                John D. is interested in Villa #123.
                                <small>
                                    2m ago
                                </small>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Property "Downtown Loft" updated.
                                <small>
                                    1h ago
                                </small>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Maintenance scheduled for
                                tomorrow.
                                <small>
                                    5h ago
                                </small>
                            </a>
                        </li>
                        {{-- <li class="no-notifications-header-item hidden">You
                            have no new notifications.</li> --}}
                    </ul>
                    <div class="notifications-dropdown-footer">
                        <button class="btn btn-link btn-small" id="markAllHeaderNotificationsRead">
                            Mark all as read
                        </button>
                    </div>
                </div>
            </div>
        @endauth
    </div>

    <nav class="header-main-nav-style-1" id="desktopNavStyle1">
        <ul>
            <li>
                <a href="{{ route('home') }}" class="active-header-link">
                    {{ __('app/layouts.header.home') }}
                </a>
            </li>
            <li class="has-submenu-header">
                {{-- <a href="#" aria-haspopup="true" aria-expanded="false">
                    من نحن <i class="fas fa-chevron-down nav-submenu-chevron"></i>
                </a>
                <ul class="submenu-header">
                    <li><a href="#">تاريخ النادي</a></li>
                    <li><a href="#">الإدارة</a></li>
                    <li class="has-submenu-header">
                        <!-- Example of a nested submenu -->
                        <a href="#" aria-haspopup="true" aria-expanded="false">
                            الفرق <i class="fas fa-chevron-right nav-submenu-chevron nested-chevron"></i>
                        </a>
                        <ul class="submenu-header nested-submenu">
                            <li><a href="#">الفريق الأول</a></li>
                            <li><a href="#">الشباب</a></li>
                            <li><a href="#">الناشئين</a></li>
                        </ul>
                    </li>
                </ul>
            </li> --}}


                @foreach ($types ?? [] as $type)
            <li class="has-submenu-header">
                <div aria-haspopup="true" aria-expanded="false" class="link">
                    <?= $type['name'] ?>
                    <i class="fas fa-chevron-down nav-submenu-chevron"></i>
                </div>
                <ul class="submenu-header">
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
                <a href="{{ route('contact-us') }}">
                </a>
            </li>
        </ul>
    </nav>

    <div class="header-group header-group-end">
        <div class="header-logo-container-style-1">
            <a href="{{ route('home') }}" class="header-logo-link">
                <img src="{{ $web_config['company_logo'] }}" alt="{{ $web_config['company_name'] }}"
                    class="app-logo-img">
            </a>
        </div>
    </div>

    <button class="hamburger-btn-style-1" id="mainHamburgerBtn" aria-label="Toggle Menu" aria-expanded="false">
        <i class="fas fa-bars hamburger-icon-open"></i>
        <i class="fas fa-times hamburger-icon-close"></i>
    </button>
</header>
