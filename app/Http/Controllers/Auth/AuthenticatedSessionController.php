<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\Auth\LoginRequest;
// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
// use Illuminate\Support\Facades\Auth;

// class AuthenticatedSessionController extends Controller
// {
//     /**
//      * Handle an incoming authentication request.
//      */
//     public function store(LoginRequest $request): Response
//     {
//         $request->authenticate();

//         $request->session()->regenerate();

//         return response()->noContent();
//     }

//     /**
//      * Destroy an authenticated session.
//      */
//     public function destroy(Request $request): Response
//     {
//         Auth::guard('web')->logout();

//         $request->session()->invalidate();

//         $request->session()->regenerateToken();

//         return response()->noContent();
//     }
// }


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // Attempt authentication
        $request->authenticate();

        $user = Auth::user();

        // Check if account is approved
        if ($user->status === 'pending') {
            Auth::logout();

            return response()->json([
                'message' => 'Your account is pending approval. You will receive an email when approved.'
            ], 403);
        }

        if ($user->status === 'deactivated') {
            Auth::logout();

            return response()->json([
                'message' => 'Your account has been deactivated. Please contact an administrator.'
            ], 403);
        }

        // Check if email is verified
        // if (!$user->hasVerifiedEmail()) {
        //     Auth::logout();

        //     return response()->json([
        //         'message' => 'Please verify your email address before logging in. Check your inbox for the verification link.'
        //     ], 403);
        // }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'person_id' => $user->person_id,
            ]
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
