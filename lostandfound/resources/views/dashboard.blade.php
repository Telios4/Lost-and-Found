@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Welcome, {{ Auth::user()->name }} 👋
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            What would you like to do today?
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

        <a href="{{ route('reports.lost.create') }}"
           class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition group">
            <div class="text-3xl mb-3">🔍</div>
            <h2 class="font-semibold text-gray-800 group-hover:text-blue-700">Report Lost Item</h2>
            <p class="text-sm text-gray-500 mt-1">Submit a report for something you have lost on campus.</p>
        </a>

        <a href="{{ route('reports.found.create') }}"
           class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition group">
            <div class="text-3xl mb-3">📦</div>
            <h2 class="font-semibold text-gray-800 group-hover:text-green-700">Report Found Item</h2>
            <p class="text-sm text-gray-500 mt-1">Let the community know about an item you have found.</p>
        </a>

        <a href="{{ route('reports.my') }}"
           class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition group">
            <div class="text-3xl mb-3">📋</div>
            <h2 class="font-semibold text-gray-800 group-hover:text-purple-700">My Reports</h2>
            <p class="text-sm text-gray-500 mt-1">View and manage all your submitted reports.</p>
        </a>

    </div>
    

@endsection