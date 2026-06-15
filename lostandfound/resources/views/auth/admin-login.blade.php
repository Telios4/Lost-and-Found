@extends('layouts.guest')

@section('title', 'Admin Login')

@section('content')

    <h2 class="text-xl font-bold text-gray-800 mb-1">Administrator Login</h2>
    <p class="text-sm text-gray-500 mb-6">Restricted to system administrators only.</p>

    <form method="POST" action="{{ route('admin.login.store') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Admin Email
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   required autofocus
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          @error('email') border-red-500 @enderror"/>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-6">
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

        <button type="submit"
                class="w-full bg-red-700 text-white py-2 rounded-lg text-sm font-semibold
                       hover:bg-red-800 transition">
            Sign In as Admin
        </button>

    </form>

    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-blue-700">
            ← Back to user login
        </a>
    </div>

@endsection