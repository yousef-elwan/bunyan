// This module provides reusable functions to create and validate phone input fields
// without relying on any global variables, making it more modular and reusable.

// Function to initialize the intlTelInput plugin on a given phone input element.
// Accepts the phone input element and optionally the current user mobile number to prefill.
// Returns the intlTelInput instance or null if initialization failed or not possible.
export function initializePhoneInput(phoneInput, currentUserMobile) {
    let itiInstance = null;

    if (phoneInput && typeof window.intlTelInput === 'function') {
        try {
            itiInstance = window.intlTelInput(phoneInput, {
                initialCountry: "sy",
                geoIpLookup: function (callback) {
                    fetch("https://ipapi.co/json")
                        .then(res => res.json())
                        .then(data => callback(data.country_code || "us"))
                        .catch(() => callback("us"));
                },
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
                separateDialCode: true,
                preferredCountries: ['sa', 'ae', 'kw', 'qa', 'bh', 'om', 'eg', 'jo', 'lb', 'tr', 'us', 'gb']
            });

            if (!window.AppConfig.isAuthenticated && currentUserMobile) {
                itiInstance.setNumber(currentUserMobile);
            }
        } catch (e) {
            console.error("[PhoneInput] Error initializing intlTelInput:", e);
            itiInstance = null; // Ensure it's null if init fails
            if (!window.AppConfig.isAuthenticated && currentUserMobile && phoneInput) { // Fallback to basic prefill
                phoneInput.value = currentUserMobile;
            }
        }
    } else if (phoneInput) {
        console.warn('[PhoneInput] intlTelInput function not found or input field missing. Phone input will be basic.');
        if (!window.AppConfig.isAuthenticated && currentUserMobile) { // Basic pre-fill if intl-tel-input is not available
            phoneInput.value = currentUserMobile;
        }
    }

    return itiInstance;
}

// Function to create a phone input validator for real-time validation and error display.
// Accepts the intlTelInput instance, the phone input element, an optional error container element, and a translate function.
// Returns an object with methods to setup event listeners, validate the input, get the instance, and reset validation.
export function createPhoneInputValidator(itiInstance, phoneInput, errorContainer, translate) {

    function getInstance() {
        return itiInstance;
    }

    function validate() {
        if (!itiInstance || !phoneInput) return true;
        if (!errorContainer) {
            console.warn('[PhoneInputValidator] errorContainer element not provided.');
        }
        if (phoneInput.value.trim() === '') {
            phoneInput.classList.remove('iti-invalid', 'is-invalid');
            if (errorContainer) {
                errorContainer.textContent = '';
                errorContainer.style.display = 'none';
            }
            return true;
        }
        const isValid = itiInstance.isValidNumber();

        phoneInput.classList.toggle('iti-invalid', !isValid);
        phoneInput.classList.toggle('is-invalid', !isValid);
        if (errorContainer) {
            errorContainer.textContent = isValid ? '' : (translate ? translate('phone_invalid') : 'Invalid phone number');
            errorContainer.style.display = isValid ? 'none' : 'block';
        }

        return isValid;
    }

    function setupEventListeners() {
        if (phoneInput) {
            ['input', 'blur'].forEach(event => phoneInput.addEventListener(event, validate));
            phoneInput.addEventListener('countrychange', () => setTimeout(validate, 100));
        }
    }

    function resetValidation() {
        if (phoneInput) {
            phoneInput.classList.remove('iti-invalid', 'is-invalid');
            if (errorContainer) {
                errorContainer.textContent = '';
                errorContainer.style.display = 'none';
            }
        }
    }

    return { setupEventListeners, validate, getInstance, resetValidation };
}
