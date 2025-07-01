@extends('layouts.app')

@section('title', 'Activity Timeline - ' . $changeRequest->title)

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Full Activity Timeline</h2>
            <p class="text-gray-600">Task: {{ $changeRequest->title }}</p>
        </div>
        <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back
        </a>
    </div>
    <div class="mb-4">
        <div class="text-sm text-gray-700 mb-1"><strong>Supervisor:</strong> {{ $supervisor ? $supervisor->name : 'N/A' }}</div>
        <div class="text-sm text-gray-700 mb-1"><strong>Developer:</strong> {{ $developer ? $developer->name : 'N/A' }}</div>
        <div class="text-sm text-gray-700 mb-1"><strong>Description:</strong> {{ $changeRequest->description }}</div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 max-h-[500px] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">All Audits & Progress</h3>
        <div class="space-y-4">
            @forelse($developerNotes as $note)
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ $note->developer->name }}</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($note->action_type === 'status_change') bg-purple-100 text-purple-800
                                    @elseif($note->action_type === 'milestone') bg-green-100 text-green-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $note->action_type)) }}
                                </span>
                                @if($note->action_type === 'status_change')
                                    <span class="text-xs text-gray-500">
                                        {{ ucfirst($note->status_before) }} → {{ ucfirst($note->status_after) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700">{{ $note->notes }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $note->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">
                    No activity yet. Add your first note to get started!
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection 