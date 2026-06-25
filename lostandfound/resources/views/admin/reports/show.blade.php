@extends('layouts.admin')

@section('title', 'Report Details')

@section('content')
    <div class="su-form-page su-detail-page">
        <a href="{{ route('admin.reports.index') }}" class="su-link su-detail-back">&larr; Back to Report Management</a>

        <article class="su-form-card su-detail-card">
            <div class="su-detail-head">
                <span class="su-badge {{ $type === 'lost' ? 'su-badge--active' : 'su-badge--resolved' }}">
                    {{ ucfirst($type) }} Item
                </span>
                <span class="su-badge {{ $report->status === 'active' ? 'su-badge--active' : 'su-badge--resolved' }}">
                    {{ ucfirst($report->status) }}
                </span>
            </div>

            <h1 class="su-page-title su-detail-title">{{ $report->item_name }}</h1>

            @if($report->photo_url)
                <img src="{{ Storage::url($report->photo_url) }}" alt="{{ $report->item_name }}" class="su-detail-image" />
            @endif

            <dl class="su-detail-grid">
                <div class="su-detail-row">
                    <dt class="su-detail-label">Category</dt>
                    <dd class="su-detail-value">{{ $report->category }}</dd>
                </div>
                <div class="su-detail-row">
                    <dt class="su-detail-label">Description</dt>
                    <dd class="su-detail-value">{{ $report->description }}</dd>
                </div>
                <div class="su-detail-row">
                    <dt class="su-detail-label">Location</dt>
                    <dd class="su-detail-value">{{ $report->location }}</dd>
                </div>
                <div class="su-detail-row">
                    <dt class="su-detail-label">{{ $type === 'lost' ? 'Date Lost' : 'Date Found' }}</dt>
                    <dd class="su-detail-value">{{ $type === 'lost' ? $report->date_lost : $report->date_found }}</dd>
                </div>
                <div class="su-detail-row">
                    <dt class="su-detail-label">Submitted By</dt>
                    <dd class="su-detail-value">{{ $report->user->name ?? 'Unknown' }}</dd>
                </div>
                <div class="su-detail-row">
                    <dt class="su-detail-label">Submitted At</dt>
                    <dd class="su-detail-value">{{ $report->created_at->format('d M Y, g:i a') }}</dd>
                </div>
            </dl>

            <div class="su-form-actions su-form-actions--start">
                @if($report->status === 'active')
                    <form method="POST" action="{{ route('admin.reports.resolve', ['type' => $type, 'id' => $report->id]) }}" class="su-inline-actions su-inline-actions--stack">
                        @csrf

                        @if($counterpartCandidates->isNotEmpty())
                            <div class="su-field">
                                <label for="counterpart_report_id" class="su-form-label">Matched {{ ucfirst($counterpartType) }} report (optional)</label>
                                <select name="counterpart_report_id" id="counterpart_report_id" class="su-form-input">
                                    <option value="">Resolve this item only</option>
                                    @foreach($counterpartCandidates as $candidate)
                                        <option value="{{ $candidate['report']->id }}">
                                            {{ $candidate['report']->item_name }} ({{ $candidate['score'] }}% match)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="su-field">
                            <label for="notes" class="su-form-label">Resolution notes (optional)</label>
                            <textarea name="notes" id="notes" class="su-form-input" rows="3" placeholder="Add closure details for auditing"></textarea>
                        </div>

                        <button type="submit" class="su-btn-primary">Mark as Resolved</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.reports.destroy', ['type' => $type, 'id' => $report->id]) }}" onsubmit="return confirm('Remove this submission? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="su-btn-danger">Remove Submission</button>
                </form>
            </div>
        </article>

        <section class="su-report-section">
            <h2 class="su-section-title">Potential {{ ucfirst($counterpartType) }} Matches</h2>

            @if($counterpartCandidates->isEmpty())
                <p class="su-empty-card">No strong match candidates for this report right now.</p>
            @else
                <div class="su-match-list">
                    @foreach($counterpartCandidates as $candidate)
                        <a href="{{ route('admin.reports.show', ['type' => $counterpartType, 'id' => $candidate['report']->id]) }}" class="su-match-item">
                            <div>
                                <p class="su-report-name">{{ $candidate['report']->item_name }}</p>
                                <p class="su-report-detail">{{ $candidate['report']->location }}</p>
                            </div>
                            <span class="su-confidence-badge">{{ $candidate['score'] }}% match</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
