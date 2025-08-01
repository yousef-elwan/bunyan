@extends('app.layouts.auth')

@section('title', __('app/auth_modal.register_title'))

@section('content')
    <h2>{{ __('app/auth_modal.register_title') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul style="margin: 0; padding: 0; list-style: none;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="registerForm" method="POST" action="{{ route('auth.register') }}">
        @csrf
        <div class="input-group-row">
            <div class="input-group">
                <i class="fas fa-user icon"></i>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                    placeholder="{{ __('app/auth_modal.first_name_placeholder') }}" required autofocus
                    aria-label="{{ __('app/auth_modal.first_name_placeholder') }}">
                <span class="invalid-feedback" data-field="first_name"></span>
            </div>
            <div class="input-group">
                <i class="fas fa-user icon"></i>
                <input type="text" name="last_name" value="{{ old('last_name') }}"
                    placeholder="{{ __('app/auth_modal.last_name_placeholder') }}" required
                    aria-label="{{ __('app/auth_modal.last_name_placeholder') }}">
                <span class="invalid-feedback" data-field="last_name"></span>
            </div>
        </div>
        <div class="input-group">
            <i class="fas fa-envelope icon"></i>
            <input type="email" name="email" value="{{ old('email') }}"
                placeholder="{{ __('app/auth_modal.email_placeholder') }}" required
                aria-label="{{ __('app/auth_modal.email_placeholder') }}">
            <span class="invalid-feedback" data-field="email"></span>
        </div>

        <div class="input-group">
            <input type="tel" id="mobileRegisterInput" class="phone-input" name="mobile"
                placeholder="{{ __('app/auth_modal.mobile_placeholder') }}" required
                aria-label="{{ __('app/auth_modal.mobile_placeholder') }}" data-hidden-input-name="mobile">
            <span class="invalid-feedback" data-field="mobile"></span>
            <span class="invalid-feedback" data-field="mobile"></span>
        </div>

        <div class="input-group">
            <i class="fas fa-lock icon"></i>
            <input type="password" name="password" class="password-input" data-toggleid="registerPasswordToggle"
                data-eyeiconid="registerPasswordEye" placeholder="{{ __('app/auth_modal.password_placeholder') }}" required
                aria-label="{{ __('app/auth_modal.password_placeholder') }}">
            <button type="button" class="password-toggle-btn" id="registerPasswordToggle"
                aria-label="Toggle password visibility" data-toggleid="registerPasswordToggle">
                <i class="fas fa-eye" id="registerPasswordEye"></i>
            </button>
            <span class="invalid-feedback" data-field="password"></span>
        </div>



        <div class="input-group">
            <i class="fas fa-lock icon"></i>
            <input type="password" name="password_confirmation" class="password-input"
                data-toggleid="registerConfirmPasswordToggle" data-eyeiconid="registerConfirmPasswordEye"
                placeholder="{{ __('app/auth_modal.confirm_password_placeholder') }}" required
                aria-label="{{ __('app/auth_modal.confirm_password_placeholder') }}">
            <button type="button" class="password-toggle-btn" id="registerConfirmPasswordToggle"
                data-toggleid="registerConfirmPasswordToggle" aria-label="Toggle password visibility">
                <i class="fas fa-eye" id="registerConfirmPasswordEye"></i>
            </button>
            <span class="invalid-feedback" data-field="password_confirmation"></span>
        </div>

        <div class="terms-agreement-group">
            <input type="checkbox" id="agreeTerms" name="agreeTerms" {{ old('agreeTerms') ? 'checked' : '' }} required
                aria-labelledby="agreeTermsLabel">
            <label for="agreeTerms" id="agreeTermsLabel" style="width: 100%;">
                {{ __('app/auth_modal.agree_terms_prefix') }}
                <a href="{{ route('privacy-policy') }}"
                    target="_blank">{{ __('app/auth_modal.privacy_policy_link_text') }}</a>
                {{ __('app/auth_modal.and_conjunction') }}
                <a href="{{ route('terms-of-service') }}"
                    target="_blank">{{ __('app/auth_modal.terms_of_service_link_text') }}</a>
            </label>
        </div>
        <span class="invalid-feedback" data-field="agreeTerms" style="width:100%; display:block;"></span>

        <button type="submit" class="submit-btn" id="registerSubmitBtn" {{ old('agreeTerms') ? '' : 'disabled' }}>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            {{ __('app/auth_modal.create_account_button') }}
        </button>

        {{-- <div class="social-login-divider">
            <span>{{ __('app/auth_modal.or_divider') }}</span>
        </div>

        <button type="button" class="social-login-btn google-login-btn" id="googleLoginBtnRegister">
            <i class="fab fa-google"></i>
            <span class="btn-text-content"></span>
        </button> --}}

        @if (Route::has('auth.login'))
            <p class="toggle-form-text">
                {{ __('app/auth_modal.already_have_account_prompt') }}
                <a href="{{ route('auth.login') }}" class="link-to-other-page">{{ __('app/auth_modal.login_link') }}</a>
            </p>
        @endif
    </form>
@endsection
