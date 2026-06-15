@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')

    <h2 class="text-xl font-bold text-gray-800 mb-1">Welcome back</h2>
    <p class="text-sm text-gray-500 mb-6">Sign in to your Strathmore account.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Strathmore Email
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="you@strathmore.edu"
                   required autofocus
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          @error('email') border-red-500 @enderror"/>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password
            </label>
            <input type="password"
                   id="password"
                   name="password"
                   required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          @error('password') border-red-500 @enderror"/>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300"/>
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-blue-700 hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold
                       hover:bg-blue-800 transition">
            Sign In
        </button>

    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-blue-700 font-medium hover:underline">Register</a>
    </p>

    <div class="border-t border-gray-200 mt-6 pt-4 text-center">
        <a href="{{ route('admin.login') }}" class="text-xs text-gray-400 hover:text-blue-700">
            Admin login →
        </a>
    </div>

@endsection