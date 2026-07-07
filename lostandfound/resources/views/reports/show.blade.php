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

            @php
                $photoPath = $report->photo_url
                    ? asset('storage/' . ltrim($report->photo_url, '/'))
                    : null;
            @endphp

            @if($photoPath)
                <img src="{{ $photoPath }}"
                     alt="{{ $report->item_name }}"
                     class="su-detail-image"
                     loading="lazy" />
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

            @php
                $isAdminManagedFoundReport = $type === 'found' && ! empty($report->reviewed_by);
            @endphp

            @if(! $isOwner && $report->user)
                @if($isAdminManagedFoundReport)
                    <div class="su-info-card">
                        <p class="su-info-card-title">Posted by Lost and Found Admin Team</p>
                        <p class="su-info-card-text">This item is managed by the admin office. Please visit the lost and found security office in Central Building Basement to claim your item.</p>
                    </div>
                @elseif(! $canMessageReporter)
                    <div class="su-info-card">
                        <p class="su-info-card-title">Messaging unavailable</p>
                        <p class="su-info-card-text">You can only message the reporter when this report has a possible match with one of your active reports.</p>
                    </div>
                @else
                    <div class="su-form-actions su-form-actions--start">
                        <a
                            href="{{ route('messages.show', ['user' => $report->user->id, 'context_type' => $type, 'context_id' => $report->id]) }}"
                            class="su-btn-primary"
                        >
                            Message Reporter
                        </a>
                    </div>
                @endif
            @endif

            @if($isOwner)
                <div class="su-form-actions su-form-actions--start">
                    @if($report->status === 'active')
                        <form method="POST" action="{{ route('reports.resolve', ['type' => $type, 'id' => $report->id]) }}" class="su-inline-actions su-inline-actions--stack">
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
                                <textarea name="notes" id="notes" class="su-form-input" rows="3" placeholder="Add closure details"></textarea>
                            </div>

                            <button type="submit" class="su-btn-primary">Mark as Resolved</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('reports.destroy', ['type' => $type, 'id' => $report->id]) }}" onsubmit="return confirm('Remove this submission? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="su-btn-danger">Remove Submission</button>
                    </form>
                </div>
            @endif

        </article>

        @if($isOwner)
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
        @endif
    </div>

@endsection