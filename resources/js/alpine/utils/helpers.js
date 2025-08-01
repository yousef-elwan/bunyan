import { MyGlobalModal } from './my-modal';

export function debounce(fn, delay = 300) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

export function getRoute(routeName, params = {}) {
    let url = window.AppConfig.routes[routeName] || '#';
    if (url === '#') {
        console.warn(`Route "${routeName}" not found in AppConfig.routes.`);
        return url;
    }
    for (const paramName in params) {
        const placeholderRegex = new RegExp(`:${paramName}(Placeholder)?|\\{${paramName}\\}`, 'g');
        url = url.replace(placeholderRegex, params[paramName]);
    }
    return url;
};

export function translate(key, replacements = {}) {
    let keys = key.split('.');
    let translation = window.AppConfig.i18n;
    for (let i = 0; i < keys.length; i++) {
        if (translation && typeof translation === 'object' && translation.hasOwnProperty(keys[i])) {
            translation = translation[keys[i]];
        } else {
            return key;
        }
    }
    if (typeof translation === 'string') {
        for (let placeholder in replacements) {
            translation = translation.replace(`:${placeholder}`, replacements[placeholder]);
        }
    }
    return translation;
};

export function showAuthRequired() {

    const myGlobalModal = new MyGlobalModal();

    const authTitle = translate('auth_required_title');
    const authMessage = translate('auth_required_message');
    const loginText = translate('login_button_text');
    const cancelText = translate('cancel_button_text');

    if (myGlobalModal && typeof myGlobalModal.show === 'function') {
        const buttons = [{ text: cancelText, class: 'my-modal-btn-outline' }];
        if (typeof window.openAuthDialog === 'function') {
            buttons.unshift({ text: loginText, class: 'my-modal-btn-primary', onClick: () => { myGlobalModal.close(); window.openAuthDialog(); } });
        } else if (getRoute('auth.login')) {
            buttons.unshift({ text: loginText, class: 'my-modal-btn-primary', onClick: () => { window.location.href = getRoute('auth.login'); } });
        }
        myGlobalModal.show({ title: authTitle, bodyHtml: authMessage, buttons: buttons });
    } else {
        if (confirm(authMessage + " " + translate('go_to_login_page') + translate('?'))) {
            if (getRoute('auth.login')) window.location.href = getRoute('auth.login');
            else console.warn('[Helpers] Login URL not configured.');
        }
    }
}

/**
 * Displays a generic message modal.
 * Uses `myGlobalModal` if available, otherwise falls back to `alert()`.
 * @param {('success'|'error'|'info'|'warning')} type - The type of message (e.g., 'success', 'error').
 * @param {string} message - The main message content.
 * @param {string} [title=null] - Optional title for the modal. If null, a default based on type is used.
 * @param {number} [timer=null] - Optional. If type is 'success', defaults to 3000ms. Otherwise null.
 * Relies on `window.JS_TRANSLATIONS` for localized text.
 */
export function showModalMessage(
    type,
    options
    // timer = null,
    // buttons = []
) {
    const myGlobalModal = new MyGlobalModal();

    let defaultTitle;
    switch (type) {
        case 'success':
            defaultTitle = translate('success_title');
            if (options.timer === null) options.timer = 3000; // Default timer for success
            break;
        case 'error':
            defaultTitle = translate('error_title');
            break;
        case 'info':
            defaultTitle = translate('info_title');
            break;
        case 'warning':
            defaultTitle = translate('warning_title');
            break;
        default:
            defaultTitle = translate('message');
    }
    options.title = options.title || defaultTitle;

    if (myGlobalModal && typeof myGlobalModal.show === 'function') {
        myGlobalModal.show(options);
    } else {
        alert(`${options.title}\n${options.bodyHtml || ''}`);
    }
}

/**
 * Clears validation errors from a form, prioritizing specific structures.
 * @param {HTMLFormElement} formElement - The form element.
 * @param {string[]} [fieldsToClear=[]] - An array of API error keys whose errors should be cleared.
 * @param {Object.<string, string>} [apiErrorKeyToInputNameMap={}] - Map API error keys to actual input `name` attributes.
 */
export function clearActionFormErrors(formElement, fieldsToClear = [], apiErrorKeyToInputNameMap = {}) {
    if (!formElement) return;

    const clearSpecificField = (apiErrorKey) => {
        const inputName = apiErrorKeyToInputNameMap[apiErrorKey] || apiErrorKey;
        const inputElement = formElement.querySelector(`[name="${inputName}"]`);

        if (inputElement) {
            inputElement.classList.remove('is-invalid');
            if (inputElement.type === 'tel' && inputElement.classList.contains('phone-input')) {
                inputElement.classList.remove('iti-invalid');
            }
        }

        // Target .invalid-feedback spans with data-field (used in auth-modal)
        const invalidFeedbackEl = formElement.querySelector(`.invalid-feedback[data-field="${apiErrorKey}"]`);
        if (invalidFeedbackEl) {
            invalidFeedbackEl.textContent = '';
            invalidFeedbackEl.style.display = 'none'; // Explicitly hide
        }

        // Target .form-error-message spans with data-field (used in propertyActions)
        const formErrorMsgEl = formElement.querySelector(`.form-error-message[data-field="${apiErrorKey}"]`);
        if (formErrorMsgEl) {
            formErrorMsgEl.textContent = '';
        }

        // Clear sidebar/fallback messages if inputElement and form-group-v3 exist (propertyActions)
        if (inputElement) {
            const formGroupV3 = inputElement.closest('.form-group-v3');
            if (formGroupV3) {
                formGroupV3.querySelectorAll('.form-error-message-sidebar, .form-error-message-fallback').forEach(el => el.remove());
            }
        }
    };

    if (fieldsToClear.length > 0) {
        fieldsToClear.forEach(apiErrorKey => clearSpecificField(apiErrorKey));
    } else { // Clear all errors in the form
        formElement.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            if (el.type === 'tel' && el.classList.contains('phone-input')) {
                el.classList.remove('iti-invalid');
            }
        });
        formElement.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        formElement.querySelectorAll('.form-error-message').forEach(el => {
            el.textContent = '';
        });
        formElement.querySelectorAll('.form-error-message-sidebar, .form-error-message-fallback').forEach(el => {
            el.remove();
        });
    }
}

/**
 * Displays validation errors on a form, prioritizing specific structures.
 * @param {HTMLFormElement} formElement - The form element.
 * @param {Object} errors - An object where keys are API error keys and values are error messages.
 * @param {boolean} [isPropertyActionsSidebarForm=false] - Specific flag for propertyActions sidebar styling.
 * @param {Object.<string, string>} [apiErrorKeyToInputNameMap={}] - Map API error keys to actual input `name` attributes.
 */
export function displayActionFormErrors(formElement, errors, isPropertyActionsSidebarForm = false, apiErrorKeyToInputNameMap = {}) {
    if (!formElement || !errors || Object.keys(errors).length === 0) return;

    clearActionFormErrors(formElement, Object.keys(errors), apiErrorKeyToInputNameMap);

    for (const apiErrorKey in errors) {
        const inputName = apiErrorKeyToInputNameMap[apiErrorKey] || apiErrorKey;
        const inputElement = formElement.querySelector(`[name="${inputName}"]`);
        let errorDisplayed = false;

        if (inputElement) {
            inputElement.classList.add('is-invalid');
            if (inputElement.type === 'tel' && inputElement.classList.contains('phone-input') &&
                (apiErrorKey === 'mobile' || apiErrorKey === 'mobile_raw' || apiErrorKey === inputName)) {
                inputElement.classList.add('iti-invalid');
            }
        }

        // 1. Try to display in .invalid-feedback[data-field="apiErrorKey"] (Auth Modal style)
        const invalidFeedbackEl = formElement.querySelector(`.invalid-feedback[data-field="${apiErrorKey}"]`);
        if (invalidFeedbackEl) {
            invalidFeedbackEl.textContent = Array.isArray(errors[apiErrorKey]) ? errors[apiErrorKey][0] : errors[apiErrorKey];
            invalidFeedbackEl.style.display = 'block';
            errorDisplayed = true;
        }

        // 2. If not displayed above, try .form-error-message[data-field="apiErrorKey"] (Property Actions main form style)
        if (!errorDisplayed && !isPropertyActionsSidebarForm) {
            const formErrorMsgEl = formElement.querySelector(`.form-error-message[data-field="${apiErrorKey}"]`);
            if (formErrorMsgEl) {
                formErrorMsgEl.textContent = Array.isArray(errors[apiErrorKey]) ? errors[apiErrorKey][0] : errors[apiErrorKey];
                errorDisplayed = true;
            }
        }

        // 3. If still not displayed AND it's a propertyActions sidebar form OR inputElement exists (general fallback for propertyActions)
        if (!errorDisplayed && inputElement) {
            const formGroupV3 = inputElement.closest('.form-group-v3');
            if (formGroupV3) { // This block is primarily for propertyActions forms
                let newErrorDisplayContainer = formGroupV3.querySelector(isPropertyActionsSidebarForm ? '.form-error-message-sidebar' : '.form-error-message-fallback');
                if (!newErrorDisplayContainer) {
                    newErrorDisplayContainer = document.createElement('div');
                    newErrorDisplayContainer.className = isPropertyActionsSidebarForm ?
                        'form-error-message-sidebar text-danger small pt-1' :
                        'form-error-message-fallback text-danger d-block small py-1';
                    const inputWrapper = inputElement.closest('.input-group') || inputElement; // input-group is from auth-modal
                    inputWrapper.parentNode.insertBefore(newErrorDisplayContainer, inputWrapper.nextSibling);
                }
                if (newErrorDisplayContainer) {
                    newErrorDisplayContainer.textContent = Array.isArray(errors[apiErrorKey]) ? errors[apiErrorKey][0] : errors[apiErrorKey];
                    errorDisplayed = true;
                }
            }
        }
    }
}
