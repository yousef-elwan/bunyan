<script type="text/javascript" src="{{ asset('dashboard/script/script.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    class IntlPhoneInput {

        constructor(inputSelector = '.phone-input', outputSelector = '.phone-output') {
            this.inputs = document.querySelectorAll(inputSelector);
            this.instances = new Map();

            if (!this.inputs.length) return;

            this.inputs.forEach((input) => {
                const output = input.nextElementSibling?.classList.contains('phone-output') ?
                    input.nextElementSibling :
                    null;

                const iti = window.intlTelInput(input, {
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/js/utils.js",
                    separateDialCode: true,
                    initialCountry: "sy",
                    nationalMode: true,
                    geoIpLookup: function(callback) {
                        fetch('https://ipapi.co/json')
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback('sy'));
                    },
                    dropdownContainer: document.body
                });

                this.instances.set(input, iti);

                const handleChange = () => {
                    if (!output) return;

                    let message;
                    if (input.value.trim()) {
                        message = iti.isValidNumber() ?
                            `✅ Valid: ${iti.getNumber()}` :
                            "❌ Invalid number";
                    } else {
                        message = "📞 Please enter a phone number";
                    }

                    console.log(message);

                    output.textContent = message;
                };

                input.addEventListener('change', handleChange);
                input.addEventListener('keyup', handleChange);
            });
        }

        getInit(input) {
            return this.instances.get(input);
        }

        getAllValues() {
            const results = [];
            this.instances.forEach((iti, input) => {
                results.push({
                    element: input,
                    number: iti.getNumber(),
                    isValid: iti.isValidNumber(),
                    country: iti.getSelectedCountryData()
                });
            });
            return results;
        }

    }
    class PasswordInputInput {
        constructor(inputSelector = '.password-input') {
            this.inputs = document.querySelectorAll(inputSelector);
            this.instances = new Map();
            if (!this.inputs.length) return;
            this.inputs.forEach((input) => {

                console.log(input);

                const togglePassword = document.getElementById(input.getAttribute('data-toggleId'));
                const eyeIcon = document.getElementById(input.getAttribute('data-eyeIconId'));

                togglePassword.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' :
                        'password';
                    input.setAttribute('type', type);

                    // Toggle eye icon fill
                    if (type === 'text') {
                        eyeIcon.innerHTML =
                            '<path d="M17.94 17.94L6.06 6.06M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    } else {
                        eyeIcon.innerHTML =
                            '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#A3ABB0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    }
                });
            });
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        window.intlPhoneInputInstance = new IntlPhoneInput(); // Expose instance globally
        new PasswordInputInput();

        function formatNumberWithCommas(value) {
            if (!value) return '';
            const parts = value.toString().replace(/,/g, '').split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        }

        function syncHiddenValue(visibleInput) {
            const targetSelector = visibleInput.getAttribute('data-hidden-target');
            const hiddenInput = document.querySelector(targetSelector);
            if (hiddenInput) {
                const raw = visibleInput.value.replace(/,/g, '');
                hiddenInput.value = raw;
            }
        }

        document.querySelectorAll('.price-input').forEach(input => {
            input.addEventListener('input', function() {
                const raw = input.value.replace(/,/g, '');
                if (!/^\d*\.?\d*$/.test(raw)) return;
                input.value = formatNumberWithCommas(raw);
                syncHiddenValue(input);
            });

            input.addEventListener('focus', function() {
                input.value = input.value.replace(/,/g, '');
            });

            input.addEventListener('blur', function() {
                input.value = formatNumberWithCommas(input.value);
                syncHiddenValue(input);
            });

            // Format initial value on page load (optional)
            input.value = formatNumberWithCommas(input.value.replace(/,/g, ''));
            syncHiddenValue(input);
        });

        // function formatNumberWithCommas(value) {
        //     if (!value) return '';
        //     const parts = value.toString().replace(/,/g, '').split('.');
        //     parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        //     return parts.join('.');
        // }

        // // Apply to all inputs with .price-input
        // document.querySelectorAll('.price-input').forEach(input => {
        //     input.addEventListener('input', function(e) {
        //         let value = e.target.value.replace(/,/g, '');
        //         if (value === '') return;

        //         const parts = value.split('.');
        //         parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        //         e.target.value = parts.join('.');
        //     });

        //     // Optional: remove commas on focus
        //     input.addEventListener('focus', function() {
        //         this.value = this.value.replace(/,/g, '');
        //     });

        //     // Optional: re-apply commas on blur
        //     input.addEventListener('blur', function() {
        //         this.value = formatNumberWithCommas(this.value);
        //     });
        // });
    });
</script>
