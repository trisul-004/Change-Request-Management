@extends('layouts.app')

@section('title', 'Work on Task - ' . $changeRequest->title)

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Work on Task</h2>
            <p class="text-gray-600">{{ $changeRequest->title }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Task Details -->
        <div class="lg:col-span-1 flex flex-col h-full">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($changeRequest->status === 'in_progress') bg-purple-100 text-purple-800
                            @elseif($changeRequest->status === 'completed') bg-green-100 text-green-800
                            @elseif($changeRequest->status === 'on_hold') bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $changeRequest->status)) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Portal</label>
                        <p class="text-sm text-gray-900">{{ $changeRequest->portal->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client</label>
                        <p class="text-sm text-gray-900">{{ $changeRequest->client->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="text-sm text-gray-900">{{ $changeRequest->description }}</p>
                    </div>

                    @if($changeRequest->validation_notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supervisor's Notes</label>
                            <p class="text-sm text-gray-900 bg-blue-50 p-3 rounded">{{ $changeRequest->validation_notes }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created</label>
                        <p class="text-sm text-gray-900">{{ $changeRequest->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    @if($changeRequest->deadline)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deadline</label>
                            <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($changeRequest->deadline)->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Update Section -->
            <div class="bg-gray-50 p-6 rounded-lg mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>
                
                <form id="statusForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">New Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="in_progress" {{ $changeRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="on_hold" {{ $changeRequest->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ $changeRequest->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div>
                        <label for="status_notes" class="block text-sm font-medium text-gray-700">Notes for Status Change</label>
                        <textarea id="status_notes" name="notes" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Explain why you're changing the status..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Notes and Activity -->
        <div class="lg:col-span-2 flex flex-col h-full">
            <!-- Add Note Section -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Note</h3>
                
                <form id="noteForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="action_type" class="block text-sm font-medium text-gray-700">Note Type</label>
                        <select id="action_type" name="action_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="note">General Note</option>
                            <option value="milestone">Milestone</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="notes" name="notes" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Describe what you've done, any issues encountered, or progress made..."></textarea>
                    </div>

                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Add Note
                    </button>
                </form>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 h-80 flex flex-col">
                <div class="flex justify-end mb-2">
                    <a href="{{ route('change-requests.activity-timeline-pdf', $changeRequest) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700 transition">
                        Download PDF
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Timeline</h3>
                
                <div id="activityTimeline" class="space-y-4 flex-1 pr-2 border border-gray-100 rounded-md" style="overflow-y: auto; max-height: 300px; min-height: 50px;">
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const noteForm = document.getElementById('noteForm');
    const statusForm = document.getElementById('statusForm');
    const activityTimeline = document.getElementById('activityTimeline');

    // Handle note submission
    noteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(noteForm);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/change-requests/{{ $changeRequest->id }}/add-note`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                notes: formData.get('notes'),
                action_type: formData.get('action_type')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new note to timeline
                const noteHtml = `
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-sm font-medium text-gray-900">${data.note.developer_name}</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        ${data.note.action_type === 'milestone' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                                        ${data.note.action_type.charAt(0).toUpperCase() + data.note.action_type.slice(1)}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700">${data.note.notes}</p>
                                <p class="text-xs text-gray-500 mt-1">${data.note.created_at}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove empty message if it exists
                const emptyMessage = activityTimeline.querySelector('.text-center');
                if (emptyMessage) {
                    emptyMessage.remove();
                }
                
                // Add new note at the top
                activityTimeline.insertAdjacentHTML('afterbegin', noteHtml);
                
                // Clear form
                noteForm.reset();
                
                // Show success message
                showMessage('Note added successfully!', 'success');
            } else {
                showMessage(data.message || 'Error adding note', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error adding note. Please try again.', 'error');
        });
    });

    // Handle status update
    statusForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(statusForm);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/change-requests/{{ $changeRequest->id }}/update-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                status: formData.get('status'),
                notes: formData.get('notes')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update status display
                const statusSpan = document.querySelector('.text-xs.leading-5.font-semibold.rounded-full');
                if (statusSpan) {
                    statusSpan.textContent = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1).replace('_', ' ');
                    statusSpan.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' + 
                        (data.new_status === 'completed' ? 'bg-green-100 text-green-800' : 
                         data.new_status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 
                         'bg-purple-100 text-purple-800');
                }
                
                // Clear form
                statusForm.reset();
                
                // Show success message
                showMessage('Status updated successfully!', 'success');
                
                // Reload page to show new status change in timeline
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showMessage(data.message || 'Error updating status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error updating status. Please try again.', 'error');
        });
    });

    function showMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 px-4 py-3 rounded ${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'}`;
        messageDiv.textContent = message;
        
        const container = document.querySelector('.bg-white.shadow.rounded-lg.p-6');
        container.insertBefore(messageDiv, container.firstChild);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
});
</script>
@endpush
@endsection 