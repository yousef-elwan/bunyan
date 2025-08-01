@php
    $currentLocale = app()->getLocale();
    $currentDir = getLocaleDirection();
@endphp

{{-- =================================================================== --}}
{{-- ========== PASSWORD GENERATOR MODAL (يبقى كما هو) ========== --}}
{{-- =================================================================== --}}
<div class="password-modal" id="passwordGeneratorModal">
    <div class="password-modal-content">
        <button class="password-modal-close" id="passwordModalCloseBtn" aria-label="Close">×</button>
        <h3>{{ __('app/auth_modal.generated_password_title', [], $currentLocale) }}</h3>
        <p>{{ __('app/auth_modal.generated_password_instructions', [], $currentLocale) }}</p>
        <div class="password-display-container">
            <input type="text" id="generatedPasswordInput" readonly>
            <button class="copy-btn" id="copyPasswordBtn" disabled>
                <i class="fas fa-copy"></i>
                {{-- <span id="copyBtnText">{{ __('app/auth_modal.copy_button', [], $currentLocale) }}</span> --}}
            </button>
        </div>
        <div class="password-confirm">
            <input type="checkbox" id="passwordSavedCheckbox">
            <label
                for="passwordSavedCheckbox">{{ __('app/auth_modal.confirm_password_saved', [], $currentLocale) }}</label>
        </div>
    </div>
</div>


{{-- =================================================================== --}}
{{-- ========== [الجديد] لوحة المصادقة الجانبية ========== --}}
{{-- =================================================================== --}}
<div class="auth-dialog-panel" id="authDialogPanel">
    <div class="auth-dialog-content">
        <button class="auth-dialog-close-btn" id="closeAuthDialogBtn"
            aria-label="{{ __('app/auth_modal.close_dialog_label', ['Default Close'], $currentLocale) }}">×</button>

        <div class="auth-dialog-form-side">
            <div class="form-container">

                {{-- =========== نموذج تسجيل الدخول =========== --}}
                <div class="form-wrapper login-form active-form">
                    <h2>{{ __('app/auth_modal.login_title', [], $currentLocale) }}</h2>
                    <form id="loginForm" action="{{ route('auth.login.submit') }}" method="POST" novalidate>
                        @csrf
                        <div class="input-group">
                            <i class="fas fa-envelope icon"></i>
                            <input type="text" name="login_identifier"
                                placeholder="{{ __('app/auth_modal.email_or_phone_placeholder', [], $currentLocale) }}"
                                required
                                aria-label="{{ __('app/auth_modal.email_or_phone_placeholder', [], $currentLocale) }}">
                            <span class="invalid-feedback" data-field="login_identifier"></span>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="password" class="password-input" id="loginPasswordInput"
                                placeholder="{{ __('app/auth_modal.password_placeholder', [], $currentLocale) }}"
                                required
                                aria-label="{{ __('app/auth_modal.password_placeholder', [], $currentLocale) }}">
                            <button type="button" class="password-toggle-btn" data-input-id="loginPasswordInput"
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                            <span class="invalid-feedback" data-field="password"></span>
                        </div>
                        <a href="#"
                            class="forgot-password toggle-to-forgot">{{ __('app/auth_modal.forgot_password_link', [], $currentLocale) }}</a>
                        <button type="submit" class="submit-btn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            {{ __('app/auth_modal.login_button', [], $currentLocale) }}
                        </button>

                        <div class="social-login-divider">
                            <span>{{ __('app/auth_modal.or_divider', [], $currentLocale) }}</span>
                        </div>

                        <button type="button" class="social-login-btn google-login-btn" id="googleLoginBtnLogin">
                            <i class="fab fa-google"></i>
                            <span>{{ __('app/auth_modal.login_with_google_login', [], $currentLocale) }}</span>
                        </button>

                        <p class="toggle-form-text">
                            {{ __('app/auth_modal.no_account_prompt', [], $currentLocale) }} <a href="#"
                                class="toggle-to-register">{{ __('app/auth_modal.create_new_account_link', [], $currentLocale) }}</a>
                        </p>
                    </form>
                </div>

                {{-- =========== نموذج التسجيل =========== --}}
                <div class="form-wrapper register-form">
                    <h2>{{ __('app/auth_modal.register_title', [], $currentLocale) }}</h2>
                    <form id="registerForm" action="{{ route('auth.register.submit') }}" method="POST" novalidate>
                        @csrf
                        <div class="input-group-row">
                            <div class="input-group">
                                <i class="fas fa-user icon"></i>
                                <input type="text" name="first_name"
                                    placeholder="{{ __('app/auth_modal.first_name_placeholder', [], $currentLocale) }}"
                                    required
                                    aria-label="{{ __('app/auth_modal.first_name_placeholder', [], $currentLocale) }}">
                                <span class="invalid-feedback" data-field="first_name"></span>
                            </div>
                            <div class="input-group">
                                <i class="fas fa-user icon"></i>
                                <input type="text" name="last_name"
                                    placeholder="{{ __('app/auth_modal.last_name_placeholder', [], $currentLocale) }}"
                                    required
                                    aria-label="{{ __('app/auth_modal.last_name_placeholder', [], $currentLocale) }}">
                                <span class="invalid-feedback" data-field="last_name"></span>
                            </div>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-envelope icon"></i>
                            <input type="email" name="email"
                                placeholder="{{ __('app/auth_modal.email_placeholder', [], $currentLocale) }}" required
                                aria-label="{{ __('app/auth_modal.email_placeholder', [], $currentLocale) }}">
                            <span class="invalid-feedback" data-field="email"></span>
                        </div>
                        <div class="input-group">
                            <input type="tel" id="mobileRegisterInput" class="phone-input" name="mobile_raw"
                                aria-label="{{ __('app/auth_modal.mobile_placeholder', [], $currentLocale) }}">
                            <span class="invalid-feedback" data-field="mobile"></span>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="password" class="password-input" id="registerPasswordInput"
                                placeholder="{{ __('app/auth_modal.password_placeholder', [], $currentLocale) }}"
                                required
                                aria-label="{{ __('app/auth_modal.password_placeholder', [], $currentLocale) }}">
                            <button type="button" class="password-toggle-btn" data-input-id="registerPasswordInput"
                                aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                            <span class="invalid-feedback" data-field="password"></span>
                        </div>
                        <button type="button" class="password-generate-btn" id="passwordGenerateBtn">
                            <i class="fas fa-key"></i>
                            {{ __('app/auth_modal.generate_strong_password', [], $currentLocale) }}
                        </button>
                        <div class="input-group" style="margin-top: 22px;">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="password_confirmation" class="password-input"
                                id="registerConfirmPasswordInput"
                                placeholder="{{ __('app/auth_modal.confirm_password_placeholder', [], $currentLocale) }}"
                                required
                                aria-label="{{ __('app/auth_modal.confirm_password_placeholder', [], $currentLocale) }}">
                            <button type="button" class="password-toggle-btn"
                                data-input-id="registerConfirmPasswordInput" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                            <span class="invalid-feedback" data-field="password_confirmation"></span>
                        </div>
                        <div class="terms-agreement-group">
                            <input type="checkbox" id="agreeTerms" name="agreeTerms" required
                                aria-labelledby="agreeTermsLabel">
                            <label for="agreeTerms" id="agreeTermsLabel" style="width: 100%;">
                                {{ __('app/auth_modal.agree_terms_prefix', [], $currentLocale) }}
                                <a href="#"
                                    target="_blank">{{ __('app/auth_modal.privacy_policy_link_text', [], $currentLocale) }}</a>
                                {{ __('app/auth_modal.and_conjunction', [], $currentLocale) }}
                                <a href="#"
                                    target="_blank">{{ __('app/auth_modal.terms_of_service_link_text', [], $currentLocale) }}</a>
                            </label>
                            <span class="invalid-feedback" data-field="agreeTerms"
                                style="width:100%; display:block;"></span>
                        </div>
                        <button type="submit" class="submit-btn" id="registerSubmitBtn" disabled>
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            {{ __('app/auth_modal.create_account_button', [], $currentLocale) }}
                        </button>
                        <p class="toggle-form-text">
                            {{ __('app/auth_modal.already_have_account_prompt', [], $currentLocale) }} <a
                                href="#"
                                class="toggle-to-login">{{ __('app/auth_modal.login_link', [], $currentLocale) }}</a>
                        </p>
                    </form>
                </div>

                {{-- =========== نموذج نسيت كلمة المرور =========== --}}
                <div class="form-wrapper forgot-password-form">
                    <h2>{{ __('app/auth_modal.forgot_password_title', [], $currentLocale) }}</h2>
                    <p class="forgot-password-instructions">
                        {{ __('app/auth_modal.forgot_password_instructions', [], $currentLocale) }}</p>
                    <form id="forgotPasswordForm" action="{{ route('api.auth.password.email') }}" method="POST"
                        novalidate>
                        @csrf
                        <div class="input-group">
                            <i class="fas fa-envelope icon"></i>
                            <input type="text" name="login_identifier"
                                placeholder="{{ __('app/auth_modal.email_or_phone_placeholder', [], $currentLocale) }}"
                                required>
                            <span class="invalid-feedback" data-field="login_identifier"></span>
                        </div>
                        <button type="submit" class="submit-btn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            {{ __('app/auth_modal.send_reset_link_button', [], $currentLocale) }}
                        </button>
                        <p class="toggle-form-text">
                            <a href="#"
                                class="toggle-to-login">{{ __('app/auth_modal.back_to_login_link', [], $currentLocale) }}</a>
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
