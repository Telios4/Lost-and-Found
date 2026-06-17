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
@endsection
