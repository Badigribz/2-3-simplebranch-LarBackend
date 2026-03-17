<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Constructor - ensure only admins can access
     */

    /**
     * Get all pending user requests
     */
    public function pendingUsers()
    {
        $pending = User::pending()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pending);
    }

    /**
     * Get all active users
     */
    public function activeUsers()
    {
        $active = User::active()
            ->with('person')  // Include linked person data
            ->orderBy('name')
            ->get();

        return response()->json($active);
    }

    /**
     * Approve a pending user
     */
    public function approveUser(Request $request, User $user)
    {
        if ($user->status !== 'pending') {
            return response()->json([
                'message' => 'User is not pending approval'
            ], 400);
        }

        $user->update([
            'status' => 'active',
        ]);

        // Send approval email (implement later)
        // Mail::to($user->email)->send(new AccountApproved($user));

        return response()->json([
            'message' => 'User approved successfully',
            'user' => $user
        ]);
    }

    /**
     * Reject a pending user (delete account)
     */
    public function rejectUser(User $user)
    {
        if ($user->status !== 'pending') {
            return response()->json([
                'message' => 'User is not pending approval'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User registration rejected and deleted'
        ]);
    }

    /**
     * Link a user to a person in the tree
     */
    public function linkPerson(Request $request, User $user)
    {
        $request->validate([
            'person_id' => 'required|exists:people,id',
        ]);

        $user->update([
            'person_id' => $request->person_id,
        ]);

        return response()->json([
            'message' => 'User linked to person successfully',
            'user' => $user->load('person')
        ]);
    }

    /**
     * Unlink a user from their person
     */
    public function unlinkPerson(User $user)
    {
        $user->update([
            'person_id' => null,
        ]);

        return response()->json([
            'message' => 'User unlinked from person',
            'user' => $user
        ]);
    }

    /**
     * Change a user's role
     */
    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:viewer,editor,admin',
        ]);

        // Prevent demoting yourself
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return response()->json([
                'message' => 'You cannot demote yourself from admin'
            ], 400);
        }

        $user->update([
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Deactivate a user account
     */
    public function deactivateUser(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot deactivate your own account'
            ], 400);
        }

        $user->update([
            'status' => 'deactivated',
        ]);

        return response()->json([
            'message' => 'User deactivated successfully',
            'user' => $user
        ]);
    }

    /**
     * Reactivate a deactivated user
     */
    public function reactivateUser(User $user)
    {
        $user->update([
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'User reactivated successfully',
            'user' => $user
        ]);
    }

    /**
     * Reset a user's password (manual admin reset)
     */
    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        return response()->json([
            'pending_users' => User::pending()->count(),
            'active_users' => User::active()->count(),
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'editors' => User::where('role', 'editor')->count(),
            'viewers' => User::where('role', 'viewer')->count(),
        ]);
    }
}
