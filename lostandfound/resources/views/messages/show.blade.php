@extends('layouts.app')

@section('title', 'Conversation')

@section('content')
    <section class="su-page-head">
        <h1 class="su-page-title">Conversation with {{ $otherUser->name }}</h1>
        <p class="su-page-subtitle">Use this channel to coordinate item return and verification.</p>
    </section>

    <section class="su-message-layout">
        <article class="su-form-card su-message-thread">
            @if($messages->isEmpty())
                <p class="su-empty-card">No messages yet. Start the conversation below.</p>
            @else
                <div class="su-message-stack">
                    @foreach($messages as $message)
                        <div class="su-message-bubble {{ $message->sender_id === auth()->id() ? 'su-message-bubble--mine' : 'su-message-bubble--theirs' }}">
                            @if($message->lostReport || $message->foundReport)
                                <p class="su-message-context">
                                    Related report:
                                    {{ $message->lostReport?->item_name ?? $message->foundReport?->item_name }}
                                </p>
                            @endif
                            <p>{{ $message->body }}</p>
                            <p class="su-message-time">{{ $message->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="su-form-card">
            <form method="POST" action="{{ route('messages.store', $otherUser) }}">
                @csrf
                <input type="hidden" name="context_type" value="{{ $contextType }}">
                <input type="hidden" name="context_id" value="{{ $contextId }}">

                <div class="su-form-group">
                    <label for="body" class="su-form-label">Message</label>
                    <textarea
                        id="body"
                        name="body"
                        rows="5"
                        class="su-form-input su-form-textarea @error('body') su-form-input--error @enderror"
                        placeholder="Write your message"
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <p class="su-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="su-form-actions su-form-actions--start">
                    <button type="submit" class="su-btn-primary">Send message</button>
                    <a href="{{ route('messages.index') }}" class="su-btn-outline">Back to inbox</a>
                </div>
            </form>
        </article>
    </section>
@endsection
