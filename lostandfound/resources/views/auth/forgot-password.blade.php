@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')

    <h2 class="su-form-title">Forgot your password?</h2>
    <p class="su-form-subtitle">
        Enter your Strathmore email and we will send you a reset link.
    </p>

    @if (session('status'))
        <div class="su-alert su-alert--success" style="margin-bottom: 1rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="su-form-group">
            <label for="email" class="su-form-label">
                Strathmore Email
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="you@strathmore.edu"
                   required autofocus
                   class="su-form-input @error('email') su-form-input--error @enderror"/>
            @error('email')
                <p class="su-form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="su-btn-primary su-btn-block">
            Email Password Reset Link
        </button>
    </form>

    <p class="su-form-note">
        Remember your password?
        <a href="{{ route('login') }}" class="su-link su-link-strong">Sign in</a>
    </p>

@endsection
