document.addEventListener('DOMContentLoaded', function () {

    const htmlElement = document.documentElement; // Target <html> for global states
    const body = document.body; // Still useful for some specific body-only classes

    const sidebar = document.getElementById('sidebar');
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const drawerBackdrop = document.getElementById('drawerBackdrop');

    const sidebarUserProfile = document.getElementById('sidebarUserProfile');
    const userProfileDropdown = document.getElementById('userProfileDropdown');
    const userProfileChevron = document.getElementById('userProfileChevron');

    const orgInfoContainer = document.getElementById('orgInfoContainer');
    const orgDropdown = document.getElementById('orgDropdown');
    const orgChevron = document.getElementById('orgChevron');

    const mainNav = document.querySelector('.main-nav');

    const SMALL_SCREEN_BREAKPOINT = 767;

    window.setAppDirection = function (dir) { // Make it global for pre-render script access if needed, or call from pre-render
        htmlElement.classList.remove('ltr', 'rtl');
        htmlElement.classList.add(dir);
        htmlElement.setAttribute('dir', dir);

        // Also update body class if some CSS still relies on it, though html target is preferred
        body.classList.remove('ltr', 'rtl');
        body.classList.add(dir);

        localStorage.setItem('uiDirection', dir);
        // // console.log(`Direction set to: ${dir} via JS`);

        // Trigger updates for components that might depend on direction
        if (typeof window.updateScrollableTabIndicators === "function") {
            window.updateScrollableTabIndicators();
        }
        if (typeof window.manageSidebarStateOnResize === "function") {
            window.manageSidebarStateOnResize(); // Re-evaluate layout
        }
    }

    // --- Sidebar Toggle (Hamburger) & Drawer Backdrop ---
    if (hamburgerBtn && sidebar) {
        hamburgerBtn.addEventListener('click', () => {
            if (window.innerWidth <= SMALL_SCREEN_BREAKPOINT) {
                htmlElement.classList.toggle('drawer-open'); // Use <html> for drawer state
                body.classList.toggle('drawer-scroll-lock', htmlElement.classList.contains('drawer-open'));
                htmlElement.classList.remove('sidebar-open', 'sidebar-closed');
            } else {
                const isCurrentlyClosed = htmlElement.classList.contains('sidebar-closed');
                if (isCurrentlyClosed) {
                    htmlElement.classList.remove('sidebar-closed');
                    htmlElement.classList.add('sidebar-open');
                } else {
                    htmlElement.classList.add('sidebar-closed');
                    htmlElement.classList.remove('sidebar-open');
                }
                if (htmlElement.classList.contains('sidebar-closed') && mainNav) {
                    mainNav.querySelectorAll('.has-submenu.submenu-open').forEach(openLi => {
                        openLi.classList.remove('submenu-open');
                        const anchor = openLi.querySelector('a');
                        if (anchor) anchor.setAttribute('aria-expanded', 'false');
                    });
                }
            }
        });
    }
    if (drawerBackdrop) {
        drawerBackdrop.addEventListener('click', () => {
            if (htmlElement.classList.contains('drawer-open')) {
                htmlElement.classList.remove('drawer-open');
                body.classList.remove('drawer-scroll-lock');
            }
        });
    }

    // Initialize and Handle Resize for Sidebar/Drawer state
    window.manageSidebarStateOnResize = function () {
        if (window.innerWidth <= SMALL_SCREEN_BREAKPOINT) {
            htmlElement.classList.remove('sidebar-open', 'sidebar-closed');
        } else {
            if (htmlElement.classList.contains('drawer-open')) {
                htmlElement.classList.remove('drawer-open');
                body.classList.remove('drawer-scroll-lock');
            }
            if (!htmlElement.classList.contains('sidebar-closed') && !htmlElement.classList.contains('sidebar-open')) {
                htmlElement.classList.add('sidebar-open');
            }
        }
        if (typeof window.updateScrollableTabIndicators === "function") window.updateScrollableTabIndicators();
    }
    window.addEventListener('resize', window.manageSidebarStateOnResize);
    window.manageSidebarStateOnResize(); // Initial call


    // --- Sidebar User Profile Dropdown ---
    if (sidebarUserProfile && userProfileDropdown && userProfileChevron) {
        sidebarUserProfile.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = userProfileDropdown.classList.toggle('open');
            userProfileChevron.classList.toggle('rotate', isOpen);
            sidebarUserProfile.setAttribute('aria-expanded', isOpen);
            if (isOpen && orgDropdown && orgDropdown.classList.contains('open')) {
                orgDropdown.classList.remove('open');
                if (orgChevron) orgChevron.classList.remove('rotate');
                if (orgInfoContainer) orgInfoContainer.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // --- Organization Info Dropdown (Header) ---
    if (orgInfoContainer && orgDropdown && orgChevron) {
        orgInfoContainer.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = orgDropdown.classList.toggle('open');
            orgChevron.classList.toggle('rotate', isOpen);
            orgInfoContainer.setAttribute('aria-expanded', isOpen);
            if (isOpen && userProfileDropdown && userProfileDropdown.classList.contains('open')) {
                userProfileDropdown.classList.remove('open');
                if (userProfileChevron) userProfileChevron.classList.remove('rotate');
                if (sidebarUserProfile) sidebarUserProfile.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Close dropdowns if clicking outside
    document.addEventListener('click', (event) => {
        if (userProfileDropdown && userProfileDropdown.classList.contains('open') &&
            sidebarUserProfile && !sidebarUserProfile.contains(event.target) && !userProfileDropdown.contains(event.target)) {
            userProfileDropdown.classList.remove('open');
            if (userProfileChevron) userProfileChevron.classList.remove('rotate');
            if (sidebarUserProfile) sidebarUserProfile.setAttribute('aria-expanded', 'false');
        }
        if (orgDropdown && orgDropdown.classList.contains('open') &&
            orgInfoContainer && !orgInfoContainer.contains(event.target) && !orgDropdown.contains(event.target)) {
            orgDropdown.classList.remove('open');
            if (orgChevron) orgChevron.classList.remove('rotate');
            if (orgInfoContainer) orgInfoContainer.setAttribute('aria-expanded', 'false');
        }
    });

    // --- Two-Level Sidebar Navigation ---
    if (mainNav) {
        const submenuLinks = mainNav.querySelectorAll('.has-submenu > a');
        submenuLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                if (this.getAttribute('href') === '#') {
                    event.preventDefault();
                }
                const parentLi = this.parentElement;
                const wasOpen = parentLi.classList.contains('submenu-open');

                if (!wasOpen) {
                    parentLi.closest('.main-nav').querySelectorAll('.has-submenu.submenu-open').forEach(openLi => {
                        if (openLi !== parentLi) {
                            openLi.classList.remove('submenu-open');
                            const anchor = openLi.querySelector('a');
                            if (anchor) anchor.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
                parentLi.classList.toggle('submenu-open');
                this.setAttribute('aria-expanded', parentLi.classList.contains('submenu-open'));

                if (window.innerWidth <= SMALL_SCREEN_BREAKPOINT && body.classList.contains('drawer-open')) {
                    const isParentLink = parentLi.classList.contains('has-submenu');
                    const isNowOpeningSubmenu = parentLi.classList.contains('submenu-open');
                    if (!isParentLink) { // Clicked on a direct link
                        body.classList.remove('drawer-open');
                        body.classList.remove('drawer-scroll-lock');
                    }
                    // If isParentLink and isNowOpeningSubmenu, do NOT close drawer.
                    // If isParentLink and !isNowOpeningSubmenu (closing submenu), also do NOT close drawer.
                }
            });
        });
    }
    // Close inline-opened submenus when a desktop hover-expanded sidebar shrinks
    if (sidebar && mainNav) {
        sidebar.addEventListener('mouseleave', () => {
            if (window.innerWidth > SMALL_SCREEN_BREAKPOINT && body.classList.contains('sidebar-closed')) {
                mainNav.querySelectorAll('.has-submenu.submenu-open').forEach(openLi => {
                    openLi.classList.remove('submenu-open');
                    const anchor = openLi.querySelector('a');
                    if (anchor) anchor.setAttribute('aria-expanded', 'false');
                });
            }
        });
    }

    // --- Function to Open Parent of Active Submenu Item ---
    function openActiveSubmenuParent() {
        // // console.log("openActiveSubmenuParent");

        if (mainNav) {
            const activeSubmenuLink = mainNav.querySelector('.submenu a.active-link');
            if (activeSubmenuLink) {
                const submenuUl = activeSubmenuLink.closest('ul.submenu');
                if (submenuUl) {
                    const parentLiHasSubmenu = submenuUl.closest('li.has-submenu');
                    if (parentLiHasSubmenu) {
                        parentLiHasSubmenu.classList.add('submenu-open');
                        parentLiHasSubmenu.classList.add('parent-of-active'); // For styling parent
                        const parentAnchor = parentLiHasSubmenu.querySelector('a');
                        if (parentAnchor) {
                            parentAnchor.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            }
        }
    }
    manageSidebarStateOnResize(); // Call this early
    openActiveSubmenuParent(); // Call on initial load
});