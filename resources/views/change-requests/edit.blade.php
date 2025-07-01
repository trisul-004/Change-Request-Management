@extends('layouts.app')

@section('title', 'Edit Change Request')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold mb-0">Edit Change Request</h2>
                    <a href="{{ route('change-requests.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 border border-gray-300 font-semibold text-xs uppercase tracking-widest">
                        Back to My Requests
                    </a>
                </div>
                <form action="{{ route('change-requests.update', $changeRequest) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    <div class="mb-6">
                        <label for="portal_id" class="block text-sm font-medium text-gray-700 mb-2">Select Portal</label>
                        <select name="portal_id" id="portal_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select a portal</option>
                            @php
                                $user = auth()->user();
                                if ($user->hasRole('admin')) {
                                    $portals = \App\Models\Portal::orderBy('name')->get();
                                } else {
                                    $portals = \App\Models\Portal::where('client', $user->client_id)->orderBy('name')->get();
                                }
                            @endphp
                            @foreach($portals as $portal)
                                <option value="{{ $portal->id }}" @if($changeRequest->portal_id == $portal->id) selected @endif>{{ $portal->name }} ({{ $portal->ip_address }})</option>
                            @endforeach
                        </select>
                        @error('portal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $changeRequest->title) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $changeRequest->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 