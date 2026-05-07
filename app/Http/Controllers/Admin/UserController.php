<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and filters.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Prevent editing super admin or self
        if ($user->isAdmin() && ! auth()->user()->isAdmin()) {
            abort(403, 'Cannot edit admin users.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user (role & status).
     */
    public function update(Request $request, User $user)
    {
        // Prevent updating super admin or self demotion
        if ($user->isAdmin() && ! auth()->user()->isAdmin()) {
            abort(403, 'Cannot modify admin users.');
        }

        if ($user->id === auth()->id()) {
            abort(403, 'Cannot modify your own account.');
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,user,manager',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting super admin or self
        if ($user->isAdmin() && ! auth()->user()->isAdmin()) {
            abort(403, 'Cannot delete admin users.');
        }

        if ($user->id === auth()->id()) {
            abort(403, 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
