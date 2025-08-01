export function initToTop() {
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    if (scrollToTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 200) {
                scrollToTopBtn.style.display = 'block';
                setTimeout(() => scrollToTopBtn.classList.add('show'), 10);
            } else {
                scrollToTopBtn.classList.remove('show');
                setTimeout(() => {
                    if (window.pageYOffset <= 200) scrollToTopBtn.style.display = 'none';
                }, 300);
            }
        });
        scrollToTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}