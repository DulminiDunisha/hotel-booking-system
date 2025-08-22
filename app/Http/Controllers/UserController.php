<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['bookings'])->get();
        
        if (request()->wantsJson()) {
            return response()->json($users);
        }
        
        return Inertia::render('Admin/UserManagement', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/UserManagement');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,guest,user',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        }

        return redirect()->back()->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['bookings.room', 'payments']);
        return Inertia::render('Admin/UserManagement', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/UserManagement', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:admin,guest,user',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'You cannot delete your own account'
                ], 422);
            }
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    /**
     * Show user dashboard for guests
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's bookings with room information
        $bookings = Booking::with(['room'])
            ->where('guest_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('UserDashboard', [
            'user' => $user,
            'bookings' => $bookings
        ]);
    }
}
