<div>
    {{-- The Master doesn't talk, he acts. --}}
    <div class="mb-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search portals by name or IP..."
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>

    @php
        $portalCount = $portals->count();
    @endphp
    
    <!-- Debug info -->
    @if(config('app.debug'))
        <div class="mb-2 p-2 bg-gray-100 text-sm">
            Portal Count: {{ $portalCount }}
        </div>
    @endif

    <select
        wire:model.live="selectedPortal"
        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
    >
        <option value="">Select a portal...</option>
        @forelse($portals as $portal)
            <option value="{{ $portal->id }}" class="py-2">
                {{ $portal->name }} 
                @if($portal->ip_address) (IP: {{ $portal->ip_address }}) @endif
            </option>
        @empty
            <option value="" disabled>No portals found</option>
        @endforelse
    </select>

    @if($selectedPortal)
        @php
            $selectedPortalDetails = $portals->firstWhere('id', $selectedPortal);
        @endphp
        @if($selectedPortalDetails)
            <div class="mt-3 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900">{{ $selectedPortalDetails->name }}</h4>
                @if($selectedPortalDetails->url)
                    <p class="text-sm text-gray-600 mt-1">URL: {{ $selectedPortalDetails->url }}</p>
                @endif
                @if($selectedPortalDetails->ip_address)
                    <p class="text-sm text-gray-600">IP: {{ $selectedPortalDetails->ip_address }}</p>
                @endif
                @if($selectedPortalDetails->managed_by)
                    <p class="text-sm text-gray-600">Managed By: {{ $selectedPortalDetails->managed_by }}</p>
                @endif
                @if($selectedPortalDetails->description)
                    <p class="text-sm text-gray-500 mt-2">{{ $selectedPortalDetails->description }}</p>
                @endif
            </div>
        @endif
    @endif
</div>
