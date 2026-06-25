@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')

    <h2 class="su-form-title">Set a new password</h2>
    <p class="su-form-subtitle">Choose a secure password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="su-form-group">
            <label for="email" class="su-form-label">
                Strathmore Email
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   required autofocus autocomplete="username"
                   class="su-form-input @error('email') su-form-input--error @enderror"/>
            @error('email')
                <p class="su-form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-form-group">
            <label for="password" class="su-form-label">
                New Password
            </label>
            <input type="password"
                   id="password"
                   name="password"
                   required autocomplete="new-password"
                   class="su-form-input @error('password') su-form-input--error @enderror"/>
            @error('password')
                <p class="su-form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-form-group">
            <label for="password_confirmation" class="su-form-label">
                Confirm Password
            </label>

            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   required autocomplete="new-password"
                   class="su-form-input @error('password_confirmation') su-form-input--error @enderror"/>
            @error('password_confirmation')
                <p class="su-form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="su-btn-primary su-btn-block">
            Reset Password
        </button>
    </form>

@endsection
