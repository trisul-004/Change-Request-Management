<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\DeveloperNote;
use App\Models\User;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ChangeRequestController extends Controller
{
    /**
     * Display a listing of the change requests.
     */
    public function index()
    {
        $changeRequests = ChangeRequest::where('client_id', auth()->id())->latest()->paginate(10);
        return view('change-requests.index', compact('changeRequests'));
    }

    /**
     * Show the form for creating a new change request.
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            // Admin can see all portals
            $portals = Portal::orderBy('name')->get();
        } else {
            // Regular users can only see portals related to their client
            $portals = Portal::where('client', $user->client_id)->orderBy('name')->get();
        }
        
        return view('change-requests.create', compact('portals'));
    }

    /**
     * Store a newly created change request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'portal_id' => 'required|exists:portals,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Add client_id from authenticated user
        $validated['client_id'] = auth()->id();
        $validated['status'] = 'pending';

        $changeRequest = ChangeRequest::create($validated);

        return redirect()->route('change-requests.index')
            ->with('success', 'Change request created successfully.');
    }

    /**
     * Display the specified change request.
     */
    public function show(ChangeRequest $changeRequest)
    {
        return view('change-requests.show', compact('changeRequest'));
    }

    /**
     * Show the form for editing the specified change request.
     */
    public function edit(ChangeRequest $changeRequest)
    {
        return view('change-requests.edit', compact('changeRequest'));
    }

    /**
     * Update the specified change request in storage.
     */
    public function update(Request $request, ChangeRequest $changeRequest)
    {
        $validated = $request->validate([
            'portal_id' => 'required|exists:portals,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $changeRequest->update($validated);

        return redirect()->route('change-requests.index')
            ->with('success', 'Change request updated successfully.');
    }

    /**
     * Remove the specified change request from storage.
     */
    public function destroy(ChangeRequest $changeRequest)
    {
        $changeRequest->delete();

        return redirect()->route('change-requests.index')
            ->with('success', 'Change request deleted successfully.');
    }

    public function review(ChangeRequest $changeRequest)
    {
        return view('change-requests.review', compact('changeRequest'));
    }

    public function validateRequest($id)
    {
        $changeRequest = ChangeRequest::findOrFail($id);
        
        if ($changeRequest->status !== ChangeRequest::STATUS_PENDING) {
            return back()->with('error', 'This request cannot be validated.');
        }

        $action = request('action');
        
        if ($action === 'reject') {
            $changeRequest->update([
                'status' => ChangeRequest::STATUS_REJECTED,
                'validation_notes' => request('validation_notes'),
                'validated_by' => auth()->id()
            ]);
            return redirect()->route('dashboard')->with('success', 'Request rejected successfully.');
        }

        // If assign is checked and developer_id is present, assign developer and deadline
        if (request('assign') && request('developer_id')) {
            $developer = User::find(request('developer_id'));
            if (!$developer || !$developer->hasRole('developer')) {
                return back()->with('error', 'Selected user is not a valid developer.');
            }
            $supervisor = auth()->user();
            if (!$supervisor->assignedDevelopers->contains($developer->id)) {
                return back()->with('error', 'Selected developer is not in your team.');
            }
            $changeRequest->update([
                'status' => ChangeRequest::STATUS_IN_PROGRESS,
                'validated_by' => auth()->id(),
                'validation_notes' => request('validation_notes'),
                'developer_id' => $developer->id,
                'deadline' => request('deadline') ?: null,
            ]);
            return redirect()->route('dashboard')->with('success', 'Request approved and developer assigned successfully.');
        }

        $changeRequest->update([
            'status' => ChangeRequest::STATUS_APPROVED,
            'validated_by' => auth()->id(),
            'validation_notes' => request('validation_notes')
        ]);

        return redirect()->route('dashboard')->with('success', 'Request approved successfully.');
    }

    public function approve(ChangeRequest $changeRequest, Request $request)
    {
        $validated = $request->validate([
            'approval_notes' => 'required|string',
        ]);

        if ($changeRequest->status !== 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Only validated requests can be approved.'
            ], 400);
        }

        $changeRequest->update([
            'approval_notes' => $validated['approval_notes'],
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Change request approved successfully.'
        ]);
    }

    public function assignDeveloper(ChangeRequest $changeRequest, Request $request)
    {
        $validated = $request->validate([
            'developer_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date|after:today',
        ]);

        if (!in_array($changeRequest->status, [ChangeRequest::STATUS_VALIDATED, ChangeRequest::STATUS_APPROVED])) {
            return response()->json([
                'success' => false,
                'message' => 'Only validated or approved requests can be assigned to developers.'
            ], 400);
        }

        // Check if the current user is the supervisor who validated this request
        if ($changeRequest->validated_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the supervisor who validated this request can assign developers.'
            ], 403);
        }

        $developer = User::findOrFail($validated['developer_id']);
        if (!$developer->hasRole('developer')) {
            return response()->json([
                'success' => false,
                'message' => 'Selected user is not a developer.'
            ], 400);
        }

        // Check if the developer is in the supervisor's team
        $supervisor = auth()->user();
        if (!$supervisor->assignedDevelopers->contains($developer->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected developer is not in your team.'
            ], 400);
        }

        $changeRequest->update([
            'developer_id' => $validated['developer_id'],
            'status' => ChangeRequest::STATUS_IN_PROGRESS,
            'deadline' => $validated['deadline'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Developer assigned successfully.'
        ]);
    }

    public function getDevelopers()
    {
        $request = request();
        $changeRequestId = $request->query('change_request_id');
        
        if ($changeRequestId) {
            $changeRequest = ChangeRequest::findOrFail($changeRequestId);
            
            // If the request is validated, only return developers from the validating supervisor's team
            if ($changeRequest->status === 'validated' && $changeRequest->validated_by) {
                $supervisor = User::find($changeRequest->validated_by);
                return $supervisor->assignedDevelopers;
            }
        }
        
        // For non-validated requests, return all developers
        return User::role('developer')->get();
    }

    public function workOnTask(ChangeRequest $changeRequest)
    {
        $developerNotes = $changeRequest->developerNotes()->with('developer')->latest()->get();
        return view('change-requests.work', compact('changeRequest', 'developerNotes'));
    }

    public function addDeveloperNote(ChangeRequest $changeRequest, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
            'action_type' => 'nullable|string|in:note,status_change,milestone',
        ]);

        DeveloperNote::create([
            'change_request_id' => $changeRequest->id,
            'developer_id' => auth()->id(),
            'notes' => $validated['notes'],
            'action_type' => $validated['action_type'] ?? 'note',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully.',
            'note' => [
                'notes' => $validated['notes'],
                'action_type' => $validated['action_type'] ?? 'note',
                'created_at' => now()->format('M d, Y H:i'),
                'developer_name' => auth()->user()->name,
            ]
        ]);
    }

    public function updateTaskStatus(ChangeRequest $changeRequest, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:in_progress,completed,on_hold',
            'notes' => 'required|string',
        ]);

        $statusBefore = $changeRequest->status;
        $statusAfter = $validated['status'];

        // Update the change request status
        $changeRequest->update([
            'status' => $statusAfter,
        ]);

        // Create a note for the status change
        DeveloperNote::create([
            'change_request_id' => $changeRequest->id,
            'developer_id' => auth()->id(),
            'notes' => $validated['notes'],
            'action_type' => 'status_change',
            'status_before' => $statusBefore,
            'status_after' => $statusAfter,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'new_status' => $statusAfter,
        ]);
    }

    public function viewProgress(ChangeRequest $changeRequest)
    {
        $developerNotes = $changeRequest->developerNotes()->with('developer')->latest()->get();
        return view('change-requests.progress', compact('changeRequest', 'developerNotes'));
    }

    public function exportActivityTimelinePdf(ChangeRequest $changeRequest)
    {
        $developerNotes = $changeRequest->developerNotes()->with('developer')->latest()->get();
        $supervisor = $changeRequest->validatedBy;
        $developer = $changeRequest->developer;
        $downloadedAt = now();
        $pdf = Pdf::loadView('change-requests.activity-timeline-pdf', compact('changeRequest', 'developerNotes', 'supervisor', 'developer', 'downloadedAt'));
        $filename = 'activity-timeline-' . $changeRequest->id . '.pdf';
        return $pdf->download($filename);
    }

    public function activityTimelineView(ChangeRequest $changeRequest)
    {
        $developerNotes = $changeRequest->developerNotes()->with('developer')->latest()->get();
        $supervisor = $changeRequest->validatedBy;
        $developer = $changeRequest->developer;
        return view('change-requests.activity-timeline', compact('changeRequest', 'developerNotes', 'supervisor', 'developer'));
    }
} 