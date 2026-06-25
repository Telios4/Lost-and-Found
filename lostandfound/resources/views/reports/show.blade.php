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

        </article>
    </div>

@endsection