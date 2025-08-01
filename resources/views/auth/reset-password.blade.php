<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ getLocaleDirection() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Password') }}</title>

    @include('app.partials.styles')

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
    <style>
        /* ========== [ STYLES - مقتبسة ومضمنة من auth-modal.css ] ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tahoma', 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
            direction: {{ getLocaleDirection() }};
        }

        .d-none {
            display: none !important;
        }

        /* Main Container */
        .reset-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 420px;
            overflow: hidden;
        }

        .form-wrapper {
            padding: 32px;
        }

        .form-wrapper h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
            font-size: 20px;
        }

        /* Form Elements */
        .input-group {
            position: relative;
            margin-bottom: 14px;
        }

        .input-group .icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 14px;
        }

        [dir="ltr"] .input-group .icon {
            left: 12px;
        }

        [dir="rtl"] .input-group .icon {
            right: 12px;
        }

        .input-group input {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            line-height: 1.4;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        [dir="ltr"] .input-group input {
            padding-left: 32px;
            padding-right: 40px;
        }

        [dir="rtl"] .input-group input {
            padding-right: 32px;
            padding-left: 40px;
        }

        .input-group input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.16rem rgba(0, 123, 255, 0.25);
        }

        .password-toggle-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #7f8c8d;
            cursor: pointer;
            padding: 8px;
            font-size: 1.05rem;
            z-index: 3;
        }

        [dir="ltr"] .password-toggle-btn {
            right: 10px;
        }

        [dir="rtl"] .password-toggle-btn {
            left: 10px;
        }

        /* Buttons */
        .submit-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .submit-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .password-generate-btn {
            width: 100%;
            padding: 10px 15px;
            margin-top: 0;
            margin-bottom: 22px;
            background-color: #f8f9fa;
            border: 1.5px solid #dce4ec;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #333;
        }

        .password-generate-btn:hover {
            background-color: #e9ecef;
        }

        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: .2em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        /* Password Generator Modal */
        .password-modal {
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
        }

        .password-modal-content {
            background-color: #fff;
            padding: 30px 35px;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            position: relative;
        }

        .password-modal-content h3 {
            margin-bottom: 8px;
        }

        .password-modal-content p {
            margin-bottom: 16px;
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }

        .password-modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            cursor: pointer;
            background: none;
            border: none;
        }

        [dir="rtl"] .password-modal-close {
            right: auto;
            left: 15px;
        }

        .password-display-container {
            display: flex;
            margin-bottom: 15px;
        }

        .password-display-container input {
            flex-grow: 1;
            border: 1.5px solid #dce4ec;
            padding: 12px 15px;
            border-radius: 8px 0 0 8px;
            border-inline-end: none;
        }

        [dir="rtl"] .password-display-container input {
            border-radius: 0 8px 8px 0;
            border-inline-end: 1.5px solid #dce4ec;
            border-inline-start: none;
        }

        .modal-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #f0f2f5;
            color: #333;
            border: 1.5px solid #dce4ec;
            padding: 0 15px;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .modal-btn:hover {
            background-color: #e9ecef;
        }

        .regenerate-btn {
            border-inline-end: none;
        }

        [dir="rtl"] .regenerate-btn {
            border-inline-start: none;
            border-inline-end: 1.5px solid #dce4ec;
        }

        .copy-btn {
            border-radius: 0 8px 8px 0;
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .copy-btn:hover:not(:disabled) {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        [dir="rtl"] .copy-btn {
            border-radius: 8px 0 0 8px;
        }

        /* [IMPROVED] Disabled state for copy button */
        .copy-btn:disabled {
            background-color: #adb5bd;
            border-color: #adb5bd;
            color: #f8f9fa;
            cursor: not-allowed;
        }

        .password-confirm {
            display: flex;
            align-items: center;
        }

        .password-confirm input[type="checkbox"] {
            margin-inline-end: 10px;
            width: 16px;
            height: 16px;
        }

        /* Alert / Error Display */
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
            width: 100%;
            box-sizing: border-box;
            text-align: {{ getLocaleDirection() === 'rtl' ? 'right' : 'left' }};
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
    @include('shared.partials.paramToJs')

    @vite(['resources/js/alpine/app/auth/reset-password.js'])

</head>

<body>
    <div class="reset-container">
        <div class="password-modal" id="passwordGeneratorModal">
            <div class="password-modal-content">
                <button class="password-modal-close" id="passwordModalCloseBtn" aria-label="Close">×</button>
                <h3>{{ __('app/auth_modal.generated_password_title') }}</h3>
                <p>{{ __('app/auth_modal.generated_password_instructions') }}</p>
                <div class="password-display-container">
                    <input type="text" id="generatedPasswordInput" readonly>
                    <button type="button" class="modal-btn regenerate-btn" id="regeneratePasswordBtn"
                        title="Regenerate Password">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="modal-btn copy-btn" id="copyPasswordBtn" disabled>
                        <i class="fas fa-copy"></i>
                        {{-- <span id="copyBtnText">{{ __('app/auth_modal.copy_button') }}</span> --}}
                    </button>
                </div>
                <div class="password-confirm">
                    <input type="checkbox" id="passwordSavedCheckbox">
                    <label for="passwordSavedCheckbox">{{ __('app/auth_modal.confirm_password_saved') }}</label>
                </div>
            </div>
        </div>

        <div class="form-wrapper">
            <h2>{{ __('Reset Your Password') }}</h2>
            <div id="status-display"></div>
            <form id="resetPasswordForm"
                action="{{ route('api.auth.password.update') }}" method="POST"
                novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token ?? '' }}">
                <input type="hidden" name="email" value="{{ $email ?? '' }}">
                <div class="input-group">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password" id="resetPasswordInput"
                        placeholder="{{ __('New Password') }}" required>
                    <button type="button" class="password-toggle-btn" data-input-id="resetPasswordInput"
                        aria-label="Toggle password visibility"><i class="fas fa-eye"></i></button>
                </div>
                <button type="button" class="password-generate-btn" id="passwordGenerateBtn"><i class="fas fa-key"></i>
                    {{ __('app/auth_modal.generate_strong_password') }}</button>
                <div class="input-group">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password_confirmation" id="resetConfirmPasswordInput"
                        placeholder="{{ __('Confirm New Password') }}" required>
                    <button type="button" class="password-toggle-btn" data-input-id="resetConfirmPasswordInput"
                        aria-label="Toggle password visibility"><i class="fas fa-eye"></i></button>
                </div>
                <button type="submit" class="submit-btn" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    {{ __('Reset Password') }}
                </button>
            </form>
        </div>
    </div>
    {{-- 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- [ Helper Functions ] ---
            const translate = (key) => ({
                'copy_button': '{{ __('app/auth_modal.copy_button') }}',
                'copied_button': '{{ __('app/auth_modal.copied_button') }}',
            })[key] || key;

            // --- [ Password Visibility Toggle Logic ] ---
            document.querySelectorAll('.password-toggle-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const inputId = e.currentTarget.dataset.inputId;
                    const input = document.getElementById(inputId);
                    if (!input) return;
                    const icon = e.currentTarget.querySelector('i');
                    input.type = input.type === 'password' ? 'text' : 'password';
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            });

            // --- [ Password Generator Logic ] ---
            const passwordGeneratorModal = document.getElementById('passwordGeneratorModal');
            if (passwordGeneratorModal) {
                const openBtn = document.getElementById('passwordGenerateBtn');
                const closeBtn = document.getElementById('passwordModalCloseBtn');
                const regenerateBtn = document.getElementById('regeneratePasswordBtn');
                const generatedPasswordInput = document.getElementById('generatedPasswordInput');
                const copyPasswordBtn = document.getElementById('copyPasswordBtn');
                const copyBtnText = document.getElementById('copyBtnText');
                const savedCheckbox = document.getElementById('passwordSavedCheckbox');
                const mainPasswordInput = document.getElementById('resetPasswordInput');
                const confirmPasswordInput = document.getElementById('resetConfirmPasswordInput');

                const generatePassword = (length = 14) => Array.from({
                    length
                }, () => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()' [Math
                    .floor(Math.random() * 72)
                ]).join('');

                const regenerateAndUpdate = () => {
                    generatedPasswordInput.value = generatePassword();
                    // [FIX] Ensure checkbox and button state are reset every time
                    savedCheckbox.checked = false;
                    copyPasswordBtn.disabled = true;
                    copyBtnText.textContent = translate('copy_button');
                };

                const openModal = () => {
                    regenerateAndUpdate();
                    passwordGeneratorModal.style.display = 'flex';
                };

                const closeModal = () => {
                    passwordGeneratorModal.style.display = 'none';
                };

                const copyToClipboard = async () => {
                    if (copyPasswordBtn.disabled) return; // Extra safety check
                    await navigator.clipboard.writeText(generatedPasswordInput.value);
                    copyBtnText.textContent = translate('copied_button');
                    mainPasswordInput.value = generatedPasswordInput.value;
                    confirmPasswordInput.value = generatedPasswordInput.value;
                    setTimeout(closeModal, 1200);
                };

                // [FIX] Correctly attach listeners
                openBtn.addEventListener('click', openModal);
                closeBtn.addEventListener('click', closeModal);
                regenerateBtn.addEventListener('click', regenerateAndUpdate);
                copyPasswordBtn.addEventListener('click', copyToClipboard);

                // [FIX] The core logic to enable/disable the copy button
                savedCheckbox.addEventListener('change', () => {
                    copyPasswordBtn.disabled = !savedCheckbox.checked;
                });
            }

            // --- [ AJAX Form Submission Logic ] ---
            const resetForm = document.getElementById('resetPasswordForm');
            const submitBtn = document.getElementById('submitBtn');
            const statusDisplay = document.getElementById('status-display');

            resetForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                statusDisplay.innerHTML = '';
                statusDisplay.style.display = 'none';
                submitBtn.disabled = true;
                const spinner = submitBtn.querySelector('.spinner-border');
                if (spinner) spinner.classList.remove('d-none');

                try {
                    const response = await fetch(resetForm.action, {
                        method: 'POST',
                        body: new FormData(resetForm),
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        let errorHtml = data.message || 'An error occurred.';
                        if (data.errors && Object.keys(data.errors).length > 0) {
                            errorHtml += '<ul style="margin-top: 10px; padding-inline-start: 20px;">';
                            Object.values(data.errors).forEach(errArray => {
                                errArray.forEach(err => errorHtml += `<li>${err}</li>`);
                            });
                            errorHtml += '</ul>';
                        }
                        statusDisplay.className = 'alert alert-danger';
                        statusDisplay.innerHTML = errorHtml;
                    } else {
                        statusDisplay.className = 'alert alert-success';
                        statusDisplay.innerHTML = data.message;
                        resetForm.style.display = 'none';
                        setTimeout(() => window.location.href = `{{ route('home') }}`, 3000);
                    }
                    statusDisplay.style.display = 'block';
                } catch (error) {
                    statusDisplay.className = 'alert alert-danger';
                    statusDisplay.innerHTML = 'A network error occurred. Please try again.';
                    statusDisplay.style.display = 'block';
                } finally {
                    submitBtn.disabled = false;
                    if (spinner) spinner.classList.add('d-none');
                }
            });
        });
    </script> --}}


</body>

</html>
