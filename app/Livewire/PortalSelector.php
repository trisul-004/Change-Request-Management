<?php

namespace App\Livewire;

use App\Models\Portal;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PortalSelector extends Component
{
    public $selectedPortal = null;
    public $search = '';

    public function mount()
    {
        Log::info('PortalSelector mounted');
    }

    public function render()
    {
        $user = Auth::user();
        
        // Get portals based on user role and client
        if ($user->hasRole('admin')) {
            // Admin can see all portals
            $portals = Portal::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                ->orderBy('name')
                ->get();
        } else {
            // Regular users can only see portals related to their client
            $portals = Portal::where('client', $user->client_id)
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('ip_address', 'like', '%' . $this->search . '%');
                })
                ->orderBy('name')
                ->get();
        }

        Log::info('Portals retrieved:', [
            'count' => $portals->count(), 
            'search' => $this->search,
            'user_client' => $user->client_id,
            'user_role' => $user->roles->pluck('name')->toArray()
        ]);

        return view('livewire.portal-selector', [
            'portals' => $portals
        ]);
    }

    public function updatedSelectedPortal($value)
    {
        Log::info('Portal selected:', ['value' => $value]);
        if ($value) {
            $this->dispatch('portalSelected', portalId: $value);
        }
    }
}
