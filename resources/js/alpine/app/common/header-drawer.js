export function initHeader() {
    const htmlElement = document.documentElement;
    const body = document.body;

    // --- Configuration ---
    const MOBILE_DROPDOWN_FIX_BREAKPOINT = 767;
    const NOTIFICATION_DROPDOWN_ID = 'notificationsDropdownContentHeader';
    const MAIN_HEADER_ID = 'mainAppHeader';
    const SMALL_SCREEN_BREAKPOINT_NAV = 992;

    // --- LTR/RTL Toggle Function ---
    window.setAppDirection = function (dir) {
        const currentDir = htmlElement.getAttribute('dir');
        if (currentDir === dir) return;
        htmlElement.classList.remove('ltr', 'rtl');
        htmlElement.classList.add(dir);
        htmlElement.setAttribute('dir', dir);
        body.classList.remove('ltr', 'rtl');
        body.classList.add(dir);
        localStorage.setItem('uiDirection', dir);
        // // console.log(`Direction set to: ${dir}`);
        const event = new CustomEvent('languageDirectionChanged', { detail: { direction: dir } });
        window.dispatchEvent(event);
        if (typeof window.manageLayoutOnResize === "function") {
            window.manageLayoutOnResize();
        }
    }
    const toggleDirButton = document.getElementById('toggleDirectionBtnExample');
    if (toggleDirButton) {
        toggleDirButton.addEventListener('click', () => {
            const newDir = htmlElement.classList.contains('rtl') ? 'ltr' : 'rtl';
            window.setAppDirection(newDir);
        });
    }
    const initialHtmlDir = htmlElement.getAttribute('dir') || 'ltr';
    if (!body.classList.contains(initialHtmlDir)) {
        body.classList.remove('ltr', 'rtl'); body.classList.add(initialHtmlDir);
    }

    // --- Mobile Drawer Toggle ---
    const mainHamburgerBtn = document.getElementById('mainHamburgerBtn');
    const mainMobileDrawerNav = document.getElementById('mainMobileDrawerNav');
    const mainDrawerBackdrop = document.getElementById('mainDrawerBackdrop');
    const closeMainMobileDrawerBtn = document.getElementById('closeMainMobileDrawerBtn');

    function toggleMainMobileDrawer(show) {
        const action = show ? 'add' : 'remove';
        htmlElement.classList[action]('drawer-open');
        body.classList[action]('drawer-scroll-lock');
        if (mainHamburgerBtn) mainHamburgerBtn.setAttribute('aria-expanded', show ? 'true' : 'false');
        if (mainMobileDrawerNav) mainMobileDrawerNav.setAttribute('aria-hidden', show ? 'false' : 'true');
    }

    if (mainHamburgerBtn) {
        mainHamburgerBtn.addEventListener('click', () => {
            // console.log("[Drawer Debug] Hamburger clicked.");
            toggleMainMobileDrawer(!htmlElement.classList.contains('drawer-open'));
        });
    }
    if (mainDrawerBackdrop) {
        mainDrawerBackdrop.addEventListener('click', () => {
            // console.log("[Drawer Debug] Backdrop clicked.");
            toggleMainMobileDrawer(false);
        });
    }
    if (closeMainMobileDrawerBtn) {
        closeMainMobileDrawerBtn.addEventListener('click', () => {
            // console.log("[Drawer Debug] Close button clicked.");
            toggleMainMobileDrawer(false);
        });
    }

    // --- START: DRAWER SUBMENU TOGGLE LOGIC (for static menu) ---
    const drawerNavContainer = document.getElementById('mainNavDrawerContent');
    if (drawerNavContainer) {
        // console.log("[Drawer Debug] Drawer nav container (#mainNavDrawerContent) found.");

        const drawerSubmenuTriggers = drawerNavContainer.querySelectorAll('.drawer-main-menu li.has-submenu-header > a');
        // console.log(`[Drawer Debug] Found ${drawerSubmenuTriggers.length} submenu triggers in drawer:`, drawerSubmenuTriggers);

        const drawerNavDivider = document.getElementById('drawerNavDivider');
        const mainDrawerMenuUl = drawerNavContainer.querySelector('.drawer-main-menu');
        const staticDrawerLinksUl = drawerNavContainer.querySelector('.drawer-static-links');

        if (drawerNavDivider &&
            mainDrawerMenuUl && mainDrawerMenuUl.children.length > 0 &&
            staticDrawerLinksUl && staticDrawerLinksUl.children.length > 0) {
            drawerNavDivider.style.display = 'list-item';
            // console.log("[Drawer Debug] Displaying drawer nav divider.");
        } else if (drawerNavDivider) {
            drawerNavDivider.style.display = 'none';
            // console.log("[Drawer Debug] Hiding drawer nav divider.");
        }

        drawerSubmenuTriggers.forEach(trigger => {
            const parentLiForThisTrigger = trigger.parentElement;
            // VVVVVVVVVVVVVVVV SELECTOR CHANGED HERE VVVVVVVVVVVVVVVV
            const submenuForThisTrigger = parentLiForThisTrigger.querySelector(':scope > ul.drawer-submenu-list');
            // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

            // console.log(`[Drawer Debug] Setting up trigger for: "${trigger.textContent.trim()}". Has submenu: ${!!submenuForThisTrigger}`);

            if (submenuForThisTrigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }

            trigger.addEventListener('click', function (event) {
                // console.log(`[Drawer Debug] Clicked on: "${this.textContent.trim()}"`);

                const currentParentLi = this.parentElement;
                // VVVVVVVVVVVVVVVV SELECTOR CHANGED HERE VVVVVVVVVVVVVVVV
                const currentSubmenu = currentParentLi.querySelector(':scope > ul.drawer-submenu-list');
                // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

                // console.log("[Drawer Debug]   - Parent LI:", currentParentLi);
                // console.log("[Drawer Debug]   - Submenu element:", currentSubmenu);

                if (currentSubmenu) {
                    event.preventDefault();
                    // console.log("[Drawer Debug]   - Preventing default navigation and toggling submenu.");

                    const isOpen = currentParentLi.classList.toggle('drawer-submenu-open');
                    this.setAttribute('aria-expanded', isOpen.toString());

                    // console.log(`[Drawer Debug]   - Parent LI class 'drawer-submenu-open' is now: ${isOpen}`);
                    // console.log(`[Drawer Debug]   - Trigger aria-expanded is now: ${this.getAttribute('aria-expanded')}`);

                    const chevron = this.querySelector('.nav-submenu-chevron');
                    if (chevron) {
                        chevron.classList.toggle('active', isOpen);
                        // console.log(`[Drawer Debug]   - Chevron 'active' class is now: ${isOpen}`);
                    }
                } else {
                    // console.log("[Drawer Debug]   - No submenu found for this item, allowing default navigation.");
                }
            });
        });
    } else {
        console.error("[Drawer Debug] Drawer nav container (#mainNavDrawerContent) NOT found!");
    }
    // --- END: DRAWER SUBMENU TOGGLE LOGIC ---

    // --- Helper functions for Notification Dropdown Positioning ---
    // ... (this section remains unchanged) ...
    function positionMobileNotificationDropdown(dropdownEl) {
        const mainHeader = document.getElementById(MAIN_HEADER_ID);
        if (!mainHeader || !dropdownEl) return;
        const headerRect = mainHeader.getBoundingClientRect();
        const dropdownGutter = 10;
        dropdownEl.style.position = 'fixed';
        dropdownEl.style.top = `${headerRect.bottom + 8}px`;
        dropdownEl.style.width = `calc(100vw - ${dropdownGutter * 2}px)`;
        dropdownEl.style.maxWidth = '320px';
        if (htmlElement.dir === 'ltr') {
            dropdownEl.style.left = `${dropdownGutter}px`;
            dropdownEl.style.right = 'auto';
        } else {
            dropdownEl.style.right = `${dropdownGutter}px`;
            dropdownEl.style.left = 'auto';
        }
        dropdownEl.dataset.jsPositioned = 'true';
    }

    function resetMobileNotificationDropdownStyle(dropdownEl) {
        if (dropdownEl && dropdownEl.dataset.jsPositioned === 'true') {
            dropdownEl.style.position = '';
            dropdownEl.style.top = '';
            dropdownEl.style.left = '';
            dropdownEl.style.right = '';
            dropdownEl.style.width = '';
            dropdownEl.style.maxWidth = '';
            delete dropdownEl.dataset.jsPositioned;
        }
    }

    // --- Header Dropdown Toggles ---
    // ... (this section remains unchanged, it's for header dropdowns not drawer) ...
    const userProfileMinimalHeader = document.getElementById('userProfileMinimalHeader');
    const userAvatarDropdownHeader = document.getElementById('userAvatarDropdownHeader');
    const toggleLangBtnHeader = document.getElementById('toggleLangBtnHeader');
    const langDropdownHeader = document.getElementById('langDropdownHeader');
    const notificationLinkHeader = document.getElementById('notificationLinkHeader');
    const notificationsDropdownContentHeader = document.getElementById('notificationsDropdownContentHeader');

    const allHeaderDropdowns = [userAvatarDropdownHeader, langDropdownHeader, notificationsDropdownContentHeader].filter(Boolean);
    const allHeaderDropdownTriggers = [userProfileMinimalHeader, toggleLangBtnHeader, notificationLinkHeader].filter(Boolean);

    function closeAllHeaderDropdowns(exceptThisDropdown = null) {
        allHeaderDropdowns.forEach(dd => {
            if (dd !== exceptThisDropdown && dd.classList.contains('open')) {
                dd.classList.remove('open');
                const trigger = allHeaderDropdownTriggers.find(t => t && (t.getAttribute('aria-controls') === dd.id || (dd.id && dd.id.includes(t.id.replace(/LinkHeader|BtnHeader|ProfileMinimalHeader/, '')))));
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                    const chevron = trigger.querySelector('.lang-chevron');
                    if (chevron) chevron.classList.remove('dropdown-active');
                    if (dd.id === NOTIFICATION_DROPDOWN_ID && dd.dataset.jsPositioned === 'true') {
                        resetMobileNotificationDropdownStyle(dd);
                    }
                }
            }
        });
    }

    function createDropdownToggler(triggerEl, dropdownEl) {
        // console.log("createDropdownToggler");
        // console.log("createDropdownToggler",triggerEl,dropdownEl);

        if (!triggerEl || !dropdownEl) return;
        if (!dropdownEl.id) dropdownEl.id = `header-dd-${Math.random().toString(36).substring(2, 9)}`;
        triggerEl.setAttribute('aria-controls', dropdownEl.id);
        triggerEl.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpening = !dropdownEl.classList.contains('open');
            if (isOpening) {
                closeAllHeaderDropdowns(dropdownEl);
            }
            dropdownEl.classList.toggle('open', isOpening);
            triggerEl.setAttribute('aria-expanded', String(isOpening));
            const chevron = triggerEl.querySelector('.lang-chevron');
            if (chevron) chevron.classList.toggle('dropdown-active', isOpening);
            if (dropdownEl.id === NOTIFICATION_DROPDOWN_ID) {
                if (isOpening && window.innerWidth <= MOBILE_DROPDOWN_FIX_BREAKPOINT) {
                    setTimeout(() => {
                        if (dropdownEl.classList.contains('open')) {
                            positionMobileNotificationDropdown(dropdownEl);
                        }
                    }, 10);
                } else if (!isOpening && dropdownEl.dataset.jsPositioned === 'true') {
                    resetMobileNotificationDropdownStyle(dropdownEl);
                }
            }
        });
    }

    createDropdownToggler(userProfileMinimalHeader, userAvatarDropdownHeader);
    createDropdownToggler(toggleLangBtnHeader, langDropdownHeader);
    createDropdownToggler(notificationLinkHeader, notificationsDropdownContentHeader);

    document.addEventListener('click', (event) => {
        let clickedInsideADropdownSystem = false;
        allHeaderDropdowns.forEach(dd => { if (dd && dd.contains(event.target)) clickedInsideADropdownSystem = true; });
        allHeaderDropdownTriggers.forEach(tr => { if (tr && tr.contains(event.target)) clickedInsideADropdownSystem = true; });
        if (!clickedInsideADropdownSystem) {
            closeAllHeaderDropdowns();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === "Escape") {
            const openDropdown = document.querySelector('.header-dropdown.open');
            if (openDropdown) {
                const trigger = allHeaderDropdownTriggers.find(t => t && t.getAttribute('aria-controls') === openDropdown.id);
                closeAllHeaderDropdowns();
                if (trigger) trigger.focus();
            } else if (htmlElement.classList.contains('drawer-open')) {
                toggleMainMobileDrawer(false);
                if (mainHamburgerBtn) mainHamburgerBtn.focus();
            }
        }
    });

    // Language Switcher Selection Logic
    // ... (this section remains unchanged) ...
    // if (langDropdownHeader && toggleLangBtnHeader) {
    //     let langBtnTextElement = toggleLangBtnHeader.childNodes[0];
    //     function setActiveLangButtonText() {
    //         const activeLangOption = langDropdownHeader.querySelector('a.active');
    //         if (activeLangOption && langBtnTextElement) {
    //             langBtnTextElement.nodeValue = activeLangOption.dataset.lang.toUpperCase() + " ";
    //         } else if (langBtnTextElement) {
    //             const langFromHtml = htmlElement.getAttribute('dir') === 'rtl' ? 'AR' : 'EN';
    //             langBtnTextElement.nodeValue = langFromHtml + " ";
    //             const correspondingOption = langDropdownHeader.querySelector(`a[data-lang="${langFromHtml.toLowerCase()}"]`);
    //             if (correspondingOption) {
    //                 langDropdownHeader.querySelectorAll('a').forEach(a => a.classList.remove('active'));
    //                 correspondingOption.classList.add('active');
    //             }
    //         }
    //     }
    //     setActiveLangButtonText();
    //     langDropdownHeader.addEventListener('click', (e) => {
    //         const langOptionLink = e.target.closest('a[data-lang]');
    //         if (langOptionLink) {
    //             e.preventDefault();
    //             const selectedLangCode = langOptionLink.dataset.lang;
    //             const currentActive = langDropdownHeader.querySelector('a.active');
    //             if (currentActive) currentActive.classList.remove('active');
    //             langOptionLink.classList.add('active');
    //             window.setAppDirection(selectedLangCode === 'ar' ? 'rtl' : 'ltr');
    //             closeAllHeaderDropdowns();
    //         }
    //     });
    //     window.addEventListener('languageDirectionChanged', setActiveLangButtonText);
    // }

    // --- Resize Handler for Header/Drawer ---
    // ... (this section remains unchanged) ...
    window.manageLayoutOnResize = function () {
        if (window.innerWidth > SMALL_SCREEN_BREAKPOINT_NAV) {
            if (htmlElement.classList.contains('drawer-open')) {
                toggleMainMobileDrawer(false);
            }
        }
        const notifDropdown = document.getElementById(NOTIFICATION_DROPDOWN_ID);
        if (notifDropdown && notifDropdown.dataset.jsPositioned === 'true') {
            if (window.innerWidth > MOBILE_DROPDOWN_FIX_BREAKPOINT) {
                resetMobileNotificationDropdownStyle(notifDropdown);
            } else {
                if (notifDropdown.classList.contains('open')) {
                    positionMobileNotificationDropdown(notifDropdown);
                }
            }
        }
        if (typeof window.updateScrollableTabIndicators === "function") {
            window.updateScrollableTabIndicators();
        }
    }
    window.addEventListener('resize', window.manageLayoutOnResize);
    window.manageLayoutOnResize();

    // --- Active Link for Main Desktop Nav ---
    // ... (this section remains unchanged) ...
    const desktopNavLinks = document.querySelectorAll('#desktopNavStyle1 ul li a');
    if (desktopNavLinks.length > 0) {
        const currentPath = window.location.pathname.split('/').pop() || 'dashboard.html';
        const homePageNames = ['dashboard.html', 'index.html', ''];
        desktopNavLinks.forEach(link => {
            link.classList.remove('active-header-link');
            if (link.closest('li.has-submenu-header')) {
                const parentAnchor = link.closest('li.has-submenu-header').querySelector(':scope > a');
                if (parentAnchor) parentAnchor.classList.remove('active-header-link');
            }
        });
        let activeLinkFound = false;
        desktopNavLinks.forEach(link => {
            if (activeLinkFound) return;
            const linkHref = link.getAttribute('href');
            if (!linkHref) return;
            const linkPath = linkHref.split('/').pop();
            if (linkPath === currentPath || (homePageNames.includes(currentPath) && homePageNames.includes(linkPath) && linkPath !== '')) {
                link.classList.add('active-header-link');
                activeLinkFound = true;
                let parentLi = link.closest('li.has-submenu-header');
                while (parentLi) {
                    const parentAnchor = parentLi.querySelector(':scope > a');
                    if (parentAnchor) parentAnchor.classList.add('active-header-link');
                    parentLi = parentLi.parentElement.closest('li.has-submenu-header');
                }
            }
        });
        if (!activeLinkFound && homePageNames.includes(currentPath)) {
            const homeLink = document.querySelector('#desktopNavStyle1 ul li a[href="dashboard.html"]');
            if (homeLink) {
                homeLink.classList.add('active-header-link');
            }
        }
    }

    // --- Active Link for Mobile Drawer Nav ---
    // ... (this section remains unchanged, but ensure selectors are correct for your drawer structure) ...
    const drawerNavLinks = document.querySelectorAll('#mainNavDrawerContent .drawer-main-menu li a');
    if (drawerNavLinks.length > 0) {
        const currentPath = window.location.pathname.split('/').pop() || 'dashboard.html';
        const homePageNames = ['dashboard.html', 'index.html', ''];

        drawerNavLinks.forEach(link => {
            link.classList.remove('active-header-link');
            if (link.closest('li.has-submenu-header')) {
                const parentLi = link.closest('li.has-submenu-header');
                const parentAnchor = parentLi.querySelector(':scope > a');
                if (parentAnchor) parentAnchor.classList.remove('active-header-link');
            }
        });

        let activeDrawerLinkFound = false;
        drawerNavLinks.forEach(link => {
            if (activeDrawerLinkFound) return;
            const linkHref = link.getAttribute('href');
            if (!linkHref) return;
            const linkPath = linkHref.split('/').pop();

            if (linkPath === currentPath || (homePageNames.includes(currentPath) && homePageNames.includes(linkPath) && linkPath !== '')) {
                link.classList.add('active-header-link');
                activeDrawerLinkFound = true;

                let parentLi = link.closest('li.has-submenu-header');
                while (parentLi) {
                    const parentAnchor = parentLi.querySelector(':scope > a');
                    if (parentAnchor) parentAnchor.classList.add('active-header-link');
                    parentLi = parentLi.parentElement.closest('li.has-submenu-header');
                }
            }
        });
        if (!activeDrawerLinkFound && homePageNames.includes(currentPath)) {
            const homeDrawerLink = document.querySelector('#mainNavDrawerContent .drawer-main-menu li a[href="dashboard.html"]');
            if (homeDrawerLink) {
                homeDrawerLink.classList.add('active-header-link');
            }
        }
    }

    // console.log("Header & Drawer JS initialized (with drawer-submenu-list and debug logs).");

}