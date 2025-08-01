AOS.init({
    // Global settings:
    disable: false, // accepts following values: 'phone', 'tablet', 'mobile', boolean, expression or function
    startEvent: 'DOMContentLoaded', // name of the event dispatched on the document, that AOSshould initialize on
    initClassName: 'aos-init', // class applied after initialization
    animatedClassName: 'aos-animate', // class applied on animation
    useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
    disableMutationObserver: false, // disables automatic mutations' detections (advanced)
    debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
    throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)

    // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
    offset: 120, // (px) offset (trigger LATE) from the original trigger point
    delay: 0, // (ms) values from 0 to 3000, with step 50ms
    duration: 600, // (ms) values from 0 to 3000, with step 50ms - DURATION OF ANIMATION
    easing: 'ease-in-out', // default easing for AOS animations
    once: false, // (boolean) whether animation should happen only once - while scrolling down
    mirror: false, // (boolean) whether elements should animate out while scrolling past them
    anchorPlacement: 'top-bottom', // defines which position of the element regarding to window should trigger the animation
});