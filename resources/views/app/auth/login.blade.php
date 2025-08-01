@extends('app.layouts.auth')

@section('title', __('app/auth_modal.login_title'))

@section('content')
    <h2>{{ __('app/auth_modal.login_title') }}</h2>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul style="margin: 0; padding: 0; list-style: none;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="loginForm" method="POST" action="{{ route('auth.login.submit') }}">
        @csrf
        <div class="input-group">
            <i class="fas fa-envelope icon"></i>
            <input type="text" name="login_identifier" value="{{ old('login_identifier') }}"
                placeholder="{{ __('app/auth_modal.email_or_phone_placeholder') }}" required
                aria-label="{{ __('app/auth_modal.email_or_phone_placeholder') }}" autofocus>
            <span class="invalid-feedback" data-field="login_identifier"></span>
        </div>

        <div class="input-group">
            <i class="fas fa-lock icon"></i>
            <input type="password" name="password" class="password-input" data-toggleid="loginPasswordToggle"
                data-eyeiconid="loginPasswordEye" placeholder="{{ __('app/auth_modal.password_placeholder') }}" required
                aria-label="{{ __('app/auth_modal.password_placeholder') }}">
            <button type="button" class="password-toggle-btn" id="loginPasswordToggle" data-toggleid="loginPasswordToggle"
                aria-label="Toggle password visibility">
                <i class="fas fa-eye" id="loginPasswordEye"></i>
            </button>
            <span class="invalid-feedback" data-field="password"></span>
            <span class="invalid-feedback" data-field="password"></span>
        </div>

        <div class="remember-forgot-row"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div class="remember-me">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">{{ __('app/auth_modal.remember_me') }}</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="forgot-password">{{ __('app/auth_modal.forgot_password_link') }}</a>
            @endif
        </div>

        <button type="submit" class="submit-btn">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            {{ __('app/auth_modal.login_button') }}
        </button>

        {{-- <div class="social-login-divider">
            <span>{{ __('app/auth_modal.or_divider') }}</span>
        </div>

        <button type="button" class="social-login-btn google-login-btn" id="googleLoginBtnLogin">
            <i class="fab fa-google"></i>
            <span class="btn-text-content"></span>
        </button> --}}

        @if (Route::has('auth.register'))
            <p class="toggle-form-text">
                {{ __('app/auth_modal.no_account_prompt') }}
                <a href="{{ route('auth.register') }}"
                    class="link-to-other-page">{{ __('app/auth_modal.create_new_account_link') }}</a>
            </p>
        @endif
    </form>
@endsection
