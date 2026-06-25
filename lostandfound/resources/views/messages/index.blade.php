@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <section class="su-page-head">
        <h1 class="su-page-title">Messages</h1>
        <p class="su-page-subtitle">Open a conversation from a report to contact another user.</p>
    </section>

    <section class="su-form-card su-list-panel">
        @if($conversations->isEmpty())
            <p class="su-empty-card">No conversations yet. Use a report page to start messaging.</p>
        @else
            <div class="su-list-stack">
                @foreach($conversations as $conversation)
                    <a href="{{ route('messages.show', $conversation['user']) }}" class="su-list-item su-list-item-link">
                        <div>
                            <p class="su-list-item-title">{{ $conversation['user']->name }}</p>
                            <p class="su-list-item-text">{{ \Illuminate\Support\Str::limit($conversation['latest_message']->body, 110) }}</p>
                            <p class="su-list-item-meta">{{ $conversation['latest_message']->created_at->diffForHumans() }}</p>
                        </div>

                        @if($conversation['unread_count'] > 0)
                            <span class="su-nav-pill">{{ $conversation['unread_count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
