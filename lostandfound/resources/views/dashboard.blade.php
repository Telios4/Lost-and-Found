@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <section class="su-page-head">
        <h1 class="su-page-title">
            Welcome, {{ Auth::user()->name }} 👋
        </h1>
        <p class="su-page-subtitle">
            What would you like to do today?
        </p>
    </section>

    <section class="su-action-grid">

        <a href="{{ route('reports.lost.create') }}"
           class="su-action-card">
            <div class="su-action-icon">🔍</div>
            <h2 class="su-action-title">Report Lost Item</h2>
            <p class="su-action-text">Submit a report for something you have lost on campus.</p>
        </a>

        <a href="{{ route('reports.found.create') }}"
           class="su-action-card">
            <div class="su-action-icon">📦</div>
            <h2 class="su-action-title">Report Found Item</h2>
            <p class="su-action-text">Let the community know about an item you have found.</p>
        </a>

        <a href="{{ route('reports.my') }}"
           class="su-action-card">
            <div class="su-action-icon">📋</div>
            <h2 class="su-action-title">My Reports</h2>
            <p class="su-action-text">View and manage all your submitted reports.</p>
        </a>

    </section>
    

@endsection