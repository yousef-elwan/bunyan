
// import { clearActionFormErrors, showModalMessage, displayActionFormErrors, getRoute, translate } from '../../utils/helpers';
// import { initializePhoneInput, createPhoneInputValidator } from '../../utils/phoneInput';

// function initAuthPages() {

//     // --- DOM Element Variables ---
//     const loginForm = document.getElementById('loginForm');
//     const registerForm = document.getElementById('registerForm');
//     const forgotPasswordForm = document.querySelector('form[action*="password/email"]');
//     const resetPasswordForm = document.querySelector('form[action*="password/update"]');

//     const passwordInputs = document.querySelectorAll('.password-input');
//     const passwordToggleBtns = document.querySelectorAll('.password-toggle-btn');

//     const agreeTermsCheckbox = document.getElementById('agreeTerms');
//     const registerSubmitBtn = document.getElementById('registerSubmitBtn'); // Specific to register form
//     const mobileRegisterInput = document.getElementById('mobileRegisterInput'); // Specific to register form

//     const googleLoginBtnLogin = document.getElementById('googleLoginBtnLogin');
//     const googleLoginBtnRegister = document.getElementById('googleLoginBtnRegister');

//     // if (!window.AppConfig?.csrfToken && (loginForm || registerForm || forgotPasswordForm || resetPasswordForm)) {
//     //     console.error("[AuthPages] CSRF token not found. Auth operations will fail.");
//     //     const errorMsg = translate('csrf_error');
//     //     if (showModalMessage) {
//     //         showModalMessage('error', { title: translate('error_title'), bodyHtml: errorMsg });
//     //     } else {
//     //         alert(errorMsg);
//     //     }
//     // }

//     // --- Local Helper Functions ---
//     function toggleButtonSpinner(button, show) {
//         if (!button) return;
//         const spinner = button.querySelector('.spinner-border');
//         button.disabled = show;

//         if (spinner) {
//             spinner.classList.toggle('d-none', !show);
//         }
//         if (!show && button === registerSubmitBtn && agreeTermsCheckbox) {
//             button.disabled = !agreeTermsCheckbox.checked;
//         }
//     }

//     // --- Password Visibility Toggle ---
//     // function setupPasswordVisibilityToggle() {
//     //     if (!passwordToggleBtns || passwordToggleBtns.length === 0) return;

//     //     passwordToggleBtns.forEach(btn => {
//     //         btn.addEventListener('click', function () {
//     //             const eyeIconId = this.dataset.toggleid;
//     //             const inputField = document.querySelector(`.password-input[data-toggleid="${eyeIconId}"]`);
//     //             const eyeIcon = document.getElementById(eyeIconId);

//     //             if (inputField && eyeIcon) {
//     //                 if (inputField.type === 'password') {
//     //                     inputField.type = 'text';
//     //                     eyeIcon.classList.remove('fa-eye');
//     //                     eyeIcon.classList.add('fa-eye-slash');
//     //                     this.setAttribute('aria-label', translate('hide_password_label') || 'Hide password');
//     //                 } else {
//     //                     inputField.type = 'password';
//     //                     eyeIcon.classList.remove('fa-eye-slash');
//     //                     eyeIcon.classList.add('fa-eye');
//     //                     this.setAttribute('aria-label', translate('show_password_label') || 'Show password');
//     //                 }
//     //             } else {
//     //                 console.warn("[AuthPages] Password toggle target or icon not found for button:", this, "Expected eyeIconId:", eyeIconId);
//     //             }
//     //         });
//     //     });
//     // }
//     // setupPasswordVisibilityToggle();

//     // --- Phone Input (intl-tel-input) Specific Logic (for Register Page) ---

//     const itiInstance = initializePhoneInput(mobileRegisterInput);
//     const phoneInputValidator = createPhoneInputValidator(
//         itiInstance,
//         mobileRegisterInput,
//         registerForm.querySelector('.invalid-feedback[data-field="mobile"]'),
//         translate
//     );
//     phoneInputValidator.setupEventListeners();

//     // --- AJAX Form Submission Logic ---
//     async function handleAuthFormSubmit(formElement, event) {
//         event.preventDefault();
//         // if (!window.AppConfig?.csrfToken) {
//         //     console.error("[AuthPages] Submission attempt without CSRF token.");
//         //     if (showModalMessage) showModalMessage('error', { bodyHtml: translate('csrf_error'), title: translate('error_title') });
//         //     else alert(translate('csrf_error'));
//         //     return;
//         // }

//         if (clearActionFormErrors) clearActionFormErrors(formElement);
//         else { // Basic fallback for clearing errors
//             formElement.querySelectorAll('.invalid-feedback').forEach(span => span.textContent = '');
//             formElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
//         }

//         const submitButton = formElement.querySelector('button[type="submit"].submit-btn');
//         toggleButtonSpinner(submitButton, true);
//         const formData = new FormData(formElement);

//         if (formElement === registerForm && mobileRegisterInput) {
//             const iti = phoneInputValidator ? phoneInputValidator.getInstance() : null;
//             const hiddenInputName = mobileRegisterInput.dataset.hiddenInputName || 'mobile';
//             if (iti && mobileRegisterInput.value.trim() !== '') {
//                 if (iti.isValidNumber()) {
//                     formData.set(hiddenInputName, iti.getNumber());
//                     formData.set('country_code', iti.getSelectedCountryData().dialCode);
//                 } else {
//                     console.warn("[AuthPages] Register form: Phone number might be invalid despite pre-check.");
//                 }
//             } else if (mobileRegisterInput.value.trim() === '') {
//                 formData.delete(hiddenInputName);
//                 formData.delete('country_code');
//             }
//         }

//         try {
//             const response = await fetch(formElement.action, {
//                 method: 'POST',
//                 headers: {
//                     'X-CSRF-TOKEN': window.AppConfig.csrfToken,
//                     'Accept': 'application/json',
//                     'X-Requested-With': 'XMLHttpRequest'
//                 },
//                 body: formData
//             });
//             const data = await response.json();

//             if (response.ok && data.success === true) {
//                 if (showModalMessage) {
//                     showModalMessage('success', {
//                         title: data.title || translate('success_title'),
//                         bodyHtml: data.message || translate('operation_successful'),
//                         timerSeconds: data.redirect || data.data?.redirect ? 2 : 3,
//                         onClose: () => {
//                             const redirectUrl = data.redirect || data.data?.redirect;
//                             if (redirectUrl) window.location.href = redirectUrl;
//                             else if (formElement === loginForm || formElement === registerForm) window.location.href = getRoute('home') || '/';
//                             else if (formElement === forgotPasswordForm) { /* Stay on page */ }
//                             else window.location.reload();
//                         }
//                     });
//                 } else {
//                     alert(data.message || translate('success'));
//                     const redirectUrl = data.redirect || data.data?.redirect;
//                     if (redirectUrl) window.location.href = redirectUrl;
//                     else if (formElement === loginForm || formElement === registerForm) window.location.href = getRoute('home') || '/';
//                     else window.location.reload();
//                 }
//                 if (formElement === forgotPasswordForm) {
//                     formElement.reset();
//                 }
//             } else {
//                 let modalTitle = data.title || translate('error_title');
//                 let modalBodyHtml = '';

//                 if (response.status === 422 && data.errors && Object.keys(data.errors).length > 0) {
//                     if (displayActionFormErrors) displayActionFormErrors(formElement, data.errors);
//                     else { // Basic fallback
//                         for (const field in data.errors) {
//                             const errInput = formElement.querySelector(`[name="${field}"]`);
//                             const errSpan = formElement.querySelector(`.invalid-feedback[data-field="${field}"]`);
//                             if (errInput) errInput.classList.add('is-invalid');
//                             if (errSpan) errSpan.textContent = data.errors[field][0];
//                         }
//                     }
//                     modalTitle = translate('validation_error_title');
//                     let errorListForModal = `<ul style="text-align: ${window.AppConfig?.isRTL ? 'right' : 'left'}; list-style-position: inside; padding-${window.AppConfig?.isRTL ? 'right' : 'left'}: 0; margin:0;">`;
//                     for (const field in data.errors) {
//                         data.errors[field].forEach(errMsg => errorListForModal += `<li style="margin-bottom: 5px;">${errMsg}</li>`);
//                     }
//                     errorListForModal += '</ul>';
//                     const summaryMsg = (data.message && data.message !== translate('validation_error_summary')) ? data.message : translate('validation_error_summary');
//                     modalBodyHtml = summaryMsg + (errorListForModal ? '<br>' + errorListForModal : '');
//                 } else if (data.message) {
//                     modalBodyHtml = data.message;
//                 } else {
//                     modalBodyHtml = translate('generic_error_try_again');
//                 }

//                 if (showModalMessage) {
//                     showModalMessage('error', {
//                         title: modalTitle, bodyHtml: modalBodyHtml,
//                         buttons: [{ text: translate('close'), class: 'my-modal-btn-primary' }],
//                         showCloseIcon: true
//                     });
//                 } else {
//                     alert(modalBodyHtml.replace(/<br>/g, '\n').replace(/<li>/g, '\n- ').replace(/<\/li>|<\/ul>|<ul>/g, ''));
//                 }
//             }
//         } catch (error) {
//             console.error('[AuthPages] Form submission fetch/network error:', error);
//             const networkErrorMsg = translate('network_error');
//             if (showModalMessage) {
//                 showModalMessage('error', { title: translate('error_title'), bodyHtml: networkErrorMsg });
//             } else {
//                 alert(networkErrorMsg);
//             }
//         } finally {
//             toggleButtonSpinner(submitButton, false);
//         }
//     }

//     function setupFormSubmissions() {
//         [loginForm, registerForm, forgotPasswordForm, resetPasswordForm].forEach(formElement => {
//             if (formElement) {
//                 formElement.addEventListener('submit', (e) => {
//                     if (formElement === registerForm) {
//                         let canSubmit = true;
//                         const errorsToDisplayInline = {};
//                         const summaryErrorMessages = [];

//                         if (clearActionFormErrors) clearActionFormErrors(registerForm);
//                         else { // Basic fallback
//                             registerForm.querySelectorAll('.invalid-feedback').forEach(span => span.textContent = '');
//                             registerForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
//                         }


//                         if (agreeTermsCheckbox && !agreeTermsCheckbox.checked) {
//                             const msg = translate('agree_to_terms_required');
//                             errorsToDisplayInline.agreeTerms = [msg];
//                             summaryErrorMessages.push(msg);
//                             canSubmit = false;
//                         }

//                         if (mobileRegisterInput && mobileRegisterInput.value.trim() !== '') {
//                             if (!phoneInputValidator.validate()) {
//                                 const msg = translate('phone_invalid');
//                                 const phoneFieldKey = mobileRegisterInput.dataset.hiddenInputName || 'mobile';
//                                 errorsToDisplayInline[phoneFieldKey] = [msg];
//                                 summaryErrorMessages.push(msg);
//                                 canSubmit = false;
//                             }
//                         }

//                         if (!canSubmit) {
//                             e.preventDefault();
//                             if (displayActionFormErrors && Object.keys(errorsToDisplayInline).length > 0) {
//                                 displayActionFormErrors(registerForm, errorsToDisplayInline);
//                             } else if (Object.keys(errorsToDisplayInline).length > 0) { // Basic fallback
//                                 for (const field in errorsToDisplayInline) {
//                                     const errInput = registerForm.querySelector(`[name="${field}"]`);
//                                     const errSpan = registerForm.querySelector(`.invalid-feedback[data-field="${field}"]`);
//                                     if (errInput) errInput.classList.add('is-invalid');
//                                     if (errSpan) errSpan.textContent = errorsToDisplayInline[field][0];
//                                 }
//                             }

//                             if (showModalMessage && summaryErrorMessages.length > 0) {
//                                 showModalMessage('error', {
//                                     title: translate('validation_error_title'),
//                                     bodyHtml: summaryErrorMessages.join('<br>')
//                                 });
//                             } else if (summaryErrorMessages.length > 0) {
//                                 alert(summaryErrorMessages.join('\n'));
//                             }
//                             if (errorsToDisplayInline.agreeTerms) agreeTermsCheckbox?.focus();
//                             else if (Object.keys(errorsToDisplayInline).some(k => k.includes('mobile'))) mobileRegisterInput?.focus();
//                             return;
//                         }
//                     }
//                     handleAuthFormSubmit(formElement, e);
//                 });
//             }
//         });
//     }
//     setupFormSubmissions();


//     // --- Other UI Logic ---
//     const otherUIModule = (() => {
//         // registerForm, agreeTermsCheckbox, registerSubmitBtn,
//         // googleLoginBtnLogin, googleLoginBtnRegister are in outer scope

//         function setupTermsCheckboxListener() {
//             if (agreeTermsCheckbox && registerSubmitBtn && registerForm) {
//                 const updateSubmitButtonState = () => {
//                     registerSubmitBtn.disabled = !agreeTermsCheckbox.checked;
//                     if (agreeTermsCheckbox.checked) {
//                         if (clearActionFormErrors) clearActionFormErrors(registerForm, ['agreeTerms']);
//                         else { // Basic fallback
//                             const errorSpan = registerForm.querySelector('.invalid-feedback[data-field="agreeTerms"]');
//                             if (errorSpan) errorSpan.textContent = '';
//                             agreeTermsCheckbox.classList.remove('is-invalid');
//                         }
//                     }
//                 };
//                 agreeTermsCheckbox.addEventListener('change', updateSubmitButtonState);
//                 updateSubmitButtonState();
//             }
//         }

//         function updateGoogleButtonTextAndSetupPlaceholders() {
//             const buttons = [
//                 { el: googleLoginBtnLogin, type: 'login', tKeyBase: 'login_with_google' },
//                 { el: googleLoginBtnRegister, type: 'register', tKeyBase: 'login_with_google' }
//             ];

//             buttons.forEach(item => {
//                 if (item.el) {
//                     const textSpan = item.el.querySelector('.btn-text-content');
//                     if (textSpan) {
//                         const textKey = `${item.tKeyBase}_${item.type}_page`;
//                         const defaultText = (item.type === 'login') ? "Sign in with Google" : "Sign up with Google";
//                         textSpan.textContent = translate(textKey) || defaultText;
//                     }

//                     item.el.addEventListener('click', (e) => {
//                         e.preventDefault();
//                         const titleKey = `google_${item.type}_title_${item.type}_page`;
//                         const defaultTitle = (item.type === 'login') ? "Sign in with Google" : "Sign up with Google";
//                         const title = translate(titleKey) || defaultTitle;
//                         const message = translate('google_oauth_placeholder');

//                         if (showModalMessage) {
//                             showModalMessage('info', { title: title, bodyHtml: message });
//                         } else { alert(message); }
//                     });
//                 }
//             });
//         }

//         function initialize() {
//             if (registerForm) setupTermsCheckboxListener(); // Needs registerForm context
//             updateGoogleButtonTextAndSetupPlaceholders();
//         }
//         return { initialize };
//     })();

//     otherUIModule.initialize();

//     console.log('[AuthPages] Initialization COMPLETE.');
// }

// // --- Run the Initializer ---
// initAuthPages();


// // --- AJAX Form Submission Logic ---
// async function handleAuthFormSubmit(formElement, event) {
//     event.preventDefault();
//     // if (!window.AppConfig?.csrfToken) {
//     //     console.error("[AuthPages] Submission attempt without CSRF token.");
//     //     if (showModalMessage) showModalMessage('error', { bodyHtml: translate('csrf_error'), title: translate('error_title') });
//     //     else alert(translate('csrf_error'));
//     //     return;
//     // }

//     if (clearActionFormErrors) clearActionFormErrors(formElement);
//     else { // Basic fallback for clearing errors
//         formElement.querySelectorAll('.invalid-feedback').forEach(span => span.textContent = '');
//         formElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
//     }

//     const submitButton = formElement.querySelector('button[type="submit"].submit-btn');
//     toggleButtonSpinner(submitButton, true);
//     const formData = new FormData(formElement);

//     if (formElement === registerForm && mobileRegisterInput) {
//         const iti = phoneInputModule.getInstance();
//         const hiddenInputName = mobileRegisterInput.dataset.hiddenInputName || 'mobile';
//         if (iti && mobileRegisterInput.value.trim() !== '') {
//             if (iti.isValidNumber()) {
//                 formData.set(hiddenInputName, iti.getNumber());
//                 formData.set('country_code', iti.getSelectedCountryData().dialCode);
//             } else {
//                 console.warn("[AuthPages] Register form: Phone number might be invalid despite pre-check.");
//             }
//         } else if (mobileRegisterInput.value.trim() === '') {
//             formData.delete(hiddenInputName);
//             formData.delete('country_code');
//         }
//     }

//     try {
//         const response = await fetch(formElement.action, {
//             method: 'POST',
//             headers: {
//                 'X-CSRF-TOKEN': window.AppConfig.csrfToken,
//                 'Accept': 'application/json',
//                 'X-Requested-With': 'XMLHttpRequest'
//             },
//             body: formData
//         });
//         const data = await response.json();

//         if (response.ok && data.success === true) {
//             if (showModalMessage) {
//                 showModalMessage('success', {
//                     title: data.title || translate('success_title'),
//                     bodyHtml: data.message || translate('operation_successful'),
//                     timerSeconds: data.redirect || data.data?.redirect ? 2 : 3,
//                     onClose: () => {
//                         const redirectUrl = data.redirect || data.data?.redirect;
//                         if (redirectUrl) window.location.href = redirectUrl;
//                         else if (formElement === loginForm || formElement === registerForm) window.location.href = getRoute('home') || '/';
//                         else if (formElement === forgotPasswordForm) { /* Stay on page */ }
//                         else window.location.reload();
//                     }
//                 });
//             } else {
//                 alert(data.message || translate('success'));
//                 const redirectUrl = data.redirect || data.data?.redirect;
//                 if (redirectUrl) window.location.href = redirectUrl;
//                 else if (formElement === loginForm || formElement === registerForm) window.location.href = getRoute('home') || '/';
//                 else window.location.reload();
//             }
//             if (formElement === forgotPasswordForm) {
//                 formElement.reset();
//             }
//         } else {
//             let modalTitle = data.title || translate('error_title');
//             let modalBodyHtml = '';

//             if (response.status === 422 && data.errors && Object.keys(data.errors).length > 0) {
//                 if (displayActionFormErrors) displayActionFormErrors(formElement, data.errors);
//                 else { // Basic fallback
//                     for (const field in data.errors) {
//                         const errInput = formElement.querySelector(`[name="${field}"]`);
//                         const errSpan = formElement.querySelector(`.invalid-feedback[data-field="${field}"]`);
//                         if (errInput) errInput.classList.add('is-invalid');
//                         if (errSpan) errSpan.textContent = data.errors[field][0];
//                     }
//                 }
//                 modalTitle = translate('validation_error_title');
//                 let errorListForModal = `<ul style="text-align: ${window.AppConfig?.isRTL ? 'right' : 'left'}; list-style-position: inside; padding-${window.AppConfig?.isRTL ? 'right' : 'left'}: 0; margin:0;">`;
//                 for (const field in data.errors) {
//                     data.errors[field].forEach(errMsg => errorListForModal += `<li style="margin-bottom: 5px;">${errMsg}</li>`);
//                 }
//                 errorListForModal += '</ul>';
//                 const summaryMsg = (data.message && data.message !== translate('validation_error_summary')) ? data.message : translate('validation_error_summary');
//                 modalBodyHtml = summaryMsg + (errorListForModal ? '<br>' + errorListForModal : '');
//             } else if (data.message) {
//                 modalBodyHtml = data.message;
//             } else {
//                 modalBodyHtml = translate('generic_error_try_again');
//             }

//             if (showModalMessage) {
//                 showModalMessage('error', {
//                     title: modalTitle, bodyHtml: modalBodyHtml,
//                     buttons: [{ text: translate('close'), class: 'my-modal-btn-primary' }],
//                     showCloseIcon: true
//                 });
//             } else {
//                 alert(modalBodyHtml.replace(/<br>/g, '\n').replace(/<li>/g, '\n- ').replace(/<\/li>|<\/ul>|<ul>/g, ''));
//             }
//         }
//     } catch (error) {
//         console.error('[AuthPages] Form submission fetch/network error:', error);
//         const networkErrorMsg = translate('network_error');
//         if (showModalMessage) {
//             showModalMessage('error', { title: translate('error_title'), bodyHtml: networkErrorMsg });
//         } else {
//             alert(networkErrorMsg);
//         }
//     } finally {
//         toggleButtonSpinner(submitButton, false);
//     }
// }

// function setupFormSubmissions() {
//     [loginForm, registerForm, forgotPasswordForm, resetPasswordForm].forEach(formElement => {
//         if (formElement) {
//             formElement.addEventListener('submit', (e) => {
//                 if (formElement === registerForm) {
//                     let canSubmit = true;
//                     const errorsToDisplayInline = {};
//                     const summaryErrorMessages = [];

//                     if (clearActionFormErrors) clearActionFormErrors(registerForm);
//                     else { // Basic fallback
//                         registerForm.querySelectorAll('.invalid-feedback').forEach(span => span.textContent = '');
//                         registerForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
//                     }


//                     if (agreeTermsCheckbox && !agreeTermsCheckbox.checked) {
//                         const msg = translate('agree_to_terms_required');
//                         errorsToDisplayInline.agreeTerms = [msg];
//                         summaryErrorMessages.push(msg);
//                         canSubmit = false;
//                     }

//                     if (mobileRegisterInput && mobileRegisterInput.value.trim() !== '') {
//                         if (!phoneInputModule.validate()) {
//                             const msg = translate('phone_invalid');
//                             const phoneFieldKey = mobileRegisterInput.dataset.hiddenInputName || 'mobile';
//                             errorsToDisplayInline[phoneFieldKey] = [msg];
//                             summaryErrorMessages.push(msg);
//                             canSubmit = false;
//                         }
//                     }

//                     if (!canSubmit) {
//                         e.preventDefault();
//                         if (displayActionFormErrors && Object.keys(errorsToDisplayInline).length > 0) {
//                             displayActionFormErrors(registerForm, errorsToDisplayInline);
//                         } else if (Object.keys(errorsToDisplayInline).length > 0) { // Basic fallback
//                             for (const field in errorsToDisplayInline) {
//                                 const errInput = registerForm.querySelector(`[name="${field}"]`);
//                                 const errSpan = registerForm.querySelector(`.invalid-feedback[data-field="${field}"]`);
//                                 if (errInput) errInput.classList.add('is-invalid');
//                                 if (errSpan) errSpan.textContent = errorsToDisplayInline[field][0];
//                             }
//                         }

//                         if (showModalMessage && summaryErrorMessages.length > 0) {
//                             showModalMessage('error', {
//                                 title: translate('validation_error_title'),
//                                 bodyHtml: summaryErrorMessages.join('<br>')
//                             });
//                         } else if (summaryErrorMessages.length > 0) {
//                             alert(summaryErrorMessages.join('\n'));
//                         }
//                         if (errorsToDisplayInline.agreeTerms) agreeTermsCheckbox?.focus();
//                         else if (Object.keys(errorsToDisplayInline).some(k => k.includes('mobile'))) mobileRegisterInput?.focus();
//                         return;
//                     }
//                 }
//                 handleAuthFormSubmit(formElement, e);
//             });
//         }
//     });
// }
// setupFormSubmissions();


// // --- Other UI Logic ---
// const otherUIModule = (() => {
//     // registerForm, agreeTermsCheckbox, registerSubmitBtn,
//     // googleLoginBtnLogin, googleLoginBtnRegister are in outer scope

//     function setupTermsCheckboxListener() {
//         if (agreeTermsCheckbox && registerSubmitBtn && registerForm) {
//             const updateSubmitButtonState = () => {
//                 registerSubmitBtn.disabled = !agreeTermsCheckbox.checked;
//                 if (agreeTermsCheckbox.checked) {
//                     if (clearActionFormErrors) clearActionFormErrors(registerForm, ['agreeTerms']);
//                     else { // Basic fallback
//                         const errorSpan = registerForm.querySelector('.invalid-feedback[data-field="agreeTerms"]');
//                         if (errorSpan) errorSpan.textContent = '';
//                         agreeTermsCheckbox.classList.remove('is-invalid');
//                     }
//                 }
//             };
//             agreeTermsCheckbox.addEventListener('change', updateSubmitButtonState);
//             updateSubmitButtonState();
//         }
//     }

//     function updateGoogleButtonTextAndSetupPlaceholders() {
//         const buttons = [
//             { el: googleLoginBtnLogin, type: 'login', tKeyBase: 'login_with_google' },
//             { el: googleLoginBtnRegister, type: 'register', tKeyBase: 'login_with_google' }
//         ];

//         buttons.forEach(item => {
//             if (item.el) {
//                 const textSpan = item.el.querySelector('.btn-text-content');
//                 if (textSpan) {
//                     const textKey = `${item.tKeyBase}_${item.type}_page`;
//                     const defaultText = (item.type === 'login') ? "Sign in with Google" : "Sign up with Google";
//                     textSpan.textContent = translate(textKey) || defaultText;
//                 }

//                 item.el.addEventListener('click', (e) => {
//                     e.preventDefault();
//                     const titleKey = `google_${item.type}_title_${item.type}_page`;
//                     const defaultTitle = (item.type === 'login') ? "Sign in with Google" : "Sign up with Google";
//                     const title = translate(titleKey) || defaultTitle;
//                     const message = translate('google_oauth_placeholder');

//                     if (showModalMessage) {
//                         showModalMessage('info', { title: title, bodyHtml: message });
//                     } else { alert(message); }
//                 });
//             }
//         });
//     }

//     function initialize() {
//         if (registerForm) setupTermsCheckboxListener(); // Needs registerForm context
//         updateGoogleButtonTextAndSetupPlaceholders();
//     }
//     return { initialize };
// })();

// otherUIModule.initialize();

// console.log('[AuthPages] Initialization COMPLETE.');

// // --- Run the Initializer ---
// initAuthPages();
