import { clearActionFormErrors, showModalMessage, displayActionFormErrors, getRoute, translate } from '../../utils/helpers.js';
import { initializePhoneInput, createPhoneInputValidator } from '../../utils/phoneInput.js';
import { http } from '../../utils/api.js';

export function initAuthModal() {
    // Selectors
    const openAuthDialogBtn = document.getElementById('openAuthDialogBtn');
    const closeAuthDialogBtn = document.getElementById('closeAuthDialogBtn');
    const authDialogPanel = document.getElementById('authDialogPanel');
    if (!authDialogPanel) return;

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
    const generatedPasswordInput = document.getElementById('generatedPasswordInput');
    const copyPasswordBtn = document.getElementById('copyPasswordBtn');
    const copyBtnText = document.getElementById('copyBtnText');
    const passwordSavedCheckbox = document.getElementById('passwordSavedCheckbox');
    const registerPasswordInput = document.getElementById('registerPasswordInput');
    const registerConfirmPasswordInput = document.getElementById('registerConfirmPasswordInput');

    // Phone Input Initialization
    if (mobileRegisterInput) {
        const itiInstance = initializePhoneInput(mobileRegisterInput);
        const phoneInputValidator = createPhoneInputValidator(
            itiInstance,
            mobileRegisterInput,
            registerForm.querySelector('.invalid-feedback[data-field="mobile"]'),
            translate
        );
        phoneInputValidator.setupEventListeners();
    }

    // Loading State Management
    function setDialogLoadingState(isLoading) {
        authDialogPanel.classList.toggle('loading-active', isLoading);
    }

    function toggleButtonSpinner(button, show) {
        if (!button) return;
        const spinner = button.querySelector('.spinner-border');
        button.disabled = show;
        if (spinner) {
            spinner.classList.toggle('d-none', !show);
        }
    }

    // Helper to clear form errors
    function clearAllFormsErrors() {
        [loginForm, registerForm, forgotPasswordForm].forEach(form => {
            if (form && typeof clearActionFormErrors === 'function') {
                clearActionFormErrors(form);
            }
        });
    }

    // Auth Panel Visibility & Form Switching Logic
    const authDialogModule = (() => {
        const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

        const showForm = (activeWrapper, inactiveWrappers) => {
            clearAllFormsErrors();
            activeWrapper.classList.add('active-form');
            activeWrapper.style.transform = 'translateX(0)';
            inactiveWrappers.forEach(wrapper => {
                wrapper.classList.remove('active-form');
                wrapper.style.transform = `translateX(${isRTL ? '-' : ''}100%)`;
            });
        };

        const showLogin = () => showForm(loginFormWrapper, [registerFormWrapper, forgotPasswordFormWrapper]);
        const showRegister = () => showForm(registerFormWrapper, [loginFormWrapper, forgotPasswordFormWrapper]);
        const showForgotPassword = () => showForm(forgotPasswordFormWrapper, [loginFormWrapper, registerFormWrapper]);

        const open = () => {
            authDialogPanel.classList.add('active');
            showLogin();
        };

        const close = () => {
            authDialogPanel.classList.remove('active');
        };

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

    // Password Visibility Toggle Logic
    function setupPasswordToggles() {
        document.querySelectorAll('.password-toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const inputId = e.currentTarget.dataset.inputId;
                const input = document.getElementById(inputId);
                if (!input) return;
                const icon = e.currentTarget.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });
    }

    // Password Generator Logic
    const passwordGeneratorModule = (() => {
        if (!passwordGeneratorModal) return { setupEventListeners: () => { } };

        const generatePassword = (length = 14) => Array.from({ length }, () => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()'[Math.floor(Math.random() * 72)]).join('');

        const openModal = () => {
            generatedPasswordInput.value = generatePassword();
            passwordGeneratorModal.style.display = 'flex';
            passwordSavedCheckbox.checked = false;
            copyPasswordBtn.disabled = true;
            copyBtnText.textContent = translate('copy_button') || 'Copy';
        };

        const closeModal = () => {
            passwordGeneratorModal.style.display = 'none';
        };

        const copyToClipboard = async () => {
            if (copyPasswordBtn.disabled) return;
            await navigator.clipboard.writeText(generatedPasswordInput.value);
            copyBtnText.textContent = translate('copied_button') || 'Copied!';
            if (registerPasswordInput) registerPasswordInput.value = generatedPasswordInput.value;
            if (registerConfirmPasswordInput) registerConfirmPasswordInput.value = generatedPasswordInput.value;
            setTimeout(closeModal, 1200);
        };

        function setupEventListeners() {
            if (passwordGenerateBtn) passwordGenerateBtn.addEventListener('click', openModal);
            if (passwordModalCloseBtn) passwordModalCloseBtn.addEventListener('click', closeModal);
            if (copyPasswordBtn) copyPasswordBtn.addEventListener('click', copyToClipboard);
            if (passwordSavedCheckbox) {
                passwordSavedCheckbox.addEventListener('change', () => {
                    copyPasswordBtn.disabled = !passwordSavedCheckbox.checked;
                });
            }
        }
        return { setupEventListeners };
    })();

    // AJAX Form Submission Logic
    async function handleAuthFormSubmit(formElement, event) {
        event.preventDefault();
        const submitButton = formElement.querySelector('.submit-btn');

        setDialogLoadingState(true);
        toggleButtonSpinner(submitButton, true);
        if (typeof clearActionFormErrors === 'function') clearActionFormErrors(formElement);

        try {
            const formData = new FormData(formElement);
            // Handle phone number if it's the register form
            if (formElement === registerForm && mobileRegisterInput && mobileRegisterInput.value.trim() !== '') {
                const iti = window.intlTelInputGlobals.getInstance(mobileRegisterInput);
                if (iti.isValidNumber()) {
                    formData.set('mobile', iti.getNumber());
                }
            }

            const response = await http().post(formElement.action, formData);
            const data = response.data;

            showModalMessage?.('success', {
                bodyHtml: data.message || translate('operation_successful'),
                title: translate('success_title'),
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

            showModalMessage?.('error', {
                title: translate('error_title'),
                bodyHtml: message || translate('generic_error_try_again')
            });

        } finally {
            setDialogLoadingState(false);
            toggleButtonSpinner(submitButton, false);
            if (formElement === registerForm && agreeTermsCheckbox) {
                registerSubmitBtn.disabled = !agreeTermsCheckbox.checked;
            }
        }
    }

    // Setup Form Submissions
    function setupFormSubmissions() {
        [loginForm, registerForm, forgotPasswordForm].forEach(form => {
            if (form) {
                form.addEventListener('submit', (e) => handleAuthFormSubmit(form, e));
            }
        });
    }

    // Other UI Logic
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

    // Initialization Calls
    authDialogModule.setupEventListeners();
    setupFormSubmissions();
    setupPasswordToggles();
    passwordGeneratorModule.setupEventListeners();
    otherUIModule.initialize();

    if (authDialogModule?.open) {
        window.openAuthDialog = authDialogModule.open;
    }

    console.log('[AuthModal] Slide-in Panel Initialization COMPLETE.');
}

// Initialize the modal if the page is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAuthModal);
} else {
    initAuthModal();
}