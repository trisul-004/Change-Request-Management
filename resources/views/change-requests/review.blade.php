@extends('layouts.app')

@section('title', 'Review Change Request')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Review Change Request</h2>
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
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

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Change Request Details</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Review and take action on this change request</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->title }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->description }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($changeRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($changeRequest->status === 'validated') bg-blue-100 text-blue-800
                            @elseif($changeRequest->status === 'approved') bg-green-100 text-green-800
                            @elseif($changeRequest->status === 'in_progress') bg-purple-100 text-purple-800
                            @elseif($changeRequest->status === 'completed') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($changeRequest->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Submitted</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Client</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->client->name }}</dd>
                </div>
                @if($changeRequest->portal && $changeRequest->portal->developer)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Portal Developer</dt>
                    <dd class="mt-1 text-sm text-blue-900 font-semibold">{{ $changeRequest->portal->developer }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    @if($changeRequest->status === 'pending')
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Take Action</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Validate or reject this change request</p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <form action="{{ route('change-requests.approve', $changeRequest) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="validation_notes" class="block text-sm font-medium text-gray-700">Note</label>
                        <textarea name="validation_notes" id="validation_notes" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div x-data="{ assign: false }">
                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="assign" x-model="assign" name="assign" value="1" class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="assign" class="text-sm text-gray-700">Assign developer now</label>
                        </div>
                        <div x-show="assign" class="space-y-2">
                            <div>
                                <label for="developer_id" class="block text-sm font-medium text-gray-700">Select Developer</label>
                                <select name="developer_id" id="developer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select a developer</option>
                                    @foreach(auth()->user()->assignedDevelopers as $dev)
                                        <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline (optional)</label>
                                <input type="date" name="deadline" id="deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="submit" name="action" value="reject" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                            Reject
                        </button>
                        <button type="submit" name="action" value="approve" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Approve Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection 