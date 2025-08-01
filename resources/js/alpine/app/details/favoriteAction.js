

import {  showAuthRequired, showModalMessage,getRoute, translate } from '../../utils/helpers';
import { http } from '../../utils/api';

export function initFavoriteAction() {

    console.log('[FavoriteAction] initFavoriteAction() CALLED');

    const favoriteBtnV3 = document.querySelector('.favorite-btn-v3');

    if (!favoriteBtnV3) {
        console.log('[FavoriteAction] No .favorite-btn-v3 found.');
        return;
    }

    const propertyId = favoriteBtnV3.dataset.propertyId;
    const favIcon = favoriteBtnV3.querySelector('i');

    // Initialize button visual state based on 'active' class
    if (favIcon) {
        if (favoriteBtnV3.classList.contains('active')) {
            favIcon.classList.remove('far', 'fa-regular');
            favIcon.classList.add('fas', 'fa-solid');
            favoriteBtnV3.dataset.tooltip = translate('favorite_remove_tooltip');
        } else {
            favIcon.classList.remove('fas', 'fa-solid');
            favIcon.classList.add('far', 'fa-regular');
            favoriteBtnV3.dataset.tooltip = translate('favorite_add_tooltip');
        }
    }

    favoriteBtnV3.addEventListener('click', async () => {
        console.log('[FavoriteAction] FavBtn clicked, PropID:', propertyId);

        if (!window.AppConfig.isAuthenticated) {
            showAuthRequired();
            return;
        }
        if (!propertyId) {
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
        // if (!window.AppConfig.csrfToken) {
        //     showModalMessage(
        //         'error',
        //         {
        //             bodyHtml: translate('csrf_error'),
        //             buttons: [
        //                 {
        //                     text: translate('close'),
        //                     class: 'my-modal-btn-primary'
        //                 }
        //             ],
        //             showCloseIcon: true
        //         });

        //     return;
        // }
        if (!getRoute('properties.toggle-favorite')) {
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

        const apiUrl = getRoute('properties.toggle-favorite').replace('{propertyId}', propertyId);
        const spinner = favoriteBtnV3.querySelector('.spinner-border');
        const btnTextEl = favoriteBtnV3.querySelector('.btn-text');
        const originalText = btnTextEl ? btnTextEl.textContent : '';

        favoriteBtnV3.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
        if (btnTextEl && translate('loading_text')) btnTextEl.textContent = translate('loading_text');

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
                        favoriteBtnV3.disabled = false;
                        if (spinner) spinner.classList.add('d-none');
                        if (btnTextEl) btnTextEl.textContent = originalText;
                    }
                }
            }).post(apiUrl, {});

            const data = response.data;

            if (data.success === true && data.data) {
                const isNowActive = data.data.is_active;
                favoriteBtnV3.classList.toggle('active', isNowActive);
                if (favIcon) {
                    if (isNowActive) {
                        favIcon.classList.remove('far', 'fa-regular');
                        favIcon.classList.add('fas', 'fa-solid');
                        favoriteBtnV3.dataset.tooltip = translate('favorite_remove_tooltip');
                    } else {
                        favIcon.classList.remove('fas', 'fa-solid');
                        favIcon.classList.add('far', 'fa-regular');
                        favoriteBtnV3.dataset.tooltip = translate('favorite_add_tooltip');
                    }
                }
                if (data.message) {
                    showModalMessage(
                        'success',
                        {
                            bodyHtml: data.message,
                            timerSeconds: 1,
                            showCloseIcon: true
                        });
                }
            } else {
                showModalMessage(
                    'error',
                    {
                        bodyHtml: data.message || translate('favorite_action_failed'),
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
            console.error('[FavoriteAction] Favorite API error:', error);
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
            favoriteBtnV3.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            if (btnTextEl) btnTextEl.textContent = originalText;
        }
    });
}
