import { clearActionFormErrors, showModalMessage, displayActionFormErrors, getRoute, translate } from '../../utils/helpers.js';
import { initializePhoneInput, createPhoneInputValidator } from '../../utils/phoneInput.js';
import { PasswordInputManager } from '../../utils/passwordManager.js';
import { http } from '../../utils/api.js';

export function initAuthModal() {
    // --- Element Selectors ---
    const openAuthDialogBtn = document.getElementById('openAuthDialogBtn');
    const closeAuthDialogBtn = document.getElementById('closeAuthDialogBtn');
    const authDialogOverlay = document.getElementById('authDialogOverlay');
    if (!authDialogOverlay) return;

    // Selector for the content area to be disabled during load
    const authDialogContent = authDialogOverlay.querySelector('.auth-dialog-content');

    const loginFormWrapper = document.querySelector('.login-form');
    const registerFormWrapper = document.querySelector('.register-form');
    const forgotPasswordFormWrapper = document.querySelector('.forgot-password-form');

    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');

    const agreeTermsCheckbox = document.getElementById('agreeTerms');
    const registerSubmitBtn = document.getElementById('registerSubmitBtn');
    const mobileRegisterInput = document.getElementById('mobileRegisterInput');
    const logoutButton = document.getElementById('logoutButton');

    const switchToRegisterLink = document.querySelector('.toggle-to-register');
    const switchToLoginLinks = document.querySelectorAll('.toggle-to-login');
    const switchToForgotLink = document.querySelector('.toggle-to-forgot');

    // Password Generator Elements
    const passwordGenerateBtn = document.getElementById('passwordGenerateBtn');
    const passwordGeneratorModal = document.getElementById('passwordGeneratorModal');
    const passwordModalCloseBtn = document.getElementById('passwordModalCloseBtn');
    const regeneratePasswordBtn = document.getElementById('regeneratePasswordBtn');
    const generatedPasswordInput = document.getElementById('generatedPasswordInput');
    const copyPasswordBtn = document.getElementById('copyPasswordBtn');
    const copyBtnText = document.getElementById('copyBtnText');
    const passwordSavedCheckbox = document.getElementById('passwordSavedCheckbox');
    const registerPasswordInput = document.getElementById('registerPasswordInput');
    const registerConfirmPasswordInput = document.getElementById('registerConfirmPasswordInput');

    // --- Phone Input Initialization ---
    let phoneInputValidator = null;
    if (mobileRegisterInput && typeof initializePhoneInput === 'function') {
        const itiInstance = initializePhoneInput(mobileRegisterInput);
        phoneInputValidator = createPhoneInputValidator(
            itiInstance,
            mobileRegisterInput,
            registerForm.querySelector('.invalid-feedback[data-field="mobile"]'),
            translate
        );
        phoneInputValidator.setupEventListeners();
    }

    // --- Loading State Management ---
    function setDialogLoadingState(isLoading) {
        if (authDialogContent) {
            authDialogContent.classList.toggle('loading-active', isLoading);
        }
        if (closeAuthDialogBtn) {
            closeAuthDialogBtn.disabled = isLoading;
        }
    }

    function toggleButtonSpinner(button, show) {
        if (!button) return;
        const spinner = button.querySelector('.spinner-border');
        button.disabled = show;
        if (spinner) {
            spinner.classList.toggle('d-none', !show);
        }
    }

    // --- Helper to clear all form errors ---
    function clearAllFormsErrors() {
        [loginForm, registerForm, forgotPasswordForm].forEach(form => {
            if (form && typeof clearActionFormErrors === 'function') {
                clearActionFormErrors(form);
            }
        });
        if (phoneInputValidator) {
            phoneInputValidator.resetValidation();
        }
    }

    // --- Auth Dialog (Modal) Visibility & Form Switching Logic ---
    const authDialogModule = (() => {
        const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

        function showLogin() {
            clearAllFormsErrors();
            if (loginFormWrapper) loginFormWrapper.classList.add('active-form');
            if (registerFormWrapper) registerFormWrapper.classList.remove('active-form');
            if (forgotPasswordFormWrapper) forgotPasswordFormWrapper.classList.remove('active-form');

            if (loginFormWrapper) loginFormWrapper.style.transform = 'translateX(0)';
            if (registerFormWrapper) registerFormWrapper.style.transform = `translateX(${isRTL ? '-' : ''}100%)`;
            if (forgotPasswordFormWrapper) forgotPasswordFormWrapper.style.transform = `translateX(${isRTL ? '-' : ''}100%)`;
        }

        function showRegister() {
            clearAllFormsErrors();
            if (registerFormWrapper) registerFormWrapper.classList.add('active-form');
            if (loginFormWrapper) loginFormWrapper.classList.remove('active-form');
            if (forgotPasswordFormWrapper) forgotPasswordFormWrapper.classList.remove('active-form');

            if (registerFormWrapper) registerFormWrapper.style.transform = 'translateX(0)';
            if (loginFormWrapper) loginFormWrapper.style.transform = `translateX(${isRTL ? '' : '-'}100%)`;
        }

        function showForgotPassword() {
            clearAllFormsErrors();
            if (forgotPasswordFormWrapper) forgotPasswordFormWrapper.classList.add('active-form');
            if (loginFormWrapper) loginFormWrapper.classList.remove('active-form');
            if (registerFormWrapper) registerFormWrapper.classList.remove('active-form');

            if (forgotPasswordFormWrapper) forgotPasswordFormWrapper.style.transform = 'translateX(0)';
            if (loginFormWrapper) loginFormWrapper.style.transform = `translateX(${isRTL ? '' : '-'}100%)`;
        }

        function open() {
            if (authDialogOverlay) authDialogOverlay.classList.add('active');
            showLogin();
        }

        function close() {
            if (authDialogOverlay) authDialogOverlay.classList.remove('active');
        }

        function setupEventListeners() {
            if (openAuthDialogBtn) openAuthDialogBtn.addEventListener('click', open);
            if (closeAuthDialogBtn) closeAuthDialogBtn.addEventListener('click', close);
            if (switchToRegisterLink) switchToRegisterLink.addEventListener('click', (e) => { e.preventDefault(); showRegister(); });
            if (switchToForgotLink) switchToForgotLink.addEventListener('click', (e) => { e.preventDefault(); showForgotPassword(); });
            switchToLoginLinks.forEach(link => {
                link.addEventListener('click', (e) => { e.preventDefault(); showLogin(); });
            });
        }
        return { open, close, setupEventListeners };
    })();


    // --- Password Generator Logic ---
    const passwordGeneratorModule = (() => {
        if (!passwordGeneratorModal) return { setupEventListeners: () => { } };

        function generatePassword(length = 14) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
            return Array.from({ length }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
        }

        function openModal() {
            generatedPasswordInput.value = generatePassword();
            passwordGeneratorModal.style.display = 'flex';
            passwordGeneratorModal.style.zIndex = '10001';
            passwordSavedCheckbox.checked = false;
            copyPasswordBtn.disabled = true;
            copyBtnText.textContent = translate('copy_button') || 'Copy';
        }

        function closeModal() {
            passwordGeneratorModal.style.display = 'none';
            passwordGeneratorModal.style.zIndex = '-1';
        }

        function regeneratePassword() {
            generatedPasswordInput.value = generatePassword();
            passwordSavedCheckbox.checked = false;
            copyPasswordBtn.disabled = true;
            copyBtnText.textContent = translate('copy_button') || 'Copy';
        }

        async function copyToClipboard() {
            if (copyPasswordBtn.disabled) return;
            await navigator.clipboard.writeText(generatedPasswordInput.value);
            copyBtnText.textContent = translate('copied_button') || 'Copied!';
            if (registerPasswordInput) registerPasswordInput.value = generatedPasswordInput.value;
            if (registerConfirmPasswordInput) registerConfirmPasswordInput.value = generatedPasswordInput.value;
            setTimeout(closeModal, 1200);
        }

        function setupEventListeners() {
            if (passwordGenerateBtn) passwordGenerateBtn.addEventListener('click', openModal);
            if (passwordModalCloseBtn) passwordModalCloseBtn.addEventListener('click', closeModal);
            if (regeneratePasswordBtn) regeneratePasswordBtn.addEventListener('click', regeneratePassword);
            if (copyPasswordBtn) copyPasswordBtn.addEventListener('click', copyToClipboard);
            if (passwordSavedCheckbox) {
                passwordSavedCheckbox.addEventListener('change', () => {
                    copyPasswordBtn.disabled = !passwordSavedCheckbox.checked;
                });
            }
        }
        return { setupEventListeners };
    })();

    // --- AJAX Form Submission Logic ---
    async function handleAuthFormSubmit(formElement, event) {
        event.preventDefault();
        const submitButton = formElement.querySelector('.submit-btn');

        setDialogLoadingState(true);
        toggleButtonSpinner(submitButton, true);
        if (typeof clearActionFormErrors === 'function') clearActionFormErrors(formElement);

        try {
            const formData = new FormData(formElement);
            if (formElement === registerForm && mobileRegisterInput && mobileRegisterInput.value.trim() !== '' && phoneInputValidator) {
                const iti = phoneInputValidator.getInstance();
                if (iti.isValidNumber()) {
                    formData.set('mobile', iti.getNumber());
                }
            }

            const response = await http().post(formElement.action, formData);
            const data = response.data;

            showModalMessage?.('success', {
                bodyHtml: data.message || 'Success!',
                title: 'Success',
                timerSeconds: 1.5,
                onClose: () => {
                    if (data.data?.redirect) {
                        window.location.href = data.data.redirect;
                    } else {
                        authDialogModule.close();
                    }
                }
            });

        } catch (error) {
            const message = error?.response?.data?.message;
            const validationErrors = error?.response?.data?.errors;
            if (validationErrors && typeof displayActionFormErrors === 'function') {
                displayActionFormErrors(formElement, validationErrors);
            }
            showModalMessage?.('error', { title: 'Error', bodyHtml: message || 'An error occurred.' });
        } finally {
            setDialogLoadingState(false);
            toggleButtonSpinner(submitButton, false);
            if (formElement === registerForm && agreeTermsCheckbox) {
                registerSubmitBtn.disabled = !agreeTermsCheckbox.checked;
            }
        }
    }

    // --- Setup Form Submissions ---
    function setupFormSubmissions() {
        [loginForm, registerForm, forgotPasswordForm].forEach(form => {
            if (form) {
                form.addEventListener('submit', (e) => handleAuthFormSubmit(form, e));
            }
        });
    }

    // --- Other UI Logic ---
    const otherUIModule = (() => {
        function setupTermsCheckbox() {
            if (agreeTermsCheckbox && registerSubmitBtn) {
                const updateSubmitButtonState = () => {
                    registerSubmitBtn.disabled = !agreeTermsCheckbox.checked;
                };
                agreeTermsCheckbox.addEventListener('change', updateSubmitButtonState);
                updateSubmitButtonState();
            }
        }
        function initialize() {
            setupTermsCheckbox();
        }
        return { initialize };
    })();


    // --- Logout Module ---
    const logoutModule = (() => {
        if (!logoutButton) return { setupEventListener: () => { } };

        async function performLogout() {
            const response = await http().post(getRoute('auth.logout'), {});
            const data = response.data;
            showModalMessage?.('success', {
                bodyHtml: data.message || 'Logged out successfully',
                title: 'Logged Out!',
                timerSeconds: 1.5,
                onClose: () => window.location.href = data.data?.redirect || getRoute('home')
            });
        }
        function setupEventListener() {
            logoutButton.addEventListener('click', (event) => {
                event.preventDefault();
                showModalMessage('confirm', {
                    title: 'Confirm Logout',
                    bodyHtml: 'Are you sure you want to log out?',
                    buttons: [
                        { text: 'Yes, Logout', class: 'my-modal-btn-danger', onClick: performLogout },
                        { text: 'Cancel', class: 'my-modal-btn-outline' }
                    ]
                });
            });
        }
        return { setupEventListener };
    })();


    // --- Initialization Calls ---
    authDialogModule.setupEventListeners();
    setupFormSubmissions();
    passwordGeneratorModule.setupEventListeners();
    otherUIModule.initialize();
    logoutModule.setupEventListener();
    PasswordInputManager.init();

    // Expose the open function globally if needed
    if (authDialogModule?.open) {
        window.openAuthDialog = authDialogModule.open;
    }

    console.log('[AuthModal] Dialog with content-lock initialized.');
}

// Initialize the entire script
// if (document.readyState === 'loading') {
//     document.addEventListener('DOMContentLoaded', initAuthModal);
// } else {
//     initAuthModal();
// }
