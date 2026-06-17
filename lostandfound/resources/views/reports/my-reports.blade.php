@extends('layouts.app')

@section('title', 'My Reports')

@section('content')

    <section class="su-page-head">
        <h1 class="su-page-title">My Reports</h1>
        <p class="su-page-subtitle">Track your lost and found submissions in one place.</p>
    </section>

    <!-- Lost Reports -->
    <div class="su-report-section">
        <h2 class="su-section-title">
            Lost Items
            <span class="su-section-count">({{ $lostReports->count() }})</span>
        </h2>

        @if($lostReports->isEmpty())
            <p class="su-empty-card">
                You have not submitted any lost item reports yet.
                <a href="{{ route('reports.lost.create') }}" class="su-link su-link-strong">Report one now</a>.
            </p>
        @else
            <div class="su-report-grid">
                @foreach($lostReports as $report)
                    <a href="{{ route('reports.show', ['type' => 'lost', 'id' => $report->id]) }}"
                       class="su-report-card">
                        <div class="su-report-meta">
                            <span class="su-badge {{ $report->status === 'active' ? 'su-badge--active' : 'su-badge--resolved' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <span class="su-report-date">{{ $report->date_lost }}</span>
                        </div>
                        <p class="su-report-name">{{ $report->item_name }}</p>
                        <p class="su-report-detail">{{ $report->category }} · {{ $report->location }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Found Reports -->
    <div>
        <h2 class="su-section-title">
            Found Items
            <span class="su-section-count">({{ $foundReports->count() }})</span>
        </h2>

        @if($foundReports->isEmpty())
            <p class="su-empty-card">
                You have not submitted any found item reports yet.
                <a href="{{ route('reports.found.create') }}" class="su-link su-link-strong">Report one now</a>.
            </p>
        @else
            <div class="su-report-grid">
                @foreach($foundReports as $report)
                    <a href="{{ route('reports.show', ['type' => 'found', 'id' => $report->id]) }}"
                       class="su-report-card">
                        <div class="su-report-meta">
                            <span class="su-badge {{ $report->status === 'active' ? 'su-badge--active' : 'su-badge--resolved' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <span class="su-report-date">{{ $report->date_found }}</span>
                        </div>
                        <p class="su-report-name">{{ $report->item_name }}</p>
                        <p class="su-report-detail">{{ $report->category }} · {{ $report->location }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

@endsection