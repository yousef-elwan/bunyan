import '../../utils/AOS-init';

import { showModalMessage, displayActionFormErrors, clearActionFormErrors, getRoute, translate } from '../../utils/helpers';
import { createPhoneInputValidator, initializePhoneInput } from '../../utils/phoneInput';
import { http } from '../../utils/api';

export function initContactAction() {


    const authUser = window.AppConfig.user;

    const phoneInput = document.querySelector("#contact-phone");
    const contactForm = document.getElementById('contactForm');

    const currentUserName = authUser?.name || '';
    const currentUserEmail = authUser?.email || '';
    const currentUserMobile = authUser?.mobile || '';

    const itiInstance = initializePhoneInput(phoneInput, currentUserName);
    const phoneInputValidator = createPhoneInputValidator(
        itiInstance,
        phoneInput,
        contactForm.querySelector('.invalid-feedback[data-field="mobile"]'),
        translate
    );
    phoneInputValidator.setupEventListeners();


    // Pre-fill name and email if authenticated (Blade might also do this, this is a JS fallback/override)
    if (window.AppConfig.isAuthenticated) {
        const nameInput = contactForm.querySelector('input[name="name"]');
        const emailInput = contactForm.querySelector('input[name="email"]');
        const phoneInputField = contactForm.querySelector('input[name="mobile"]');
        if (nameInput && !nameInput.value && currentUserName) nameInput.value = currentUserName;
        if (emailInput && !emailInput.value && currentUserEmail) emailInput.value = currentUserEmail;
        if (phoneInputField && !phoneInputField.value && currentUserMobile) phoneInputField.value = currentUserMobile;
    }

    contactForm.addEventListener('submit', async (event) => {
        event.preventDefault();

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
        if (!getRoute('contact-us')) {
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

        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn ? submitBtn.textContent : (translate('contact_send_button_text'));
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = translate('loading_text');
        }

        // Fields to clear: API error keys (e.g., mobile for the phone input)
        clearActionFormErrors(contactForm);

        const formData = new FormData(contactForm);

        const rawPhone = formData.get('mobile'); // This should be the name of your phone input field
        let fullPhone = rawPhone; // Default to raw input if validator is not used or number is invalid but submitted

        if (phoneInputValidator) {
            if (rawPhone && rawPhone.trim() !== '' && !phoneInputValidator.validate()) {
                displayActionFormErrors(contactForm, { mobile: [translate('phone_invalid')] }, true);
                showModalMessage(
                    'error',
                    {
                        bodyHtml: translate('phone_invalid'),
                        buttons: [
                            {
                                text: translate('close'),
                                class: 'my-modal-btn-primary'
                            }
                        ],
                        showCloseIcon: true
                    });
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }
                return;
            }
            if (phoneInputValidator.getInstance()?.isValidNumber()) {
                fullPhone = phoneInputValidator.getInstance().getNumber();
            } else if (!rawPhone || rawPhone.trim() === '') {
                fullPhone = "";
            }
        }

        const contactData = {
            name: formData.get('name'),
            email: formData.get('email'),
            subject: formData.get('subject'),
            mobile: fullPhone,
            message: formData.get('message')
        };

        try {
            const response = await http({
                onStatusCodeError: {
                    422: (error) => {
                        const message = error?.response?.data?.message;
                        const validationErrors = error?.response?.data?.errors;

                        let modalTitle = translate('error_title');
                        let modalBodyHtml = '';
                        let errorListForModal = '';

                        if (validationErrors && Object.keys(validationErrors).length > 0) {
                            if (displayActionFormErrors) {
                                displayActionFormErrors(contactForm, validationErrors);
                            } else {
                                console.warn("[ContactAction] displayActionFormErrors helper not available to show inline errors.");
                            }
                            modalTitle = translate('validation_error_title');
                            errorListForModal = `<ul style="text-align: ${window.AppConfig.isRTL ? 'right' : 'left'}; list-style-position: inside; padding-${window.AppConfig.isRTL ? 'right' : 'left'}: 0; margin:0;">`;
                            for (const field in validationErrors) {
                                validationErrors[field].forEach(errMsg => errorListForModal += `<li style="margin-bottom: 5px;">${errMsg}</li>`);
                            }
                            errorListForModal += '</ul>';
                            modalBodyHtml = translate('validation_error_summary') + '<br>' + errorListForModal;
                        } else if (message) {
                            modalBodyHtml = message;
                        } else {
                            modalBodyHtml = translate('generic_error_try_again');
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalBtnText;
                        }

                        showModalMessage?.(
                            'error',
                            {
                                title: modalTitle,
                                bodyHtml: modalBodyHtml,
                                buttons: [
                                    {
                                        text: translate('close'),
                                        class: 'my-modal-btn-primary'
                                    }
                                ],
                                showCloseIcon: true
                            });
                    },
                }
            }).post(getRoute('contact-us'), contactData);

            const data = response.data;

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            }

            showModalMessage?.(
                'success',
                {
                    bodyHtml: data.message || translate('contact_message_sent_success'),
                    timerSeconds: 2,
                    showCloseIcon: true
                });
            contactForm.reset();

            if (!window.AppConfig.isAuthenticated) {
                const nameInput = contactForm.querySelector('input[name="name"]');
                const emailInput = contactForm.querySelector('input[name="email"]');
                const phoneInputField = contactForm.querySelector('input[name="mobile"]');
                if (nameInput && currentUserName) nameInput.value = currentUserName;
                if (emailInput && currentUserEmail) emailInput.value = currentUserEmail;
                if (phoneInputField && currentUserMobile) phoneInputField.value = currentUserMobile;
            }
        } catch (error) {
            console.error('[ContactAction] Contact API error:', error);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            }
            showModalMessage?.(
                'error',
                {
                    bodyHtml: translate('network_error'),
                    buttons: [
                        {
                            text: translate('close'),
                            class: 'my-modal-btn-primary'
                        }
                    ],
                    showCloseIcon: true
                });
        }
        finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            }
        }
    });

}

document.addEventListener('DOMContentLoaded', function () {
    initContactAction()
});
