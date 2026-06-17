@extends('layouts.guest')

@section('title', 'Create Account')

@section('content')

    <h2 class="su-form-title">Create an account</h2>
    <p class="su-form-subtitle">Use your Strathmore email to register.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="su-form-group">
            <label for="name" class="su-form-label">
                Full Name
            </label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name') }}"
                   required autofocus
                   class="su-form-input @error('name') su-form-input--error @enderror"/>
            @error('name')
                <p class="su-form-error">{{ $message }}</p>
            @enderror
        </div>

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
                   required
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
            <p class="su-auth-footer" style="text-align:left; margin-top:0.25rem; margin-bottom:0;">
                Min. 8 characters, one uppercase, one number, one special character.
            </p>
            @error('password')
                <p class="su-form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="su-form-group">
            <label for="password_confirmation" class="su-form-label">
                Confirm Password
            </label>
            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   required
                   class="su-form-input"/>
        </div>

        <button type="submit" class="su-btn-primary su-btn-block">
            Create Account
        </button>

    </form>

    <p class="su-form-note">
        Already have an account?
        <a href="{{ route('login') }}" class="su-link su-link-strong">Sign in</a>
    </p>

@endsection