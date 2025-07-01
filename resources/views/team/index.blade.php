@extends('layouts.app')

@section('title', 'Manage Team')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Team</h2>
        <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Current Team Members</h3>
        @if($teamMembers->isEmpty())
            <p class="text-gray-500">No team members assigned yet.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teamMembers as $member)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">{{ $member->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $member->email }}</p>
                            </div>
                            <form action="{{ route('team.remove', $member->supervisor) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to remove this developer from your team?')">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div>
        <h3 class="text-lg font-semibold mb-4">Available Developers</h3>
        @if($availableDevelopers->isEmpty())
            <p class="text-gray-500">No available developers to add to your team.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availableDevelopers as $developer)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">{{ $developer->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $developer->email }}</p>
                            </div>
                            <form action="{{ route('team.assign') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="developer_id" value="{{ $developer->id }}">
                                <button type="submit" class="text-blue-600 hover:text-blue-800">
                                    Add to Team
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection 