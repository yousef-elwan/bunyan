// js/ui/videoModal.js (or add to modals.js or tabs.js)
export function initVideoModal() {
    const videoPlaceholder = document.getElementById('videoPlaceholderV3');

    if (videoPlaceholder && window.AppConfig.pageData.video_url) {
        videoPlaceholder.addEventListener('click', () => {
            if (typeof Fancybox !== 'undefined') {
                Fancybox.show([{
                    src: window.AppConfig.pageData.video_url + '?autoplay=1',
                    type: 'iframe',
                    preload: false,
                    // caption: __('view_video_tour'), // Use translated string
                    iframe: {
                        attr: {
                            allow: "autoplay; fullscreen",
                            allowfullscreen: true
                        }
                    }
                }]);
            } else {
                // Fallback or custom modal logic if Fancybox isn't available
                alert(__('video_not_available')); // Or "Fancybox not loaded"
                console.warn("Fancybox not loaded. Video cannot be opened in a modal.");
            }
        });
    }
}