@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Client Dashboard</h2>
        <div class="flex space-x-2">
            <a href="{{ route('change-requests.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 border border-gray-300 font-semibold text-xs uppercase tracking-widest">
                View My Requests
            </a>
            <a href="{{ route('change-requests.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                New Change Request
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-blue-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800">Pending Requests</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $pendingRequests }}</p>
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-green-800">Approved Requests</h3>
            <p class="text-3xl font-bold text-green-600">{{ $approvedRequests }}</p>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-yellow-800">In Progress</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $inProgressRequests }}</p>
        </div>
        <div class="bg-red-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-red-800">Rejected</h3>
            <p class="text-3xl font-bold text-red-600">{{ $rejectedRequests }}</p>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">My Change Requests</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">List of all your change requests</p>
        </div>
        <ul class="divide-y divide-gray-200">
            @forelse($changeRequests as $request)
                <li class="px-6 py-4 @if($request->status === 'rejected') bg-red-50 @endif">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $request->title }}</h3>
                            <p class="text-sm text-gray-500">{{ Str::limit($request->description, 100) }}</p>
                            <div class="mt-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'validated') bg-blue-100 text-blue-800
                                    @elseif($request->status === 'approved') bg-green-100 text-green-800
                                    @elseif($request->status === 'in_progress') bg-purple-100 text-purple-800
                                    @elseif($request->status === 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                            @if($request->status === 'rejected' && $request->validation_notes)
                                <div class="mt-2 bg-red-100 p-4 rounded-md border border-red-200">
                                    <h4 class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</h4>
                                    <p class="text-sm text-red-700">{{ $request->validation_notes }}</p>
                                    <p class="text-xs text-red-600 mt-2">Rejected on {{ $request->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $request->created_at->diffForHumans() }}
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-4">
                    <div class="text-center text-gray-500">No change requests yet</div>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection 