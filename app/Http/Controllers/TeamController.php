<?php

namespace App\Http\Controllers;

use App\Models\TeamAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
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

        return view('team.index', compact('teamMembers', 'availableDevelopers'));
    }

    public function addDeveloper($developerId)
    {
        $developer = User::findOrFail($developerId);
        
        if (!$developer->hasRole('developer')) {
            return response()->json(['error' => 'Selected user is not a developer.'], 422);
        }

        // Check if developer is already assigned to any supervisor
        if (TeamAssignment::where('developer_id', $developer->id)->exists()) {
            return response()->json(['error' => 'Developer is already assigned to another supervisor.'], 422);
        }

        TeamAssignment::create([
            'supervisor_id' => auth()->id(),
            'developer_id' => $developer->id
        ]);

        return response()->json(['success' => 'Developer added to your team successfully.']);
    }

    public function assignDeveloper(Request $request)
    {
        $request->validate([
            'developer_id' => 'required|exists:users,id'
        ]);

        $developer = User::findOrFail($request->developer_id);
        
        if (!$developer->hasRole('developer')) {
            return back()->with('error', 'Selected user is not a developer.');
        }

        // Check if developer is already assigned to any supervisor
        if (TeamAssignment::where('developer_id', $developer->id)->exists()) {
            return back()->with('error', 'Developer is already assigned to another supervisor.');
        }

        TeamAssignment::create([
            'supervisor_id' => auth()->id(),
            'developer_id' => $developer->id
        ]);

        return back()->with('success', 'Developer added to your team successfully.');
    }

    public function removeDeveloper(TeamAssignment $teamAssignment)
    {
        if ($teamAssignment->supervisor_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $teamAssignment->delete();

        return back()->with('success', 'Developer removed from your team successfully.');
    }
} 