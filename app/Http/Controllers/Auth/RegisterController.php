<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserRegistration;

class RegisterController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'registration_note' => ['required', 'string', 'max:500'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'registration_note' => $request->registration_note,
            'role' => 'viewer',  // Default role as specified
            'status' => 'pending',  // Requires admin approval
        ]);

        event(new Registered($user));


        // Auto-verify email (we're skipping user email verification for now)
        $user->markEmailAsVerified();

        // Notify ALL admins about new registration
        $admins = User::where('role', 'admin')->where('status', 'active')->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new NewUserRegistration($user));
        }

        return response()->json([
            'message' => 'Registration successful! Your account is pending approval. You will receive an email when approved.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
            ]
        ], 201);
    }

    /**
     * Notify admins of new registration (implement later)
     */
    // protected function notifyAdmins(User $user)
    // {
    //     $admins = User::where('role', 'admin')->where('status', 'active')->get();
    //     foreach ($admins as $admin) {
    //         // Send notification email
    //     }
    // }
}
