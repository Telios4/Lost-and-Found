@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <section class="su-page-head">
        <h1 class="su-page-title">Admin Dashboard</h1>
        <p class="su-page-subtitle">Overview of active and resolved lost and found reports.</p>
    </section>

    <section class="su-stats-grid">
        <article class="su-stat-card">
            <p class="su-stat-label">Total Lost Reports</p>
            <p class="su-stat-value">{{ $totalLost }}</p>
        </article>

        <article class="su-stat-card">
            <p class="su-stat-label">Total Found Reports</p>
            <p class="su-stat-value">{{ $totalFound }}</p>
        </article>

        <article class="su-stat-card">
            <p class="su-stat-label">Active Lost</p>
            <p class="su-stat-value">{{ $activeLost }}</p>
        </article>

        <article class="su-stat-card">
            <p class="su-stat-label">Active Found</p>
            <p class="su-stat-value">{{ $activeFound }}</p>
        </article>

        <article class="su-stat-card su-stat-card--accent">
            <p class="su-stat-label">Resolved Cases</p>
            <p class="su-stat-value">{{ $resolvedCount }}</p>
        </article>
    </section>

    <section class="su-report-section">
        <h2 class="su-section-title">Admin Actions</h2>

        <div class="su-action-grid">
            <a href="{{ route('admin.reports.lost.create') }}" class="su-action-card">
                <h3 class="su-action-title">Post Lost Item</h3>
                <p class="su-action-text">Create a lost-item listing from the admin module.</p>
            </a>

            <a href="{{ route('admin.reports.found.create') }}" class="su-action-card">
                <h3 class="su-action-title">Post Found Item</h3>
                <p class="su-action-text">Create a found-item listing from the admin module.</p>
            </a>
        </div>
    </section>
@endsection
