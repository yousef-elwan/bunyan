import { translate } from '../../utils/helpers';
import { openModal, closeModal } from './modals';

/**
 * @file shareAction.js
 * @description Handles the "Share Property" modal opening, link copying, and social share links.
 */

/**
 * Initializes the share modal and its associated actions.
 */
export function initShareAction() {

    console.log('[ShareAction] initShareAction() CALLED');

    // --- Share Modal Elements ---
    const shareModal = document.getElementById('sharePropertyModal');
    const shareBtnTrigger = document.querySelectorAll('.share-btn-trigger-v3');
    const closeShareModalBtn = document.querySelector('[data-close-share-modal]');
    const propertyShareLinkInput = document.getElementById('propertyShareLink');
    const copyShareLinkBtn = document.getElementById('copyShareLinkBtn');
    const copyShareLinkBtnText = copyShareLinkBtn ? copyShareLinkBtn.querySelector('.copy-btn-text') : null;
    const shareViaWhatsApp = document.getElementById('shareViaWhatsApp');
    const shareViaTwitter = document.getElementById('shareViaTwitter');
    const shareViaFacebook = document.getElementById('shareViaFacebook');
    const shareViaEmail = document.getElementById('shareViaEmail');
    const shareViaLinkedIn = document.getElementById('shareViaLinkedIn');
    const shareViaTelegram = document.getElementById('shareViaTelegram');

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
                        if (textElementForButton) textElementForButton.textContent = buttonElement.id === 'copyShareLinkBtn' ? translate('copy_link') :
                            translate('copy_number');
                        if (feedbackElement) feedbackElement.classList.remove('show');
                        buttonElement.disabled = false;
                        const icon = buttonElement.querySelector('i'); // Reset icon for both types
                        if (icon && !buttonElement.classList.contains('copied')) icon.className = 'far fa-copy'; // Check if still copied
                    }, 2000);
                })
                .catch(err => {
                    console.error('Clipboard copy failed: ', err);
                    alert(buttonElement.id === 'copyShareLinkBtn' ? translate('copy_fail') : translate('copy_fail'));
                });
        } else {
            alert("Clipboard API not supported")
        }
    }

    function openShareModal() {
        console.log("openShareModal");
        console.log("shareModal", shareModal);
        if (!shareModal) return;
        const propertyURL = window.location.href;
        let propertyTitle = document.title;
        const h1TitleElement = document.querySelector('.property-intro-v3 .title-location-v3 h1');
        if (h1TitleElement) propertyTitle = h1TitleElement.textContent.trim();

        if (propertyShareLinkInput) propertyShareLinkInput.value = propertyURL;

        const encodedURL = encodeURIComponent(propertyURL);
        const encodedTitle = encodeURIComponent(propertyTitle);

        if (shareViaWhatsApp) shareViaWhatsApp.href = `https://wa.me/?text=${encodedTitle}%20-%20${encodedURL}`;
        if (shareViaTwitter) shareViaTwitter.href = `https://twitter.com/intent/tweet?url=${encodedURL}&text=${encodedTitle}`;
        if (shareViaFacebook) shareViaFacebook.href = `https://www.facebook.com/sharer/sharer.php?u=${encodedURL}`;
        if (shareViaEmail) shareViaEmail.href = `mailto:?subject=${encodedTitle}&body=Check%20out%20this%20property:%20${encodedURL}`;
        if (shareViaLinkedIn) shareViaLinkedIn.href = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedURL}`;
        if (shareViaTelegram) shareViaTelegram.href = `https://t.me/share/url?url=${encodedURL}&text=${encodedTitle}`;

        if (copyShareLinkBtn) {
            copyShareLinkBtn.classList.remove('copied');
            copyShareLinkBtn.disabled = false;
            if (copyShareLinkBtnText) copyShareLinkBtnText.textContent = translate('copy_link');
            const icon = copyShareLinkBtn.querySelector('i');
            if (icon) icon.className = 'far fa-copy';
        }
        openModal(shareModal);
    }
    function closeTheShareModal() { closeModal(shareModal); }

    // --- Main Initialization for Share Modal ---

    shareBtnTrigger.forEach(btn => {
        btn.addEventListener('click', openShareModal);
        if (closeShareModalBtn) closeShareModalBtn.addEventListener('click', closeTheShareModal);
        if (copyShareLinkBtn && propertyShareLinkInput && copyShareLinkBtnText) {
            copyShareLinkBtn.addEventListener('click', () => {
                handleCopyToClipboard(copyShareLinkBtn, propertyShareLinkInput.value, copyShareLinkBtnText, translate('copy_success'), null);
            });
        }
    });
    // function init() {
    //     if (!shareBtnTrigger) return;
    // }
    // init();
}
