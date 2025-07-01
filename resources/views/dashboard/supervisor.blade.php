@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Supervisor Dashboard</h2>
        <a href="{{ route('team.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
            </svg>
            Manage Team
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-yellow-800">Pending Approval</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $pendingApproval }}</p>
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-green-800">My Approved</h3>
            <p class="text-3xl font-bold text-green-600">{{ $approved }}</p>
        </div>
        <div class="bg-red-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-red-800">Rejected</h3>
            <p class="text-3xl font-bold text-red-600">{{ $rejected }}</p>
        </div>
        <div class="bg-blue-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800">Team Members</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $teamMembers }}</p>
        </div>
    </div>

    <div x-data="{ tab: 'pending' }" class="mt-8">
        <div class="flex justify-center mb-8">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button @click="tab = 'pending'"
                    :class="tab === 'pending' ? 'bg-indigo-600 text-white shadow font-semibold' : 'bg-white text-gray-700 border border-gray-300 hover:bg-indigo-50 hover:text-indigo-700'"
                    class="px-5 py-2 rounded-full transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Pending Requests
                </button>
                <button @click="tab = 'approved'"
                    :class="tab === 'approved' ? 'bg-green-600 text-white shadow font-semibold' : 'bg-white text-gray-700 border border-gray-300 hover:bg-green-50 hover:text-green-700'"
                    class="px-5 py-2 rounded-full transition focus:outline-none focus:ring-2 focus:ring-green-500">
                    Approved Requests
                </button>
                <button @click="tab = 'rejected'"
                    :class="tab === 'rejected' ? 'bg-red-600 text-white shadow font-semibold' : 'bg-white text-gray-700 border border-gray-300 hover:bg-red-50 hover:text-red-700'"
                    class="px-5 py-2 rounded-full transition focus:outline-none focus:ring-2 focus:ring-red-500">
                    Rejected Requests
                </button>
                <button @click="tab = 'activity'"
                    :class="tab === 'activity' ? 'bg-blue-600 text-white shadow font-semibold' : 'bg-white text-gray-700 border border-gray-300 hover:bg-blue-50 hover:text-blue-700'"
                    class="px-5 py-2 rounded-full transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Developer Activity
                </button>
            </nav>
        </div>

        <!-- Pending Requests Tab -->
        <div x-show="tab === 'pending'" class="space-y-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Pending Requests</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Requests waiting for your approval</p>
                </div>
                <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    @forelse($pendingRequests as $request)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $request->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ Str::limit($request->description, 100) }}</p>
                                    <div class="mt-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('change-requests.review', $request) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                        Review
                                    </a>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4">
                            <div class="text-center text-gray-500">No pending requests available</div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Approved Requests Tab -->
        <div x-show="tab === 'approved'" class="space-y-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">My Approved Requests</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Requests that you have approved</p>
                </div>
                <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    @forelse($myApprovedRequests as $request)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $request->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ Str::limit($request->description, 100) }}</p>
                                    <div class="mt-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($request->status === 'approved') bg-blue-100 text-blue-800
                                            @elseif($request->status === 'in_progress') bg-purple-100 text-purple-800
                                            @elseif($request->status === 'completed') bg-green-100 text-green-800
                                            @elseif($request->status === 'on_hold') bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </div>
                                    @if($request->validation_notes)
                                        <div class="mt-2 bg-blue-50 p-3 rounded-md">
                                            <h4 class="text-sm font-medium text-blue-800">Your Notes:</h4>
                                            <p class="text-sm text-blue-600">{{ $request->validation_notes }}</p>
                                        </div>
                                    @endif
                                    @if($request->developer)
                                        <div class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium">Assigned to:</span> {{ $request->developer->name }}
                                        </div>
                                    @endif
                                    @if($request->deadline)
                                        <div class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium">Deadline:</span> {{ \Carbon\Carbon::parse($request->deadline)->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 space-y-2">
                                    @if($request->status === 'approved' && !$request->developer_id)
                                        <button onclick="openAssignModal({{ $request->id }})" 
                                                class="w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 mb-2">
                                            Assign Developer
                                        </button>
                                    @endif
                                    @if($request->developer_id)
                                        <a href="{{ route('change-requests.progress', $request) }}" 
                                           class="block text-center w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                            View Progress
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4">
                            <div class="text-center text-gray-500">No approved requests available</div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Rejected Requests Tab -->
        <div x-show="tab === 'rejected'" class="space-y-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Rejected Requests</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Requests that have been rejected</p>
                </div>
                <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    @php
                        $rejectedRequests = \App\Models\ChangeRequest::where('validated_by', auth()->id())
                            ->where('status', 'rejected')
                            ->latest()
                            ->get();
                    @endphp
                    @forelse($rejectedRequests as $request)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $request->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ Str::limit($request->description, 100) }}</p>
                                    <div class="mt-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                    @if($request->validation_notes)
                                        <div class="mt-2 bg-red-50 p-3 rounded-md">
                                            <h4 class="text-sm font-medium text-red-800">Rejection Notes:</h4>
                                            <p class="text-sm text-red-600">{{ $request->validation_notes }}</p>
                                        </div>
                                    @endif
                                    @if($request->deadline)
                                        <div class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium">Deadline:</span> {{ \Carbon\Carbon::parse($request->deadline)->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4">
                            <div class="text-center text-gray-500">No rejected requests available</div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Developer Activity Tab -->
        <div x-show="tab === 'activity'" class="space-y-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Developer Activity</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest updates from your team members</p>
                </div>
                <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    @php
                        $recentNotes = \App\Models\DeveloperNote::whereHas('changeRequest', function($query) {
                            $query->where('validated_by', auth()->id());
                        })->with(['changeRequest', 'developer'])->latest()->take(10)->get();
                    @endphp
                    @forelse($recentNotes as $note)
                        <li class="px-6 py-4">
                            <div class="flex items-start space-x-3">
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
                                    <div class="mt-1 text-xs text-gray-500">
                                        <span class="font-medium">{{ $note->changeRequest->title }}</span> • {{ $note->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4">
                            <div class="text-center text-gray-500">No recent developer activity</div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Developer Assignment Modal -->
<div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900">Assign Developer</h3>
            <form id="assignForm" class="mt-4">
                @csrf
                <input type="hidden" id="requestId" name="requestId">
                
                <div class="mb-4">
                    <label for="developerSelect" class="block text-sm font-medium text-gray-700">Select Developer</label>
                    <select id="developerSelect" name="developer_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a developer</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="deadlineInput" class="block text-sm font-medium text-gray-700">Deadline (optional)</label>
                    <input type="date" id="deadlineInput" name="deadline"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignModal()"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                        Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function loadDevelopers(requestId) {
    try {
        const response = await fetch(`/developers?change_request_id=${requestId}`);
        const developers = await response.json();
        const select = document.getElementById('developerSelect');
        select.innerHTML = '<option value="">Select a developer</option>';
        developers.forEach(developer => {
            select.innerHTML += `<option value="${developer.id}">${developer.name}</option>`;
        });
    } catch (error) {
        console.error('Error loading developers:', error);
    }
}

function openAssignModal(requestId) {
    document.getElementById('requestId').value = requestId;
    document.getElementById('assignModal').classList.remove('hidden');
    loadDevelopers(requestId);
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('assignForm').reset();
}

document.getElementById('assignForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const requestId = document.getElementById('requestId').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/change-requests/${requestId}/assign`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                developer_id: formData.get('developer_id'),
                deadline: formData.get('deadline')
            })
        });

        const result = await response.json();
        
        if (result.success) {
            closeAssignModal();
            window.location.reload();
        } else {
            alert(result.message || 'Failed to assign developer');
        }
    } catch (error) {
        console.error('Error assigning developer:', error);
        alert('Failed to assign developer');
    }
});
</script>
@endpush
@endsection 