@extends('layouts.app')

@section('title', 'Progress - ' . $changeRequest->title)

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Task Progress</h2>
            <p class="text-gray-600">{{ $changeRequest->title }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Task Details -->
        <div class="lg:col-span-1">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($changeRequest->status === 'in_progress') bg-purple-100 text-purple-800
                            @elseif($changeRequest->status === 'completed') bg-green-100 text-green-800
                            @elseif($changeRequest->status === 'on_hold') bg-yellow-100 text-yellow-800
                            @elseif($changeRequest->status === 'validated') bg-blue-100 text-blue-800
                            @elseif($changeRequest->status === 'approved') bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $changeRequest->status)) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client</label>
                        <p class="text-sm text-gray-900">{{ $changeRequest->client->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="text-sm text-gray-900">{{ $changeRequest->description }}</p>
                    </div>

                    @if($changeRequest->developer)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assigned Developer</label>
                            <p class="text-sm text-gray-900">{{ $changeRequest->developer->name }}</p>
                        </div>
                    @endif

                    @if($changeRequest->validation_notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Your Validation Notes</label>
                            <p class="text-sm text-gray-900 bg-blue-50 p-3 rounded">{{ $changeRequest->validation_notes }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created</label>
                        <p class="text-sm text-gray-900">{{ $changeRequest->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    @if($changeRequest->developerNotes->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Notes</label>
                            <p class="text-sm text-gray-900">{{ $changeRequest->developerNotes->count() }} note(s)</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex justify-end mb-2">
                    <a href="{{ route('change-requests.activity-timeline-pdf', $changeRequest) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700 transition">
                        Download PDF
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Developer Activity Timeline</h3>
                
                <div class="space-y-4" style="overflow-y: auto; max-height: 370px; min-height: 50px;">
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
                            No developer activity yet. The assigned developer hasn't added any notes.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 