@extends('layouts.app')

@section('title', 'Change Request Details')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Change Request Details</h2>
                <a href="{{ route('change-requests.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 border border-gray-300 font-semibold text-xs uppercase tracking-widest">
                    Back to My Requests
                </a>
            </div>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                    <dd class="mt-1 text-lg text-gray-900 font-semibold">{{ $changeRequest->title }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->description }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Portal</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->portal->name }} <span class="text-xs text-gray-400">({{ $changeRequest->portal->ip_address }})</span></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($changeRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($changeRequest->status === 'approved') bg-green-100 text-green-800
                            @elseif($changeRequest->status === 'rejected') bg-red-100 text-red-800
                            @elseif($changeRequest->status === 'in_progress') bg-purple-100 text-purple-800
                            @elseif($changeRequest->status === 'completed') bg-green-200 text-green-900
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $changeRequest->status)) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $changeRequest->updated_at->format('M d, Y H:i') }}</dd>
                </div>
                @if($changeRequest->status === 'rejected' && $changeRequest->validation_notes)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-red-700">Rejection Reason</dt>
                        <dd class="mt-1 text-sm text-red-700">{{ $changeRequest->validation_notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection 