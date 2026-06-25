@extends('layouts.app')

@section('title', 'Search & Matches')

@section('content')

    <section class="su-page-head">
        <h1 class="su-page-title">Search &amp; Match Detection</h1>
        <p class="su-page-subtitle">Search active reports and review likely lost-to-found matches.</p>
    </section>

    <section class="su-search-panel">
        <form method="GET" action="{{ route('reports.search') }}" class="su-form-card su-search-form">
            <div class="su-search-form-grid">
                <div class="su-form-group">
                    <label for="q" class="su-form-label">Search term</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        class="su-form-input"
                        placeholder="Item name, location, or description"
                        value="{{ $term }}"
                    />
                </div>

                <div class="su-form-group">
                    <label for="type" class="su-form-label">Report type</label>
                    <select id="type" name="type" class="su-form-input">
                        <option value="all" {{ $selectedType === 'all' ? 'selected' : '' }}>All</option>
                        <option value="lost" {{ $selectedType === 'lost' ? 'selected' : '' }}>Lost only</option>
                        <option value="found" {{ $selectedType === 'found' ? 'selected' : '' }}>Found only</option>
                    </select>
                </div>

                <div class="su-form-group">
                    <label for="category" class="su-form-label">Category</label>
                    <select id="category" name="category" class="su-form-input">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ $selectedCategory === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="su-form-actions su-form-actions--start">
                <button type="submit" class="su-btn-primary">Run Search</button>
                <a href="{{ route('reports.search') }}" class="su-btn-outline">Reset</a>
            </div>
        </form>
    </section>

    <section class="su-report-section">
        <h2 class="su-section-title">
            Lost Reports
            <span class="su-section-count">({{ $lostResults->count() }})</span>
        </h2>

        @if($lostResults->isEmpty())
            <p class="su-empty-card">No lost reports matched your current filters.</p>
        @else
            <div class="su-report-grid">
                @foreach($lostResults as $report)
                    <a href="{{ route('reports.show', ['type' => 'lost', 'id' => $report->id]) }}" class="su-report-card">
                        <div class="su-report-meta">
                            <span class="su-badge su-badge--active">Lost</span>
                            <span class="su-report-date">{{ $report->date_lost }}</span>
                        </div>
                        <p class="su-report-name">{{ $report->item_name }}</p>
                        <p class="su-report-detail">{{ $report->category }} · {{ $report->location }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="su-report-section">
        <h2 class="su-section-title">
            Found Reports
            <span class="su-section-count">({{ $foundResults->count() }})</span>
        </h2>

        @if($foundResults->isEmpty())
            <p class="su-empty-card">No found reports matched your current filters.</p>
        @else
            <div class="su-report-grid">
                @foreach($foundResults as $report)
                    <a href="{{ route('reports.show', ['type' => 'found', 'id' => $report->id]) }}" class="su-report-card">
                        <div class="su-report-meta">
                            <span class="su-badge su-badge--resolved">Found</span>
                            <span class="su-report-date">{{ $report->date_found }}</span>
                        </div>
                        <p class="su-report-name">{{ $report->item_name }}</p>
                        <p class="su-report-detail">{{ $report->category }} · {{ $report->location }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="su-report-section">
        <h2 class="su-section-title">Potential Matches</h2>

        @if($matches->isEmpty())
            <p class="su-empty-card">No strong match candidates were detected from the current result set.</p>
        @else
            <div class="su-match-stack">
                @foreach($matches as $matchSet)
                    <article class="su-match-card">
                        <div class="su-match-head">
                            <h3 class="su-match-title">Lost: {{ $matchSet['lost']->item_name }}</h3>
                            <span class="su-match-meta">{{ $matchSet['lost']->category }} · {{ $matchSet['lost']->location }}</span>
                        </div>

                        <div class="su-match-list">
                            @foreach($matchSet['candidates'] as $candidate)
                                <a
                                    href="{{ route('reports.show', ['type' => 'found', 'id' => $candidate['report']->id]) }}"
                                    class="su-match-item"
                                >
                                    <div>
                                        <p class="su-report-name">{{ $candidate['report']->item_name }}</p>
                                        <p class="su-report-detail">{{ $candidate['report']->location }} · {{ $candidate['report']->date_found }}</p>
                                    </div>
                                    <span class="su-confidence-badge">{{ $candidate['score'] }}% match</span>
                                </a>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

@endsection
