@extends('layouts.app')

@section('title', $report->item_name)

@section('content')

    <div class="max-w-2xl mx-auto">

        <a href="{{ route('reports.my') }}"
           class="text-sm text-blue-700 hover:underline mb-4 inline-block">← Back to My Reports</a>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">

            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-medium px-3 py-1 rounded-full
                    {{ $type === 'lost' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                    {{ ucfirst($type) }} Item
                </span>
                <span class="text-xs font-medium px-3 py-1 rounded-full
                    {{ $report->status === 'active' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($report->status) }}
                </span>
            </div>

            <h1 class="text-xl font-bold text-gray-800 mb-4">{{ $report->item_name }}</h1>

            @if($report->photo_url)
                <img src="{{ Storage::url($report->photo_url) }}"
                     alt="{{ $report->item_name }}"
                     class="w-full h-52 object-cover rounded-lg mb-4"/>
            @endif

            <dl class="space-y-3 text-sm">
                <div class="flex gap-2">
                    <dt class="font-medium text-gray-500 w-28 shrink-0">Category</dt>
                    <dd class="text-gray-800">{{ $report->category }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="font-medium text-gray-500 w-28 shrink-0">Description</dt>
                    <dd class="text-gray-800">{{ $report->description }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="font-medium text-gray-500 w-28 shrink-0">Location</dt>
                    <dd class="text-gray-800">{{ $report->location }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="font-medium text-gray-500 w-28 shrink-0">
                        {{ $type === 'lost' ? 'Date Lost' : 'Date Found' }}
                    </dt>
                    <dd class="text-gray-800">
                        {{ $type === 'lost' ? $report->date_lost : $report->date_found }}
                    </dd>
                </div>
                <div class="flex gap-2">
                    <dt class="font-medium text-gray-500 w-28 shrink-0">Submitted</dt>
                    <dd class="text-gray-800">{{ $report->created_at->format('d M Y, g:i a') }}</dd>
                </div>
            </dl>

        </div>
    </div>

@endsection