@extends('layouts.admin')

@section('title', 'Admin: Post Found Item')

@section('content')
    <div class="su-form-page">
        <h1 class="su-page-title">Post Found Item</h1>
        <p class="su-page-subtitle">Create a found item listing from the admin module.</p>

        <form method="POST" action="{{ route('admin.reports.found.store') }}" enctype="multipart/form-data" class="su-form-card">
            @csrf

            <div class="su-form-group">
                <label for="item_name" class="su-form-label">Item Name <span class="su-required">*</span></label>
                <input type="text" id="item_name" name="item_name" value="{{ old('item_name') }}" class="su-form-input @error('item_name') su-form-input--error @enderror" />
                @error('item_name')
                    <p class="su-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-group">
                <label for="category" class="su-form-label">Category <span class="su-required">*</span></label>
                <select id="category" name="category" class="su-form-input @error('category') su-form-input--error @enderror">
                    <option value="">-- Select a category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="su-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-group">
                <label for="description" class="su-form-label">Description <span class="su-required">*</span></label>
                <textarea id="description" name="description" rows="4" class="su-form-input su-form-textarea @error('description') su-form-input--error @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="su-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-group">
                <label for="location" class="su-form-label">Location Found <span class="su-required">*</span></label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" class="su-form-input @error('location') su-form-input--error @enderror" />
                @error('location')
                    <p class="su-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-group">
                <label for="date_found" class="su-form-label">Date Found <span class="su-required">*</span></label>
                <input type="date" id="date_found" name="date_found" value="{{ old('date_found') }}" max="{{ date('Y-m-d') }}" class="su-form-input @error('date_found') su-form-input--error @enderror" />
                @error('date_found')
                    <p class="su-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-group">
                <label for="photo" class="su-form-label">Photo <span class="su-form-help">(optional)</span></label>
                <input type="file" id="photo" name="photo" accept="image/*" class="su-form-input @error('photo') su-form-input--error @enderror" />
                @error('photo')
                    <p class="su-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-actions">
                <a href="{{ route('admin.dashboard') }}" class="su-btn-outline">Cancel</a>
                <button type="submit" class="su-btn-primary">Post Found Item</button>
            </div>
        </form>
    </div>
@endsection
