@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')

    <h2 class="su-form-title">Welcome back</h2>
    <p class="su-form-subtitle">Sign in to your Strathmore account.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
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

        <!-- Password -->
        <div class="su-form-group">
            <label for="password" class="su-form-label">
                Password
            </label>
            <input type="password"
                   id="password"
                   name="password"
                   required
                   class="su-form-input @error('password') su-form-input--error @enderror"/>
            @error('password')
                <p class="su-form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="su-form-row">
            <label class="su-check-label">
                <input type="checkbox" name="remember" class="su-check-input"/>
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="su-link">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="su-btn-primary su-btn-block">
            Sign In
        </button>

    </form>

    <p class="su-form-note">
        Don't have an account?
        <a href="{{ route('register') }}" class="su-link su-link-strong">Register</a>
    </p>

    <div class="su-form-divider">
        <a href="{{ route('admin.login') }}" class="su-link-muted">
            Admin login →
        </a>
    </div>

@endsection