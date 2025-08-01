
// Toggle sidebar on mobile
document.getElementById('hamburgerBtn')?.addEventListener('click', function () {
    document.querySelector('html').classList.toggle('drawer-open');
});

// Toggle organization dropdown
const orgInfo = document.getElementById('orgInfoContainer');
const orgDropdown = document.getElementById('orgDropdown');
const orgChevron = document.getElementById('orgChevron');

if (orgInfo) {
    orgInfo.addEventListener('click', function () {
        orgDropdown.classList.toggle('hidden');
        orgChevron.classList.toggle('rotate-180');
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    if (orgInfo && !orgInfo.contains(event.target)) {
        orgDropdown.classList.add('hidden');
        orgChevron.classList.remove('rotate-180');
    }
});

// Toggle user profile dropdown in sidebar
const userProfile = document.getElementById('sidebarUserProfile');
const userProfileDropdown = document.getElementById('userProfileDropdown');
const userProfileChevron = document.getElementById('userProfileChevron');

if (userProfile) {
    userProfile.addEventListener('click', function () {
        userProfileDropdown.classList.toggle('open');
        userProfileChevron.classList.toggle('rotate-180');
    });
}

// Toggle submenus
document.querySelectorAll('.has-submenu > a').forEach(item => {
    item.addEventListener('click', function (e) {
        if (this.getAttribute('href') === '#') {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('submenu-open');

            // Rotate chevron
            const chevron = this.querySelector('.nav-chevron');
            if (chevron) {
                chevron.classList.toggle('rotate-90');
            }
        }
    });
});

// Responsive sidebar behavior
function handleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const html = document.querySelector('html');

    if (window.innerWidth < 768) {
        html.classList.remove('sidebar-closed');
        sidebar.classList.add('transform', '-translate-x-full');
        if (html.getAttribute('dir') === 'rtl') {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-full');
        }
    } else {
        sidebar.classList.remove('transform', '-translate-x-full', 'translate-x-full');
    }
}

// Initial setup
handleSidebar();

// Handle window resize
window.addEventListener('resize', handleSidebar);