@extends('layouts.app')

@section('title', 'Developer Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Developer Dashboard</h2>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-blue-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800">New Requests</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $newRequests }}</p>
        </div>
        <div class="bg-purple-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-purple-800">In Progress</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $inProgress }}</p>
        </div>
    </div>

    <div class="space-y-6">
        <!-- My Tasks Section -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">My Tasks</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Change requests assigned to you</p>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($changeRequests->where('developer_id', auth()->id()) as $request)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900">{{ $request->title }}</h3>
                                <p class="text-sm text-gray-500">{{ Str::limit($request->description, 100) }}</p>
                                <div class="mt-2 flex items-center space-x-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($request->status === 'in_progress') bg-purple-100 text-purple-800
                                        @elseif($request->status === 'completed') bg-green-100 text-green-800
                                        @elseif($request->status === 'on_hold') bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $request->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @if($request->developerNotes->count() > 0)
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">{{ $request->developerNotes->count() }}</span> note(s) added
                                    </div>
                                @endif
                                @if($request->deadline)
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">Deadline:</span> {{ \Carbon\Carbon::parse($request->deadline)->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex space-x-2">
                                <a href="{{ route('change-requests.work', $request) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                    Work on Task
                                </a>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4">
                        <div class="text-center text-gray-500">No tasks assigned to you</div>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection 