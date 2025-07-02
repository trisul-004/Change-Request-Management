<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\TeamController;
use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Root and login routes - only for guests
Route::middleware('guest')->group(function () {
    Route::get('/', [App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('home');
    Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
});

// Registration Routes (accessible even when logged in)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Debug route to check user roles
    Route::get('/debug', function () {
        $user = auth()->user();
        return [
            'user' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'has_role_client' => $user->hasRole('client'),
            'has_role_supervisor' => $user->hasRole('supervisor'),
            'has_role_developer' => $user->hasRole('developer'),
        ];
    });
    
    // Team Management Routes
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::post('/team/assign', [TeamController::class, 'addDeveloper'])->name('team.assign');
    Route::delete('/team/{teamAssignment}', [TeamController::class, 'removeDeveloper'])->name('team.remove');

    // Dashboard Routes
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Debug: Log user info
        \Log::info('Dashboard access attempt', [
            'user_id' => $user ? $user->id : 'null',
            'user_email' => $user ? $user->email : 'null',
            'roles' => $user ? $user->getRoleNames() : 'null',
            'authenticated' => auth()->check()
        ]);
        
        if ($user->hasRole('client')) {
            $changeRequests = ChangeRequest::where('client_id', $user->id)->latest()->get();
            $pendingRequests = $changeRequests->where('status', 'pending')->count();
            $approvedRequests = $changeRequests->where('status', 'approved')->count();
            $inProgressRequests = $changeRequests->where('status', 'in_progress')->count();
            $rejectedRequests = $changeRequests->where('status', 'rejected')->count();
            
            return view('dashboard.client', compact('changeRequests', 'pendingRequests', 'approvedRequests', 'inProgressRequests', 'rejectedRequests'));
        } elseif ($user->hasRole('developer')) {
            // Get requests assigned to this developer
            $assignedRequests = ChangeRequest::where('developer_id', $user->id)->with('developerNotes')->latest()->get();
            
            // Get requests approved by the developer's supervisor
            $supervisor = $user->supervisor;
            $supervisorApprovedRequests = collect(); // 🔧 FIXED: previously []

            if ($supervisor) {
                $supervisorApprovedRequests = ChangeRequest::where('validated_by', $supervisor->id)
                    ->where('status', 'validated')
                    ->latest()
                    ->get();
            }
            
            // Combine both sets of requests
            $changeRequests = $assignedRequests->concat($supervisorApprovedRequests)->unique('id');
            
            $newRequests = $supervisorApprovedRequests->count();
            $pendingValidation = ChangeRequest::where('status', 'pending')->count();
            $approved = ChangeRequest::where('status', 'approved')->count();
            $inProgress = ChangeRequest::where('status', 'in_progress')->count();
            
            return view('dashboard.developer', compact('changeRequests', 'newRequests', 'pendingValidation', 'approved', 'inProgress'));
        } elseif ($user->hasRole('supervisor')) {
            // Get all requests for counting
            $allRequests = ChangeRequest::latest()->get();
            
            // Get requests approved by this supervisor (both validated and approved status)
            $myApprovedRequests = ChangeRequest::where('validated_by', $user->id)
                ->whereIn('status', ['validated', 'approved', 'in_progress', 'completed', 'on_hold'])
                ->with(['developer', 'developerNotes'])
                ->latest()
                ->get();
            
            // Get requests pending approval (not yet approved by any supervisor)
            $pendingRequests = ChangeRequest::where('status', 'pending')
                ->latest()
                ->get();
            
            $pendingApproval = $pendingRequests->count();
            $approved = $myApprovedRequests->count();
            $rejected = $allRequests->where('status', 'rejected')->count();
            $teamMembers = $user->assignedDevelopers()->count();
            
            return view('dashboard.supervisor', compact(
                'pendingRequests',
                'myApprovedRequests',
                'pendingApproval',
                'approved',
                'rejected',
                'teamMembers'
            ));
        } elseif ($user->hasRole('admin')) {
            // Redirect admin users to the Filament admin panel
            return redirect('/adm');
        }
        
        return response('Unauthorized - No valid role found', 403);
    })->name('dashboard');

    // Client routes with debugging
    Route::middleware('auth')->group(function () {
        Route::get('/change-requests/create', function() {
            // dd('Route hit');
            if (!auth()->user()->hasRole('client')) {
                dd('User does not have client role');
            }
            return app(ChangeRequestController::class)->create();
        })->name('change-requests.create');
        
        Route::post('/change-requests', [ChangeRequestController::class, 'store'])->name('change-requests.store');
    });
    
    // Developer routes
    Route::middleware('auth')->group(function () {
        // Developer task management routes
        Route::get('/change-requests/{changeRequest}/work', function(ChangeRequest $changeRequest) {
            if (!auth()->user()->hasRole('developer')) {
                dd('User does not have developer role');
            }
            // Check if the developer is assigned to this request
            if ($changeRequest->developer_id !== auth()->id()) {
                return redirect()->route('dashboard')->with('error', 'You are not assigned to this task.');
            }
            return app(ChangeRequestController::class)->workOnTask($changeRequest);
        })->name('change-requests.work');

        Route::post('/change-requests/{changeRequest}/add-note', function(ChangeRequest $changeRequest) {
            if (!auth()->user()->hasRole('developer')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only developers can add notes.'
                ], 403);
            }
            // Check if the developer is assigned to this request
            if ($changeRequest->developer_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this task.'
                ], 403);
            }
            return app(ChangeRequestController::class)->addDeveloperNote($changeRequest, request());
        })->name('change-requests.add-note');

        Route::post('/change-requests/{changeRequest}/update-status', function(ChangeRequest $changeRequest) {
            if (!auth()->user()->hasRole('developer')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only developers can update status.'
                ], 403);
            }
            // Check if the developer is assigned to this request
            if ($changeRequest->developer_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this task.'
                ], 403);
            }
            return app(ChangeRequestController::class)->updateTaskStatus($changeRequest, request());
        })->name('change-requests.update-status');
    });
    
    // Supervisor routes
    Route::middleware('auth')->group(function () {
        Route::get('/change-requests/{changeRequest}/review', function(ChangeRequest $changeRequest) {
            if (!auth()->user()->hasRole('supervisor')) {
                dd('User does not have supervisor role');
            }
            return app(ChangeRequestController::class)->review($changeRequest);
        })->name('change-requests.review');

        Route::post('/change-requests/{changeRequest}/approve', function(ChangeRequest $changeRequest) {
            if (!auth()->user()->hasRole('supervisor')) {
                dd('User does not have supervisor role');
            }
            return app(ChangeRequestController::class)->validateRequest($changeRequest->id);
        })->name('change-requests.approve');

        Route::post('/change-requests/{changeRequest}/assign', function(ChangeRequest $changeRequest) {
            if (!auth()->user()->hasRole('supervisor')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only supervisors can assign developers.'
                ], 403);
            }
            return app(ChangeRequestController::class)->assignDeveloper($changeRequest, request());
        })->name('change-requests.assign');

        Route::get('/developers', function() {
            if (!auth()->user()->hasRole('supervisor')) {
                dd('User does not have supervisor role');
            }
            return app(ChangeRequestController::class)->getDevelopers();
        })->name('developers.list');

        // Team management routes
        Route::get('/team', [TeamController::class, 'index'])->name('team.index');
        Route::post('/team/assign', [TeamController::class, 'assignDeveloper'])->name('team.assign');
        Route::delete('/team/{developer}', [TeamController::class, 'removeDeveloper'])->name('team.remove');
        
        // Route for supervisors to view developer work progress
        Route::get('/change-requests/{changeRequest}/progress', function(ChangeRequest $changeRequest) {
            if (!auth()->user()->hasRole('supervisor')) {
                return redirect()->route('dashboard')->with('error', 'Access denied.');
            }
            // Check if the supervisor validated this request
            if ($changeRequest->validated_by !== auth()->id()) {
                return redirect()->route('dashboard')->with('error', 'You can only view progress of requests you validated.');
            }
            return app(ChangeRequestController::class)->viewProgress($changeRequest);
        })->name('change-requests.progress');
    });

    // Change Request Routes
    Route::resource('change-requests', App\Http\Controllers\ChangeRequestController::class);

    Route::get('/change-requests/{changeRequest}/activity-timeline-pdf', [App\Http\Controllers\ChangeRequestController::class, 'exportActivityTimelinePdf'])->name('change-requests.activity-timeline-pdf');
    Route::get('/change-requests/{changeRequest}/activity-timeline', [App\Http\Controllers\ChangeRequestController::class, 'activityTimelineView'])->name('change-requests.activity-timeline');
});
