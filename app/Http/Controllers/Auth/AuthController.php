<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        Log::info('Show login page', [
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'session_id' => request()->session()->getId()
        ]);
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        Log::info('Login attempt', ['email' => $credentials['email']]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $roles = $user->getRoleNames();
            
            Log::info('Login successful', [
                'user_id' => $user->id, 
                'user_email' => $user->email,
                'roles' => $roles,
                'has_admin_role' => $user->hasRole('admin'),
                'session_id' => $request->session()->getId()
            ]);
            
            if ($user->hasRole('admin')) {
                Log::info('Redirecting admin to /adm');
                return redirect('/adm');
            }
            Log::info('Redirecting user to /dashboard', ['roles' => $roles]);
            return redirect('/dashboard');
        }

        Log::warning('Login failed', ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:client,developer,supervisor'],
        ]);

        // Determine client_id based on user name
        $clientId = null;
        $availableClients = ['c1', 'c2', 'c3', 'c4', 'c5'];
        
        // If the name matches a client ID, assign that client
        if (in_array(strtolower($request->name), $availableClients)) {
            $clientId = strtolower($request->name);
        } else {
            // Otherwise, assign a client in round-robin fashion
            $existingUsers = User::whereNotNull('client_id')->count();
            $clientId = $availableClients[$existingUsers % count($availableClients)];
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'client_id' => $clientId,
        ]);

        // Create role if it doesn't exist and assign it
        $role = Role::firstOrCreate(['name' => $request->role]);
        $user->assignRole($role);

        Auth::login($user);

        return redirect()->intended('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 