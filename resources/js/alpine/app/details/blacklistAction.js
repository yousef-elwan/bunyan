import { http } from '../../utils/api';
import { showAuthRequired, showModalMessage, getRoute, translate } from '../../utils/helpers';
import { openModal, closeModal } from './modals';


export function initBlacklistAction() {

    console.log('[BlacklistAction] initBlacklistAction() CALLED');

    const blacklistModal = document.getElementById('blacklistConfirmModal');
    const blacklistBtnTrigger = document.querySelector('.blacklist-btn-trigger-v3');

    if (!blacklistBtnTrigger) {
        console.log('[BlacklistAction] No .blacklist-btn-trigger-v3 found.');
        return;
    }
    if (!blacklistModal) {
        console.warn('[BlacklistAction] .blacklist-btn-trigger-v3 found, but #blacklistConfirmModal not found.');
        return;
    }

    const closeBlacklistModalBtn = blacklistModal.querySelector('[data-close-blacklist-modal]');
    const confirmBlacklistBtn = document.getElementById('confirmBlacklistBtn');
    const cancelBlacklistBtn = document.getElementById('cancelBlacklistBtn'); // Usually same as closeBlacklistModalBtn or separate
    const blacklistConfirmMessage = document.getElementById('blacklistConfirmMessage');
    let isBlacklisted = blacklistBtnTrigger.classList.contains('active'); // Initial state from class
    const blacklistPropertyId = blacklistBtnTrigger.dataset.propertyId;

    if (!blacklistPropertyId) {
        console.warn('[BlacklistAction] Blacklist button .blacklist-btn-trigger-v3 is missing data-property-id.');
        // Optionally disable the button or hide it if propertyId is crucial and missing
        // blacklistBtnTrigger.style.display = 'none';
        // return;
    }

    /** Updates the visual state of the blacklist button (icon, tooltip, active class). */
    function updateBlacklistButtonVisuals() {
        if (!blacklistBtnTrigger) return;
        const icon = blacklistBtnTrigger.querySelector('i');
        blacklistBtnTrigger.classList.toggle('active', isBlacklisted);
        if (icon) { // Assuming icon also gets an 'active-blacklist' class or similar
            icon.classList.toggle('active-blacklist', isBlacklisted); // Or adjust icon classes directly e.g. fas/far
        }
        blacklistBtnTrigger.dataset.tooltip = isBlacklisted ?
            translate('blacklist_remove_tooltip') :
            translate('blacklist_add_tooltip');
    }

    /** Opens the blacklist confirmation dialog, setting appropriate messages. */
    function openBlacklistDialog() {
        if (!blacklistConfirmMessage || !confirmBlacklistBtn) {
            console.error('[BlacklistAction] Blacklist modal essential elements (message/confirm button) missing.');
            showModalMessage(
                'error',
                {
                    bodyHtml: translate('generic_error_try_again'),
                    buttons: [
                        {
                            text: translate('close'),
                            class: 'my-modal-btn-primary'
                        }
                    ],
                    showCloseIcon: true
                });
            return;
        }
        blacklistConfirmMessage.textContent = isBlacklisted ?
            translate('blacklist_remove_confirm_message') :
            translate('blacklist_add_confirm_message');

        confirmBlacklistBtn.textContent = isBlacklisted ?
            translate('blacklist_confirm_remove_button') :
            translate('blacklist_confirm_add_button');

        const modalTitleElement = blacklistModal.querySelector('.modal-title');
        if (modalTitleElement) {
            modalTitleElement.textContent = isBlacklisted ?
                translate('blacklist_remove_confirm_title') :
                translate('blacklist_add_confirm_title');
        }
        openModal(blacklistModal)
        blacklistModal.style.display = 'flex'; // Or use Bootstrap modal('show') if applicable
    }

    /** Closes the blacklist confirmation dialog and resets the confirm button state. */
    function closeTheBlacklistDialog() {
        blacklistModal.style.display = 'none'; // Or use Bootstrap modal('hide')
        if (confirmBlacklistBtn) {
            confirmBlacklistBtn.disabled = false;
            const spinner = confirmBlacklistBtn.querySelector('.spinner-border');
            if (spinner) spinner.classList.add('d-none');
            // Restore original button text if it was changed to loading
        }
        closeModal(blacklistModal)
    }

    // Initialize button visuals on load
    updateBlacklistButtonVisuals();

    blacklistBtnTrigger.addEventListener('click', () => {
        console.log('[BlacklistAction] BlacklistBtn clicked, PropID:', blacklistPropertyId);
        if (!window.AppConfig.isAuthenticated) {
            showAuthRequired();
            return;
        }
        if (!blacklistPropertyId) { // Double check, though checked at init
            showModalMessage(
                'error',
                {
                    bodyHtml: translate('generic_error_try_again'),
                    buttons: [
                        {
                            text: translate('close'),
                            class: 'my-modal-btn-primary'
                        }
                    ],
                    showCloseIcon: true
                });
            return;
        }
        openBlacklistDialog();
    });

    if (closeBlacklistModalBtn) closeBlacklistModalBtn.addEventListener('click', closeTheBlacklistDialog);
    if (cancelBlacklistBtn) cancelBlacklistBtn.addEventListener('click', closeTheBlacklistDialog);

    // Close modal on Escape key or click outside
    blacklistModal.addEventListener('click', (event) => {
        if (event.target === blacklistModal) closeTheBlacklistDialog();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && blacklistModal.style.display === 'flex') {
            closeTheBlacklistDialog();
        }
    });

    if (confirmBlacklistBtn) {
        confirmBlacklistBtn.addEventListener('click', async () => {
            if (!window.AppConfig.isAuthenticated) { // Should be caught by trigger, but good for safety
                showAuthRequired();
                closeTheBlacklistDialog(); // Close modal as auth will take over
                return;
            }
            if (!blacklistPropertyId) {
                showModalMessage(
                    'error',
                    {
                        bodyHtml: translate('generic_error_try_again'),
                        buttons: [
                            {
                                text: translate('close'),
                                class: 'my-modal-btn-primary'
                            }
                        ],
                        showCloseIcon: true
                    });
                closeTheBlacklistDialog();
                return;
            }
            // if (!window.AppConfig.csrfToken) {
            //     showModalMessage(
            //         'error',
            //         {
            //             bodyHtml: translate('csrf_error'),
            //             buttons: [
            //                 {
            //                     text: translate('ok_button_text'),
            //                     class: 'my-modal-btn-primary'
            //                 }
            //             ],
            //             showCloseIcon: true
            //         });
            //     closeTheBlacklistDialog();
            //     return;
            // }
            if (!getRoute('properties.toggle-blacklist')) {
                showModalMessage(
                    'error',
                    {
                        bodyHtml: translate('generic_error_try_again'),
                        buttons: [
                            {
                                text: translate('close'),
                                class: 'my-modal-btn-primary'
                            }
                        ],
                        showCloseIcon: true
                    });
                closeTheBlacklistDialog();
                return;
            }

            const apiUrl = getRoute('properties.toggle-blacklist').replace('{propertyId}', blacklistPropertyId);
            const spinner = confirmBlacklistBtn.querySelector('.spinner-border');
            // const originalButtonText = confirmBlacklistBtn.textContent; // Store if changing to loading

            confirmBlacklistBtn.disabled = true;
            if (spinner) spinner.classList.remove('d-none');
            // confirmBlacklistBtn.textContent = translate('loading_text');


            try {
                const response = await http({
                    onStatusCodeError: {
                        401: (error) => {
                            const message = error?.response?.data?.message;

                            showModalMessage?.(
                                'error',
                                {
                                    bodyHtml: message || translate('auth_required_message'),
                                    buttons: [
                                        {
                                            text: translate('ok_button_text'),
                                            class: 'my-modal-btn-primary'
                                        }
                                    ],
                                    showCloseIcon: true
                                });
                            closeTheBlacklistDialog();
                        }
                    }
                }).post(apiUrl, {});

                const data = response.data;

                if (data.success === true && data.data) {
                    isBlacklisted = data.data.is_active;
                    updateBlacklistButtonVisuals();
                    showModalMessage(
                        'success',
                        {
                            bodyHtml: data.message,
                            timerSeconds: 1,
                            showCloseIcon: true
                        });
                } else {
                    showModalMessage(
                        'error',
                        {
                            bodyHtml: data.message || translate('blacklist_action_failed'),
                            buttons: [
                                {
                                    text: translate('ok_button_text'),
                                    class: 'my-modal-btn-primary'
                                }
                            ],
                            showCloseIcon: true
                        });
                }
            } catch (error) {
                console.error('[BlacklistAction] Blacklist API error:', error);
                showModalMessage(
                    'error',
                    {
                        bodyHtml: translate('network_error'),
                        buttons: [
                            {
                                text: translate('ok_button_text'),
                                class: 'my-modal-btn-primary'
                            }
                        ],
                        showCloseIcon: true
                    });
            } finally {
                // Resetting button state (disabled, spinner, text) is handled in closeTheBlacklistDialog
                closeTheBlacklistDialog();
            }
        });
    } else {
        console.error('[BlacklistAction] Critical: #confirmBlacklistBtn not found inside #blacklistConfirmModal.');
    }
}
