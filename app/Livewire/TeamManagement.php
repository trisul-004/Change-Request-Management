<?php

namespace App\Livewire;

use App\Models\TeamAssignment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class TeamManagement extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $supervisor = auth()->user();
        
        // Get team members with pagination
        $teamMembers = $supervisor->assignedDevelopers()
            ->paginate(12);
        
        // Get all developers who are already assigned to any supervisor
        $assignedDeveloperIds = TeamAssignment::pluck('developer_id');
        
        // Get available developers with pagination
        $availableDevelopers = User::role('developer')
            ->whereNotIn('id', $assignedDeveloperIds)
            ->paginate(12);

        return view('livewire.team-management', [
            'teamMembers' => $teamMembers,
            'availableDevelopers' => $availableDevelopers
        ]);
    }

    public function assignDeveloper($developerId)
    {
        $developer = User::findOrFail($developerId);
        
        if (!$developer->hasRole('developer')) {
            $this->dispatch('error', message: 'Selected user is not a developer.');
            return;
        }

        // Check if developer is already assigned to any supervisor
        if (TeamAssignment::where('developer_id', $developer->id)->exists()) {
            $this->dispatch('error', message: 'Developer is already assigned to another supervisor.');
            return;
        }

        TeamAssignment::create([
            'supervisor_id' => auth()->id(),
            'developer_id' => $developer->id
        ]);

        $this->dispatch('success', message: 'Developer added to your team successfully.');
        $this->resetPage('teamMembers');
        $this->resetPage('availableDevelopers');
    }

    public function removeDeveloper($developerId)
    {
        $teamAssignment = TeamAssignment::where('supervisor_id', auth()->id())
            ->where('developer_id', $developerId)
            ->first();

        if (!$teamAssignment) {
            $this->dispatch('error', message: 'Developer not found in your team.');
            return;
        }

        $teamAssignment->delete();
        $this->dispatch('success', message: 'Developer removed from your team successfully.');
        $this->resetPage('teamMembers');
        $this->resetPage('availableDevelopers');
    }
}
