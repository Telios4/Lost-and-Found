@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <section class="su-page-head">
        <h1 class="su-page-title">Notifications</h1>
        <p class="su-page-subtitle">Review potential matches and other account activity.</p>
    </section>

    <section class="su-form-card su-list-panel">
        <div class="su-inline-actions">
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="su-btn-outline">Mark all as read</button>
            </form>
        </div>

        @if($notifications->isEmpty())
            <p class="su-empty-card">No notifications available yet.</p>
        @else
            <div class="su-list-stack">
                @foreach($notifications as $notification)
                    @php
                        $data = is_array($notification->data) ? $notification->data : [];
                        $title = $data['title'] ?? 'Notification';
                        $message = $data['message'] ?? 'You have an update in the system.';
                        $url = $data['url'] ?? null;
                    @endphp

                    <article class="su-list-item {{ $notification->read_at ? '' : 'su-list-item--unread' }}">
                        <div>
                            <p class="su-list-item-title">{{ $title }}</p>
                            <p class="su-list-item-text">{{ $message }}</p>
                            <p class="su-list-item-meta">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        <div class="su-inline-actions">
                            @if($url)
                                <a href="{{ $url }}" class="su-btn-outline">Open</a>
                            @endif

                            @if(! $notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="su-btn-outline">Mark read</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="su-pagination-wrap">
                {{ $notifications->links() }}
            </div>
        @endif
    </section>
@endsection
