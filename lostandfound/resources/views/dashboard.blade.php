@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <style>
        .su-dashboard-page {
            width: 100%;
            max-width: 44rem;
            margin-inline: auto;
            text-align: center;
        }

        .su-dashboard-page .su-action-grid {
            grid-template-columns: 1fr;
            justify-items: center;
            gap: 0.85rem;
        }

        .su-dashboard-page .su-action-card {
            width: 100%;
            max-width: 34rem;
            text-align: left;
        }
    </style>

    <div class="su-dashboard-page">

    <section class="su-page-head">
        <h1 class="su-page-title">
            Welcome, {{ Auth::user()->name }}
        </h1>
        <p class="su-page-subtitle">
            What would you like to do today?
        </p>
    </section>

    <section class="su-action-grid">

        <a href="{{ route('reports.lost.create') }}"
           class="su-action-card">
            <h2 class="su-action-title">Report Lost Item</h2>
            <p class="su-action-text">Submit a report for something you have lost on campus.</p>
        </a>

        <a href="{{ route('reports.found.create') }}"
           class="su-action-card">
            <h2 class="su-action-title">Report Found Item</h2>
            <p class="su-action-text">Let the community know about an item you have found.</p>
        </a>

        <a href="{{ route('reports.my') }}"
           class="su-action-card">
            <h2 class="su-action-title">My Reports</h2>
            <p class="su-action-text">View and manage all your submitted reports.</p>
        </a>

        <a href="{{ route('reports.search') }}"
           class="su-action-card">
            <h2 class="su-action-title">Search &amp; Match</h2>
            <p class="su-action-text">Search active reports and detect likely lost-to-found matches.</p>
        </a>

    </section>

    </div>

@endsection