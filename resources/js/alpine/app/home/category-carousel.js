document.addEventListener('DOMContentLoaded', function () {
    // Ensures Swiper is defined (loaded from CDN)
    if (typeof Swiper === 'undefined') {
        console.error('SwiperJS not loaded!');
        return;
    }

    // Category Carousel Initialization
    const categorySwiper = new Swiper('.category-swiper', {
        direction: 'horizontal',
        loop: true, // Enable loop for autoplay
        autoplay: {
            delay: 4000, // Time in ms
            disableOnInteraction: false, // Autoplay will not be disabled after user interactions
            pauseOnMouseEnter: true,     // Pause autoplay when mouse is over swiper
        },
        speed: 1000, // Transition speed in ms for autoplay and manual interaction

        // Navigation arrows
        navigation: {
            nextEl: '.category-swiper-button-next',
            prevEl: '.category-swiper-button-prev',
        },

        // Pagination
        pagination: {
            el: '.category-swiper-pagination',
            clickable: true,
        },

        // Responsive breakpoints
        slidesPerView: 2.5, // Default for smallest
        spaceBetween: 10, // Default space
        breakpoints: {
            0: {
                slidesPerView: 2.5,
                spaceBetween: 10
            },
            300: {
                slidesPerView: 3.2,
                spaceBetween: 10
            },
            480: {
                slidesPerView: 4.5,
                spaceBetween: 10
            },
            768: {
                slidesPerView: 5.5,
                spaceBetween: 15
            },
            992: {
                slidesPerView: 6.5,
                spaceBetween: 15
            },
            1200: {
                slidesPerView: 8.5,
                spaceBetween: 15
            }
        }
    });

});