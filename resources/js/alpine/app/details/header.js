// js/ui/header.js
export function initHeader() {
    const mobileMenuToggleV3 = document.querySelector('.mobile-menu-toggle-v3');
    const siteHeaderV3 = document.querySelector('.site-header-v3');

    if (mobileMenuToggleV3 && siteHeaderV3) {
        mobileMenuToggleV3.addEventListener('click', () => {
            siteHeaderV3.classList.toggle('mobile-menu-open-v3');
        });
    }
}