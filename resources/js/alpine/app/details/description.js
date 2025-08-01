import { translate } from "../../utils/helpers";

// js/ui/description.js
export function initDescription() {
    const seeMoreBtnV3 = document.getElementById('seeMoreBtnV3');
    const descriptionContentV3 = document.getElementById('descriptionContentV3');

    if (seeMoreBtnV3 && descriptionContentV3) {
        // Check if the content is actually overflowing
        // If content height is less than its scroll height, it means it's clamped
        const isContentClamped = descriptionContentV3.scrollHeight > descriptionContentV3.clientHeight;

        if (!isContentClamped) {
            // If there's nothing to expand, hide the button
            seeMoreBtnV3.style.display = 'none';
        } else {
            seeMoreBtnV3.addEventListener('click', () => {
                // We will toggle the 'short-v3' class which applies the line-clamp
                const isCurrentlyShort = descriptionContentV3.classList.contains('short-v3');

                if (isCurrentlyShort) {
                    descriptionContentV3.classList.remove('short-v3');
                    seeMoreBtnV3.textContent = translate('show_less');
                } else {
                    descriptionContentV3.classList.add('short-v3');
                    seeMoreBtnV3.textContent = translate('show_more');
                }
            });
        }
    }
}