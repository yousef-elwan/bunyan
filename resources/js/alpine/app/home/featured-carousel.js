document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper === 'undefined') {
        console.error('SwiperJS not loaded!');
        return;
    }

    // Featured Properties Carousel Initialization
    const propertiesSwiper = new Swiper('.properties-swiper', {
        direction: 'horizontal',
        loop: true,
        autoplay: {
            delay: 5500, // Slightly longer delay for content-heavy slides
            disableOnInteraction: false, // Keep autoplaying after user interaction
            pauseOnMouseEnter: true,     // Pause when mouse is over
        },
        speed: 800, // Transition speed in ms

        // Navigation arrows
        navigation: {
            nextEl: '.properties-swiper-button-next',
            prevEl: '.properties-swiper-button-prev',
        },

        // Pagination
        pagination: {
            el: '.properties-swiper-pagination',
            clickable: true,
        },

        // Responsive breakpoints
        slidesPerView: 1.2, // Default for smallest screens
        spaceBetween: 20, // Space between slides
        breakpoints: {
            0: { // Mobile screens
                slidesPerView: 1.2,
                nav: false
            },
            300: {
                slidesPerView: 1.4,
                spaceBetween: 10
            },
            600: { // Matches old Owl breakpoint
                slidesPerView: 2.25,
                spaceBetween: 20
            },
            992: { // Adjusted from 1000 for common bootstrap breakpoint
                slidesPerView: 3.5,
                spaceBetween: 20
            },
            1200: {
                slidesPerView: 4.5, // Or 4 if your cards are narrow enough and container wider
                spaceBetween: 20
            },
            // You might need a wider breakpoint for 4 items if you increased container width
            1400: {
                slidesPerView: 4.5,
                spaceBetween: 20
            }
        }
    });
});