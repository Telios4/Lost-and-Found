@extends('layouts.guest')

@section('title', 'Admin Login')

@section('content')

    <h2 class="su-form-title">Administrator Login</h2>
    <p class="su-form-subtitle">Restricted to system administrators only.</p>

    <form method="POST" action="{{ route('admin.login.store') }}">
        @csrf

        <!-- Email -->
        <div class="su-form-group">
            <label for="email" class="su-form-label">
                Admin Email
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
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

        <button type="submit" class="su-btn-primary su-btn-block">
            Sign In as Admin
        </button>

    </form>

    <div class="su-form-note">
        <a href="{{ route('login') }}" class="su-link-muted">
            ← Back to user login
        </a>
    </div>

@endsection