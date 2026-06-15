@extends('layouts.guest')

@section('title', 'Create Account')

@section('content')

    <h2 class="text-xl font-bold text-gray-800 mb-1">Create an account</h2>
    <p class="text-sm text-gray-500 mb-6">Use your Strathmore email to register.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Full Name
            </label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name') }}"
                   required autofocus
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          @error('name') border-red-500 @enderror"/>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

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
                   required
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
            <p class="text-xs text-gray-400 mt-1">
                Min. 8 characters, one uppercase, one number, one special character.
            </p>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm Password
            </label>
            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <button type="submit"
                class="w-full bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold
                       hover:bg-blue-800 transition">
            Create Account
        </button>

    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-700 font-medium hover:underline">Sign in</a>
    </p>

@endsection