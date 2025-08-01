import { translate } from '../../utils/helpers';
import { openModal, closeModal } from './modals';

/**
 * @file showMobileAction.js
 * @description Handles the "Show Mobile Number" modal opening, copying, and display.
 */

/**
 * Initializes the show mobile modal and its associated actions.
 */
export function initShowMobileAction() {
    // --- Show Mobile Modal Elements ---
    const showMobileModal = document.getElementById('showMobileModal');
    const closeMobileModalBtn = document.querySelector('[data-close-mobile-modal]');
    const agentMobileNumberDisplay = document.getElementById('agentMobileNumberDisplay');
    const callAgentLink = document.getElementById('callAgentLink');
    const copyMobileNumberBtn = document.getElementById('copyMobileNumberBtnV3'); // This is now the inline icon button

    // --- Helper function for copy to clipboard with animation ---
    function handleCopyToClipboard(buttonElement, textToCopy, textElementForButton, copiedTextForButton, feedbackElement) {
        if (!buttonElement || !textToCopy) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {

            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    buttonElement.classList.add('copied');
                    if (textElementForButton) textElementForButton.textContent = copiedTextForButton; // For full buttons
                    if (feedbackElement) feedbackElement.classList.add('show'); // For inline icon feedback
                    buttonElement.disabled = true;

                    setTimeout(() => {
                        buttonElement.classList.remove('copied');
                        if (textElementForButton) textElementForButton.textContent = translate('copy_number');
                        if (feedbackElement) feedbackElement.classList.remove('show');
                        buttonElement.disabled = false;
                        const icon = buttonElement.querySelector('i'); // Reset icon for both types
                        if (icon && !buttonElement.classList.contains('copied')) icon.className = 'far fa-copy'; // Check if still copied
                    }, 2000);
                })
                .catch(err => {
                    console.error('Clipboard copy failed: ', err);
                    alert(translate('copy_fail'));
                });
        } else {
            alert("Clipboard API not supported")
        }
    }

    function openShowMobileModal() {
        const number = window.AppConfig.pageData.owner.mobile;
        if (!showMobileModal) return;
        if (agentMobileNumberDisplay) {
            agentMobileNumberDisplay.textContent = translate('agent_number');
            setTimeout(() => {
                if (typeof number !== 'undefined' && number) {
                    agentMobileNumberDisplay.textContent = number;
                    if (callAgentLink) callAgentLink.href = `tel:${number}`;
                    if (copyMobileNumberBtn) copyMobileNumberBtn.style.display = 'flex';
                    if (callAgentLink) callAgentLink.style.display = 'inline-flex';
                } else {
                    agentMobileNumberDisplay.textContent = translate('number_not_found');
                    if (callAgentLink) callAgentLink.style.display = 'none';
                    if (copyMobileNumberBtn) copyMobileNumberBtn.style.display = 'none';
                }
            }, 200);
        }
        if (copyMobileNumberBtn) {
            copyMobileNumberBtn.classList.remove('copied');
            copyMobileNumberBtn.disabled = false;
            const icon = copyMobileNumberBtn.querySelector('i');
            if (icon) icon.className = 'far fa-copy';
            const feedback = copyMobileNumberBtn.querySelector('.copied-feedback-v3');
            if (feedback) feedback.classList.remove('show');
        }
        openModal(showMobileModal);
    }
    function closeTheMobileModal() { closeModal(showMobileModal); }

    // --- Main Initialization for Show Mobile Modal ---
    function init() {
        const showMobileBtn = document.querySelector('.show-mobile-btn'); // Assuming this is the button class to open modal
        if (showMobileBtn) {
            showMobileBtn.addEventListener('click', openShowMobileModal);
        }
        if (closeMobileModalBtn) closeMobileModalBtn.addEventListener('click', closeTheMobileModal);
        if (copyMobileNumberBtn && agentMobileNumberDisplay) {
            copyMobileNumberBtn.addEventListener('click', () => {
                const numberToCopy = agentMobileNumberDisplay.textContent.replace(/\s+/g, '');
                if (numberToCopy && numberToCopy !== translate('loading_text') && numberToCopy !== translate('number_not_found')) {
                    handleCopyToClipboard(copyMobileNumberBtn, numberToCopy, null, null, copyMobileNumberBtn.querySelector('.copied-feedback-v3'));
                }
            });
        }
    }

    init();
}
