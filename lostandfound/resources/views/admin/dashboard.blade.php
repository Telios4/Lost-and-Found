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
            <a href="{{ route('admin.reports.index') }}" class="su-action-card">
                <h3 class="su-action-title">Manage Submissions</h3>
                <p class="su-action-text">View report details, track activity status, and remove submissions.</p>
            </a>

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

    <section class="su-report-section">
        <div class="su-report-section-head">
            <h2 class="su-section-title">Recent Lost Reports</h2>
            <a href="{{ route('admin.reports.index') }}" class="su-link">View all</a>
        </div>

        @if($recentLostReports->isEmpty())
            <p class="su-empty-card">No lost item submissions yet.</p>
        @else
            <div class="su-list-stack">
                @foreach($recentLostReports as $report)
                    <div class="su-list-item">
                        <div>
                            <p class="su-list-item-title">{{ $report->item_name }}</p>
                            <p class="su-list-item-text">{{ $report->category }} · {{ $report->location }}</p>
                            <p class="su-list-item-meta">Reporter: {{ $report->user->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="su-inline-actions">
                            <span class="su-badge {{ $report->status === 'active' ? 'su-badge--active' : 'su-badge--resolved' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <a href="{{ route('admin.reports.show', ['type' => 'lost', 'id' => $report->id]) }}" class="su-btn-outline">Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="su-report-section">
        <h2 class="su-section-title">Recent Found Reports</h2>

        @if($recentFoundReports->isEmpty())
            <p class="su-empty-card">No found item submissions yet.</p>
        @else
            <div class="su-list-stack">
                @foreach($recentFoundReports as $report)
                    <div class="su-list-item">
                        <div>
                            <p class="su-list-item-title">{{ $report->item_name }}</p>
                            <p class="su-list-item-text">{{ $report->category }} · {{ $report->location }}</p>
                            <p class="su-list-item-meta">Reporter: {{ $report->user->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="su-inline-actions">
                            <span class="su-badge {{ $report->status === 'active' ? 'su-badge--active' : 'su-badge--resolved' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <a href="{{ route('admin.reports.show', ['type' => 'found', 'id' => $report->id]) }}" class="su-btn-outline">Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
