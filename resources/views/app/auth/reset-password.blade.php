@extends('app.layouts.auth')

@section('title', __('Reset Password'))

@section('content')
    <h2>{{ __('Reset Your Password') }}</h2>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul style="margin: 0; padding: 0; list-style: none;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="input-group">
            <i class="fas fa-envelope icon"></i>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                autofocus placeholder="{{ __('Email') }}">
            <span class="invalid-feedback" data-field="email"></span>
        </div>

        <!-- Password -->
        <div class="input-group password-input-wrapper" style="margin-top: 1rem;">
            <div class="relative">
                <i class="fas fa-lock icon"></i>
                <input id="password" type="password" name="password" required
                    class="password-input password-strength-input" data-toggleid="resetPasswordToggle"
                    data-eyeiconid="resetPasswordEye" placeholder="{{ __('New Password') }}">
                <button type="button" class="password-toggle-btn" id="resetPasswordToggle"
                    aria-label="Toggle password visibility">
                    <i class="fas fa-eye" id="resetPasswordEye"></i>
                </button>
            </div>
            <div class="password-strength-meter">
                <div class="strength-bar"></div>
                <div class="strength-text"></div>
            </div>
            <span class="invalid-feedback" data-field="password"></span>
        </div>

        <!-- Confirm Password -->
        <div class="input-group password-input-wrapper" style="margin-top: 1rem;">
            <div class="relative">
                <i class="fas fa-lock icon"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="password-input password-strength-input" data-toggleid="resetConfirmPasswordToggle"
                    data-eyeiconid="resetConfirmPasswordEye" placeholder="{{ __('Confirm New Password') }}">
                <button type="button" class="password-toggle-btn" id="resetConfirmPasswordToggle"
                    aria-label="Toggle password visibility">
                    <i class="fas fa-eye" id="resetConfirmPasswordEye"></i>
                </button>
            </div>
            <div class="password-strength-meter">
                <div class="strength-bar"></div>
                <div class="strength-text"></div>
            </div>
            <span class="invalid-feedback" data-field="password_confirmation"></span>
        </div>

        <button type="submit" class="submit-btn" style="width: 100%; margin-top: 15px;">
            {{ __('Reset Password') }}
        </button>
    </form>
@endsection
