@extends('layouts.admin')

@section('title', 'Manage Reports')

@section('content')
    <section class="su-page-head">
        <h1 class="su-page-title">Manage Reports</h1>
        <p class="su-page-subtitle">View report details, monitor activity status, and remove submissions.</p>
    </section>

    <section class="su-report-section">
        <h2 class="su-section-title">Lost Item Reports</h2>

        @if($lostReports->isEmpty())
            <p class="su-empty-card">No lost item submissions found.</p>
        @else
            <div class="su-list-stack">
                @foreach($lostReports as $report)
                    <div class="su-list-item">
                        <div>
                            <p class="su-list-item-title">{{ $report->item_name }}</p>
                            <p class="su-list-item-text">{{ $report->category }} · {{ $report->location }}</p>
                            <p class="su-list-item-meta">
                                Reporter: {{ $report->user->name ?? 'Unknown' }} · Submitted {{ $report->created_at->format('d M Y, g:i a') }}
                            </p>
                        </div>
                        <div class="su-inline-actions">
                            <span class="su-badge {{ $report->status === 'active' ? 'su-badge--active' : 'su-badge--resolved' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <a href="{{ route('admin.reports.show', ['type' => 'lost', 'id' => $report->id]) }}" class="su-btn-outline">Details</a>

                            <form method="POST" action="{{ route('admin.reports.destroy', ['type' => 'lost', 'id' => $report->id]) }}" onsubmit="return confirm('Remove this submission? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="su-btn-danger">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="su-pagination-wrap">
                {{ $lostReports->links() }}
            </div>
        @endif
    </section>

    <section class="su-report-section">
        <h2 class="su-section-title">Found Item Reports</h2>

        @if($foundReports->isEmpty())
            <p class="su-empty-card">No found item submissions found.</p>
        @else
            <div class="su-list-stack">
                @foreach($foundReports as $report)
                    <div class="su-list-item">
                        <div>
                            <p class="su-list-item-title">{{ $report->item_name }}</p>
                            <p class="su-list-item-text">{{ $report->category }} · {{ $report->location }}</p>
                            <p class="su-list-item-meta">
                                Reporter: {{ $report->user->name ?? 'Unknown' }} · Submitted {{ $report->created_at->format('d M Y, g:i a') }}
                            </p>
                        </div>
                        <div class="su-inline-actions">
                            <span class="su-badge {{ $report->status === 'active' ? 'su-badge--active' : 'su-badge--resolved' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <a href="{{ route('admin.reports.show', ['type' => 'found', 'id' => $report->id]) }}" class="su-btn-outline">Details</a>

                            <form method="POST" action="{{ route('admin.reports.destroy', ['type' => 'found', 'id' => $report->id]) }}" onsubmit="return confirm('Remove this submission? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="su-btn-danger">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="su-pagination-wrap">
                {{ $foundReports->links() }}
            </div>
        @endif
    </section>
@endsection
