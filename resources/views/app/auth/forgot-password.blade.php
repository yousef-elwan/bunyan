@extends('app.layouts.auth')

@section('title', __('Reset Password'))

@section('content')
    <h2>{{ __('Forgot your password?') }}</h2>

    <div class="mb-4 text-sm text-gray-600" style="margin-bottom: 1rem; font-size: 0.875rem; color: #555;">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="input-group">
            <i class="fas fa-envelope icon"></i>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="{{ __('Email') }}">
            <span class="invalid-feedback" data-field="email"></span>
        </div>

        <button type="submit" class="submit-btn" style="width: 100%; margin-top: 10px;">
            {{ __('Email Password Reset Link') }}
        </button>

        @if (Route::has('login'))
            <p class="toggle-form-text" style="margin-top: 1rem;">
                <a href="{{ route('login') }}" class="link-to-other-page">{{ __('Back to Login') }}</a>
            </p>
        @endif
    </form>
@endsection
