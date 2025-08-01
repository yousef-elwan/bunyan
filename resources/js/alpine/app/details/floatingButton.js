import { translate } from "../../utils/helpers";

export function initFloatingButton() {
    const floatingContactBtn = document.getElementById('floatingContactBtnV3');
    const agentContactCard = document.querySelector('.agent-contact-card-v3');

    function isElementInViewport(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        // Check if any part of the element is within the viewport
        const verticalInView = rect.top < window.innerHeight && rect.bottom >= 0;
        const horizontalInView = rect.left < window.innerWidth && rect.right >= 0;
        return verticalInView && horizontalInView;
    }

    function checkFloatingButtonVisibility() {
        if (!floatingContactBtn) return;

        const agentCardInView = isElementInViewport(agentContactCard);
        // const footerInView = isElementInViewport(mainFooter); // If you want to hide it when footer is visible

        if (agentCardInView /* || footerInView */) {
            floatingContactBtn.classList.remove('visible');
        } else {
            // Always visible if agent card (and optionally footer) is NOT in view
            floatingContactBtn.classList.add('visible');
        }
    }

    // Initialization (ensure agentContactCard is likely to exist)
    if (floatingContactBtn) {
        // Delay check slightly if agentContactCard might be rendered late
        // setTimeout(() => {
        if (document.querySelector('.agent-contact-card-v3')) { // Check if it exists now
            window.addEventListener('scroll', checkFloatingButtonVisibility, { passive: true });
            window.addEventListener('resize', checkFloatingButtonVisibility);
            checkFloatingButtonVisibility(); // Initial check
        } else {
            // If agent card might not always be on the page,
            // you might want a fallback to always show the button or a different condition.
            // For now, if no agent card, this logic won't attach listeners.
            // To always show if no agent card, you could do:
            // floatingContactBtn.classList.add('visible');
            console.warn("Agent contact card not found for floating button visibility logic. Button may not behave as expected.");
        }
        // }, 100);


        floatingContactBtn.addEventListener('click', () => {
            const agentCard = document.querySelector('.agent-contact-card-v3'); // Re-select in case it was added dynamically
            if (agentCard) {
                agentCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert(translate('contact_us_action'));
            }
        });
    }
}
