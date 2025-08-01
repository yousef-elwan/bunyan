import { translate } from "../../utils/helpers";

// js/ui/gallery.js
export function initGallery() {
    const heroSection = document.querySelector('.hero-swiper-section-v3');
    const heroSwiperContainer = document.querySelector('.hero-swiper');
    const heroSwiperWrapper = document.getElementById('heroSwiperWrapperV3');
    const thumbnailContainer = document.getElementById('thumbnailContainerV3');
    const swiperProgressIndicator = document.getElementById('swiperProgressV3');
    let heroSwiperInstance = null;

    const SLIDER_BREAKPOINT_MAX = 991; // Screens <= this width use slider layout
    const MEDIUM_GRID_BREAKPOINT_MAX = 1399; // Screens > this width use large grid
    const MAX_SMALL_IMAGES_MEDIUM_GRID = 2;
    const MAX_SMALL_IMAGES_LARGE_GRID = 4;

    const images = (AppConfig.pageData.images || []).map(function (item) {
        return {
            src: item,
            thumb: item,
            caption: ''
        }
    });

    if (!heroSection || !heroSwiperContainer || !heroSwiperWrapper || !thumbnailContainer || !swiperProgressIndicator) {
        console.error("Gallery init failed: Essential HTML elements for hero gallery missing.");
        return;
    }
    if (typeof images === 'undefined' || !Array.isArray(images)) {
        console.error("Gallery init failed: images is not defined or not an array.");
        if (heroSwiperWrapper) heroSwiperWrapper.innerHTML = '<p style="text-align:center; padding: 20px;">' + translate('no_images_to_display') + '</p>';
        return;
    }
    if (images.length === 0) {
        if (heroSwiperWrapper) heroSwiperWrapper.innerHTML = '<p style="text-align:center; padding: 20px;">' + translate('no_images_to_display') + '</p>';
        if (thumbnailContainer) thumbnailContainer.style.display = 'none';
        if (swiperProgressIndicator) swiperProgressIndicator.style.display = 'none';
        return;
    }

    const fancyboxGalleryItems = images.map(item => ({
        src: item.src, thumb: item.thumb, caption: item.caption || ''
    }));

    function destroySwiper() {
        if (heroSwiperInstance) {
            heroSwiperInstance.destroy(true, true);
            heroSwiperInstance = null;
        }
    }

    function handleImageLoad(imgElement) {
        imgElement.classList.remove('loading');
        imgElement.classList.add('loaded');
    }

    function handleImageError(imgElement) {
        imgElement.classList.remove('loading');
        imgElement.classList.add('error');
        const parentAnchor = imgElement.closest('a');
        if (parentAnchor) parentAnchor.classList.add('img-error');
        console.warn('Image failed to load:', imgElement.src || imgElement.dataset.srcLarge);
    }

    function openFancyboxWithFullGallery(startIndex = 0) {
        if (typeof Fancybox !== 'undefined' && fancyboxGalleryItems.length > 0) {
            Fancybox.show(fancyboxGalleryItems, {
                startIndex: startIndex, Hash: false,
                Toolbar: { display: { left: ["infobar"], middle: [], right: ["slideshow", "thumbs", "fullscreen", "close"] } },
                Thumbs: { autoStart: true, type: "classic" },
                Carousel: { infinite: images.length > 1 },
                on: {
                    'selectSlide': (fancyboxInstance, carousel, slide) => {
                        if (heroSwiperInstance && heroSwiperContainer.classList.contains('slider-layout-active')) {
                            if (slide && typeof slide.index !== 'undefined') {
                                heroSwiperInstance.slideToLoop(slide.index);
                            }
                        }
                    }
                }
            });
        } else {
            console.error("Fancybox library not loaded or no gallery items for Fancybox.");
        }
    }

    function createSlideAnchorAndImage(item, itemGlobalIndex, isSliderViewLayout, isOverlay = false, overlayText = '') {
        const anchor = document.createElement('a');
        anchor.href = item.src;
        anchor.dataset.caption = item.caption || `Image ${itemGlobalIndex + 1}`;

        const img = document.createElement('img');
        img.alt = item.caption || `Image ${itemGlobalIndex + 1}`;
        img.loading = "lazy";
        img.classList.add('loading');

        if (!isSliderViewLayout && itemGlobalIndex === 0) { // Grid main image
            img.src = item.src;
        } else { // Thumb for small grid items OR slider initial load
            img.src = item.thumb;
            if (isSliderViewLayout) { // Only for slider, store large src
                img.dataset.srcLarge = item.src;
            }
        }

        img.addEventListener('load', function () {
            if (isSliderViewLayout && this.dataset.srcLarge && this.src !== this.dataset.srcLarge) {
                this.src = this.dataset.srcLarge; // Upgrade to large image for slider (will trigger another 'load')
            } else {
                handleImageLoad(this); // Mark as loaded (thumb or final large src)
            }
        });
        img.addEventListener('error', function () { handleImageError(this); });
        anchor.appendChild(img);

        anchor.addEventListener('click', (e) => {
            e.preventDefault();
            openFancyboxWithFullGallery(itemGlobalIndex);
        });

        if (isOverlay) {
            anchor.dataset.morePhotosText = overlayText;
        }
        return anchor;
    }

    function buildSlidesHtml(isSliderViewLayout) {
        heroSwiperWrapper.innerHTML = '';
        if (thumbnailContainer) thumbnailContainer.innerHTML = '';
        if (images.length === 0) return;

        if (!isSliderViewLayout) { // GRID LAYOUT
            const mainItem = images[0];
            const mainSlide = document.createElement('div');
            mainSlide.classList.add('swiper-slide', 'grid-main-image');
            mainSlide.appendChild(createSlideAnchorAndImage(mainItem, 0, false));
            heroSwiperWrapper.appendChild(mainSlide);

            if (images.length > 1) {
                const smallImagesGridEl = document.createElement('div');
                smallImagesGridEl.classList.add('small-images-grid');
                const isLargeGrid = innerWidth > MEDIUM_GRID_BREAKPOINT_MAX;
                const maxSmallSlots = isLargeGrid ? MAX_SMALL_IMAGES_LARGE_GRID : MAX_SMALL_IMAGES_MEDIUM_GRID;
                const numSmallAvailable = images.length - 1;
                const numSmallToDisplay = Math.min(numSmallAvailable, maxSmallSlots);
                let smallSlidesHtml = [];

                for (let i = 0; i < numSmallToDisplay; i++) {
                    const itemIdx = i + 1;
                    const item = images[itemIdx];
                    const isLastDisplayedSmall = (i === numSmallToDisplay - 1);
                    const hasMoreHidden = numSmallAvailable > maxSmallSlots;
                    const applyOverlay = isLastDisplayedSmall && hasMoreHidden;

                    let overlayText = '';
                    if (applyOverlay) {
                        const totalTrulyHidden = numSmallAvailable - numSmallToDisplay;
                        overlayText = `+${translate('more_photos_overlay', {
                            'count': totalTrulyHidden
                        })}`;
                        // Alternative: overlayText = `عرض الكل (${images.length})`;
                    }

                    const slideDiv = document.createElement('div');
                    slideDiv.classList.add('swiper-slide');
                    if (applyOverlay) slideDiv.classList.add('has-more-photos-overlay');
                    slideDiv.appendChild(createSlideAnchorAndImage(item, itemIdx, false, applyOverlay, overlayText));
                    smallSlidesHtml.push(slideDiv);
                }

                if (isLargeGrid && smallSlidesHtml.length >= 3 && maxSmallSlots === 4) {
                    const row1 = document.createElement('div'); row1.classList.add('small-image-row');
                    const row2 = document.createElement('div'); row2.classList.add('small-image-row');
                    smallSlidesHtml.forEach((slide, idx) => (idx < 2 ? row1.appendChild(slide) : row2.appendChild(slide)));
                    if (row1.hasChildNodes()) smallImagesGridEl.appendChild(row1);
                    if (row2.hasChildNodes()) smallImagesGridEl.appendChild(row2);
                } else {
                    smallSlidesHtml.forEach(slide => smallImagesGridEl.appendChild(slide));
                }
                if (smallImagesGridEl.hasChildNodes()) heroSwiperWrapper.appendChild(smallImagesGridEl);
            }
        } else { // SLIDER LAYOUT
            images.forEach((item, index) => {
                const slide = document.createElement('div');
                slide.classList.add('swiper-slide');
                slide.appendChild(createSlideAnchorAndImage(item, index, true));
                heroSwiperWrapper.appendChild(slide);

                if (thumbnailContainer) {
                    const thumbImg = document.createElement('img');
                    thumbImg.src = item.thumb;
                    thumbImg.alt = `Thumbnail ${index + 1}`;
                    thumbImg.loading = "lazy";
                    thumbImg.classList.add('thumbnail-v3', 'loading');
                    thumbImg.dataset.index = index;
                    thumbImg.addEventListener('load', function () { handleImageLoad(this); });
                    thumbImg.addEventListener('error', function () { handleImageError(this); });
                    thumbImg.onclick = () => {
                        if (heroSwiperInstance) heroSwiperInstance.slideToLoop(index);
                    };
                    thumbnailContainer.appendChild(thumbImg);
                }
            });
        }
    }

    function updateSwiperProgress(swiper) {
        if (swiperProgressIndicator && swiper && swiper.slides && swiper.slides.length > 0 && images.length > 0) {
            const current = swiper.realIndex !== undefined ? swiper.realIndex + 1 : 1;
            swiperProgressIndicator.textContent = `${current} / ${images.length}`;
        } else if (swiperProgressIndicator) {
            swiperProgressIndicator.textContent = '';
        }
    }

    function setupHeroLayout() {
        const screenWidth = innerWidth;
        destroySwiper();
        heroSection.classList.add('layout-changing');
        heroSwiperContainer.className = 'swiper hero-swiper'; // Reset classes
        if (thumbnailContainer) thumbnailContainer.style.display = 'none';
        if (swiperProgressIndicator) swiperProgressIndicator.style.display = 'none';

        const useSliderLayout = screenWidth <= SLIDER_BREAKPOINT_MAX || images.length <= 1;

        heroSection.classList.toggle('grid-active-section', !useSliderLayout);
        heroSection.classList.toggle('slider-active-section', useSliderLayout);

        if (useSliderLayout) {
            heroSwiperContainer.classList.add('slider-layout-active');
            if (thumbnailContainer && images.length > 1) thumbnailContainer.style.display = 'flex';
            if (swiperProgressIndicator && images.length > 0) swiperProgressIndicator.style.display = 'block';
            buildSlidesHtml(true);

            if (typeof Swiper !== 'undefined' && images.length > 0) {
                heroSwiperInstance = new Swiper(heroSwiperContainer, {
                    loop: images.length > 1,
                    slidesPerView: 1,
                    spaceBetween: 10,
                    pagination: { el: '.swiper-pagination-slider', clickable: true, dynamicBullets: images.length > 5 },
                    navigation: { nextEl: '.swiper-button-next-slider', prevEl: '.swiper-button-prev-slider' },
                    on: {
                        init: updateSwiperProgress,
                        slideChange: function (swiper) {
                            if (thumbnailContainer) {
                                thumbnailContainer.querySelectorAll('.thumbnail-v3').forEach(t => t.classList.remove('active'));
                                const activeThumb = thumbnailContainer.querySelector(`.thumbnail-v3[data-index="${swiper.realIndex}"]`);
                                if (activeThumb) activeThumb.classList.add('active');
                            }
                            updateSwiperProgress(swiper);
                        }
                    }
                });
                if (thumbnailContainer && images.length > 1) {
                    const firstThumb = thumbnailContainer.querySelector('.thumbnail-v3[data-index="0"]');
                    if (firstThumb) firstThumb.classList.add('active');
                }
                if (heroSwiperInstance) updateSwiperProgress(heroSwiperInstance);
            }
        } else { // GRID LAYOUT
            heroSwiperContainer.classList.add('grid-layout-active');
            buildSlidesHtml(false);
            heroSwiperContainer.classList.toggle('large-grid', screenWidth > MEDIUM_GRID_BREAKPOINT_MAX);
            heroSwiperContainer.classList.toggle('medium-grid', screenWidth > SLIDER_BREAKPOINT_MAX && screenWidth <= MEDIUM_GRID_BREAKPOINT_MAX);

            heroSwiperWrapper.querySelectorAll('.swiper-slide:not(.has-more-photos-overlay) > a').forEach(anchor => {
                anchor.addEventListener('mouseenter', () => {
                    if (heroSwiperContainer.classList.contains('grid-layout-active')) {
                        heroSwiperContainer.classList.add('grid-hover-active');
                    }
                });
                anchor.addEventListener('mouseleave', () => {
                    if (heroSwiperContainer.classList.contains('grid-layout-active')) {
                        heroSwiperContainer.classList.remove('grid-hover-active');
                    }
                });
            });
        }
        setTimeout(() => heroSection.classList.remove('layout-changing'), 100); // Slightly longer for render
    }

    // --- Floating Contact Button Logic (Included here as per previous structure) ---
    // If you have a separate floatingButton.js, manage this there.
    const floatingContactBtn = document.getElementById('floatingContactBtnV3');
    const agentContactCardForButton = document.querySelector('.agent-contact-card-v3');

    function isElementInViewport(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        return (
            rect.top < innerHeight && rect.bottom >= 0 &&
            rect.left < innerWidth && rect.right >= 0
        );
    }

    function checkFloatingButtonVisibility() {
        if (!floatingContactBtn) return;
        const agentCardInView = isElementInViewport(agentContactCardForButton);

        if (agentCardInView) {
            floatingContactBtn.classList.remove('visible');
        } else {
            floatingContactBtn.classList.add('visible'); // Always visible if agent card is not
        }
    }

    if (floatingContactBtn) {
        // Check if agentContactCardForButton exists before adding listeners that depend on it
        if (agentContactCardForButton) {
            addEventListener('scroll', checkFloatingButtonVisibility, { passive: true });
            addEventListener('resize', checkFloatingButtonVisibility);
        }
        // Initial check should happen after a small delay if agent card might render later
        // or if it might not exist on all pages where this script runs.
        setTimeout(checkFloatingButtonVisibility, 150);


        floatingContactBtn.addEventListener('click', () => {
            const agentCardToScroll = document.querySelector('.agent-contact-card-v3');
            if (agentCardToScroll) {
                agentCardToScroll.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert("Contact section not found."); // Fallback action
            }
        });
    }
    // --- End Floating Contact Button Logic ---

    setupHeroLayout();
    let resizeTimeout;
    addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(setupHeroLayout, 250);
    });

} // End of initGallery