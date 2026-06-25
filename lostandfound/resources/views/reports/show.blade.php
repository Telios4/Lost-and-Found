@extends('layouts.app')

@section('title', $report->item_name)

@section('content')

    <div class="su-form-page su-detail-page">

        <a href="{{ route('reports.my') }}" class="su-link su-detail-back">&larr; Back to My Reports</a>

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
                <img src="{{ Storage::url($report->photo_url) }}"
                     alt="{{ $report->item_name }}"
                     class="su-detail-image"/>
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
                    <dt class="su-detail-label">Submitted</dt>
                    <dd class="su-detail-value">{{ $report->created_at->format('d M Y, g:i a') }}</dd>
                </div>
            </dl>

            @if(! $isOwner && $report->user)
                <div class="su-form-actions su-form-actions--start">
                    <a
                        href="{{ route('messages.show', ['user' => $report->user->id, 'context_type' => $type, 'context_id' => $report->id]) }}"
                        class="su-btn-primary"
                    >
                        Message Reporter
                    </a>
                </div>
            @endif

        </article>

        <section class="su-report-section">
            <h2 class="su-section-title">Potential {{ ucfirst($counterpartType) }} Matches</h2>

            @if($counterpartCandidates->isEmpty())
                <p class="su-empty-card">No strong match candidates for this report right now.</p>
            @else
                <div class="su-match-list">
                    @foreach($counterpartCandidates as $candidate)
                        <a
                            href="{{ route('reports.show', ['type' => $counterpartType, 'id' => $candidate['report']->id]) }}"
                            class="su-match-item"
                        >
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