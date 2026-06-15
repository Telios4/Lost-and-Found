@extends('layouts.app')

@section('title', 'Report a Lost Item')

@section('content')

    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Report a Lost Item</h1>
        <p class="text-sm text-gray-500 mb-6">
            Fill in as much detail as possible to help us find a match.
        </p>

        <form method="POST"
              action="{{ route('reports.lost.store') }}"
              enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf

            <!-- Item Name -->
            <div>
                <label for="item_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Item Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="item_name"
                       name="item_name"
                       value="{{ old('item_name') }}"
                       placeholder="e.g. Black laptop bag"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('item_name') border-red-500 @enderror"/>
                @error('item_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                    Category <span class="text-red-500">*</span>
                </label>
                <select id="category"
                        name="category"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('category') border-red-500 @enderror">
                    <option value="">-- Select a category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          placeholder="Describe the item — colour, brand, distinguishing features..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-blue-500
                                 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                    Location Lost <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="location"
                       name="location"
                       value="{{ old('location') }}"
                       placeholder="e.g. Library, 2nd floor"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('location') border-red-500 @enderror"/>
                @error('location')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Lost -->
            <div>
                <label for="date_lost" class="block text-sm font-medium text-gray-700 mb-1">
                    Date Lost <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="date_lost"
                       name="date_lost"
                       value="{{ old('date_lost') }}"
                       max="{{ date('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('date_lost') border-red-500 @enderror"/>
                @error('date_lost')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo -->
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">
                    Photo <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input type="file"
                       id="photo"
                       name="photo"
                       accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0 file:text-sm file:font-medium
                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                              @error('photo') border-red-500 @enderror"/>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG or WEBP. Max 2MB.</p>
                @error('photo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800 transition">
                    Submit Report
                </button>
            </div>

        </form>
    </div>

@endsection