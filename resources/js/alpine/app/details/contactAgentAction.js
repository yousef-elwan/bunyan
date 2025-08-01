import { showModalMessage, displayActionFormErrors, clearActionFormErrors, getRoute, translate } from '../../utils/helpers';
import { createPhoneInputValidator, initializePhoneInput } from '../../utils/phoneInput';
import { http } from '../../utils/api';

export function initContactAgentAction() {

    const authUser = window.AppConfig.user;

    const currentUserName = authUser?.name || '';
    const currentUserEmail = authUser?.email || '';
    const currentUserMobile = authUser?.mobile || '';

    const contactAgentForm = document.getElementById('contactAgentFormV3');
    const agentContactPhoneInput = document.getElementById('agentContactPhoneV3');
    const whatsAppNumber = window.AppConfig.pageData.owner.mobile;

    const itiInstance = initializePhoneInput(agentContactPhoneInput, currentUserMobile);
    const phoneInputValidator = createPhoneInputValidator(
        itiInstance,
        agentContactPhoneInput,
        contactAgentForm.querySelector('.invalid-feedback[data-field="mobile"]'),
        translate
    );
    phoneInputValidator.setupEventListeners();

    // Pre-fill name and email if authenticated (Blade might also do this, this is a JS fallback/override)
    if (!window.AppConfig.isAuthenticated) {
        const nameInput = contactAgentForm.querySelector('input[name="name"]');
        const emailInput = contactAgentForm.querySelector('input[name="email"]');
        if (nameInput && !nameInput.value && currentUserName) nameInput.value = currentUserName;
        if (emailInput && !emailInput.value && currentUserEmail) emailInput.value = currentUserEmail;
    }

    contactAgentForm.addEventListener('submit', async (event) => {
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
        if (!getRoute('properties.submit-report')) {
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

        const submitBtn = contactAgentForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn ? submitBtn.textContent : (translate('contact_agent_send_button_text'));
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = translate('loading_text');
        }

        // Fields to clear: API error keys (e.g., mobile for the phone input)
        // The map is used if API error key differs from input name: { 'api_key': 'input_name' }
        // Here, 'mobile' is both API key and likely input name for the raw phone.
        clearActionFormErrors(contactAgentForm, ['name', 'email', 'mobile', 'message']);

        const formData = new FormData(contactAgentForm);
        const rawPhone = formData.get('mobile'); // This should be the name of your phone input field
        let fullPhone = rawPhone; // Default to raw input if validator is not used or number is invalid but submitted

        if (phoneInputValidator) {
            if (rawPhone && rawPhone.trim() !== '' && !phoneInputValidator.validate()) {
                // `displayActionFormErrors` expects errors object: { fieldName: [message] }
                // The fieldName 'mobile' should match an input name or be mapped.
                displayActionFormErrors(contactAgentForm, { mobile: [translate('phone_invalid')] }, true);
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
            if (phoneInputValidator.getInstance()?.isValidNumber()) { // Only get number if valid to avoid errors
                fullPhone = phoneInputValidator.getInstance()?.getNumber() || rawPhone;
            } else if (!rawPhone || rawPhone.trim() === '') {
                fullPhone = ""; // Ensure empty if raw input is empty
            }
        }

        const contactData = {
            name: formData.get('name'),
            email: formData.get('email'),
            mobile: rawPhone,       // The raw phone number input by the user
            userPhoneFull: fullPhone,     // Full E.164 formatted number, or raw if validator not used/valid
            message: formData.get('message')
            // property_id: contactAgentForm.dataset.propertyId // If form needs to send property_id
        };

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
                    },
                    422: (error) => {
                        const message = error?.response?.data?.message;
                        const validationErrors = error?.response?.data?.errors;

                        let modalTitle = translate('error_title');
                        let modalBodyHtml = '';
                        let errorListForModal = '';

                        if (validationErrors && Object.keys(validationErrors).length > 0) {
                            if (displayActionFormErrors) {
                                displayActionFormErrors(contactAgentForm, validationErrors);
                            } else {
                                console.warn("[ContactAgentAction] displayActionFormErrors helper not available to show inline errors.");
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
            }).post(getRoute('properties.contact-agent'), contactData);

            const data = response.data;

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            }

            showModalMessage?.(
                'success',
                {
                    bodyHtml: data.message || translate('contact_message_sent_success'),
                    timerSeconds: 1,
                    showCloseIcon: true
                });
            contactAgentForm.reset(); // Clears form fields

            if (!window.AppConfig.isAuthenticated) { // Re-apply pre-fills if reset clears them
                const nameInput = contactAgentForm.querySelector('input[name="name"]');
                const emailInput = contactAgentForm.querySelector('input[name="email"]');
                if (nameInput && currentUserName) nameInput.value = currentUserName;
                if (emailInput && currentUserEmail) emailInput.value = currentUserEmail;
                // Phone is trickier due to validator; handled by manual re-entry.
                if (phoneInputValidator && !window.AppConfig.isAuthenticated && currentUserMobile) {
                    phoneInputValidator.getInstance()?.setNumber(currentUserMobile);
                } else if (agentContactPhoneInput && !window.AppConfig.isAuthenticated && currentUserMobile) {
                    agentContactPhoneInput.value = currentUserMobile; // Basic fallback if no validator
                } else if (phoneInputValidator) {
                    phoneInputValidator.getInstance()?.setNumber(""); // Clear for guest
                }
            }
        } catch (error) {
            console.error('[ContactAgentAction] Contact Agent API error:', error);
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

    // --- WhatsApp Link (Ensure AGENT_PHONE_NUMBER & whatsAppNumber are from config.js) ---
    const whatsappLinkV3 = document.getElementById('whatsappLinkV3');
    if (whatsappLinkV3 && typeof whatsappLinkV3 !== 'undefined' && typeof whatsAppNumber !== 'undefined') {
        let encodedMessage = contactAgentForm.querySelector('[name="message"]')?.value;
        encodedMessage = encodeURIComponent(encodedMessage);
        const cleanPhoneNumber = whatsAppNumber.replace(/\D/g, ''); // Remove non-digits
        whatsappLinkV3.href = `https://wa.me/${cleanPhoneNumber}?text=${encodedMessage}`;
    }

    // "Show Mobile Number" modal is triggered here, but its display logic is in modals.js
    // const callBtnTriggerV3 = document.querySelector('.call-btn-trigger-v3');
    // const showMobileModal = document.getElementById('showMobileModal'); // Needed to trigger open
    // if (callBtnTriggerV3 && showMobileModal) { // Check if modal itself exists before adding listener
    //     callBtnTriggerV3.addEventListener('click', () => {
    //         // The actual modal display logic is handled by openShowMobileModal in modals.js
    //         // This just ensures the button exists to trigger it.
    //         // We might need a global event bus or direct call if openShowMobileModal is not global
    //         if (typeof openShowMobileModal === 'function') { // Check if the function exists globally (defined in modals.js)
    //             openShowMobileModal();
    //         } else {
    //             console.warn('openShowMobileModal function not found. Ensure modals.js is loaded and the function is accessible.');
    //         }
    //     });
    // }
}
