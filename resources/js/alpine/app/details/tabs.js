// js/ui/tabs.js
export function initTabs() {
    // New structure elements
    const tabsWrapper = document.getElementById('tabsWrapperV3');
    const tabButtonsContainer = document.getElementById('tabButtonsContainerV3');
    const scrollLeftBtn = document.getElementById('scrollTabsLeftBtn');
    const scrollRightBtn = document.getElementById('scrollTabsRightBtn');

    // Original elements
    const tabButtonsV3 = document.querySelectorAll('.tab-button-v3');
    const tabContentsV3 = document.querySelectorAll('.tab-content-v3');

    if (!tabsWrapper || !tabButtonsContainer) {
        return; // Exit if the main components are not found
    }

    // Tab switching logic (unchanged)
    if (tabButtonsV3.length > 0 && tabContentsV3.length > 0) {
        tabButtonsV3.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.dataset.tab;

                tabButtonsV3.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                tabContentsV3.forEach(content => {
                    content.classList.remove('active');
                    if (content.id === targetTab + "ContentV3") {
                        content.classList.add('active');
                    }
                });
            });
        });
    }

    // --- Scroll Logic for Buttons ---
    if (scrollLeftBtn && scrollRightBtn) {
        scrollLeftBtn.addEventListener('click', () => {
            // Scroll left by 75% of the container's width
            const scrollAmount = tabButtonsContainer.clientWidth * 0.75;
            tabButtonsContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        scrollRightBtn.addEventListener('click', () => {
            // Scroll right by 75% of the container's width
            const scrollAmount = tabButtonsContainer.clientWidth * 0.75;
            tabButtonsContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }

    function updateScrollHints() {
        if (!tabsWrapper) return;

        const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
        const scrollAmount = isRTL ? -tabButtonsContainer.scrollLeft : tabButtonsContainer.scrollLeft;
        const maxScroll = tabButtonsContainer.scrollWidth - tabButtonsContainer.clientWidth;
        const threshold = 10;

        // Show/hide the "start" (left) hint/button
        if (scrollAmount > threshold) {
            tabsWrapper.classList.add('show-scroll-hint-start');
        } else {
            tabsWrapper.classList.remove('show-scroll-hint-start');
        }

        // Show/hide the "end" (right) hint/button
        if (scrollAmount < (maxScroll - threshold)) {
            tabsWrapper.classList.add('show-scroll-hint-end');
        } else {
            tabsWrapper.classList.remove('show-scroll-hint-end');
        }
    }

    function checkTabScroll() {
        requestAnimationFrame(() => {
            const hasOverflow = tabButtonsContainer.scrollWidth > tabButtonsContainer.clientWidth + 1;

            if (hasOverflow) {
                tabsWrapper.classList.add('is-scrollable');
                updateScrollHints(); // Initial check
            } else {
                tabsWrapper.classList.remove('is-scrollable', 'show-scroll-hint-start', 'show-scroll-hint-end');
            }
        });
    }

    // Initial check and event listeners
    setTimeout(checkTabScroll, 150); // Delay to ensure layout is stable
    tabButtonsContainer.addEventListener('scroll', updateScrollHints, { passive: true });
    window.addEventListener('resize', checkTabScroll);
}