@extends('layouts.app')

@section('title', 'My Reports')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">My Reports</h1>

    <!-- Lost Reports -->
    <div class="mb-10">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">
            Lost Items
            <span class="ml-2 text-sm font-normal text-gray-400">({{ $lostReports->count() }})</span>
        </h2>

        @if($lostReports->isEmpty())
            <p class="text-sm text-gray-400 bg-white border border-gray-200 rounded-xl p-6 text-center">
                You have not submitted any lost item reports yet.
                <a href="{{ route('reports.lost.create') }}" class="text-blue-700 hover:underline ml-1">Report one now</a>.
            </p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($lostReports as $report)
                    <a href="{{ route('reports.show', ['type' => 'lost', 'id' => $report->id]) }}"
                       class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $report->status === 'active' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $report->date_lost }}</span>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm mb-1">{{ $report->item_name }}</p>
                        <p class="text-xs text-gray-500">{{ $report->category }} · {{ $report->location }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Found Reports -->
    <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-3">
            Found Items
            <span class="ml-2 text-sm font-normal text-gray-400">({{ $foundReports->count() }})</span>
        </h2>

        @if($foundReports->isEmpty())
            <p class="text-sm text-gray-400 bg-white border border-gray-200 rounded-xl p-6 text-center">
                You have not submitted any found item reports yet.
                <a href="{{ route('reports.found.create') }}" class="text-green-700 hover:underline ml-1">Report one now</a>.
            </p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($foundReports as $report)
                    <a href="{{ route('reports.show', ['type' => 'found', 'id' => $report->id]) }}"
                       class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $report->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $report->date_found }}</span>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm mb-1">{{ $report->item_name }}</p>
                        <p class="text-xs text-gray-500">{{ $report->category }} · {{ $report->location }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

@endsection