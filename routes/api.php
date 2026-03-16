<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\PersonController;
// use App\Http\Controllers\Api\PhotoController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/people', [PersonController::class, 'index']);
// Route::post('/people', [PersonController::class, 'store']);
// Route::patch('/people/{person}', [PersonController::class, 'update']);
// Route::get('/tree', [PersonController::class, 'tree']);
// Route::delete('/people/{person}', [PersonController::class, 'destroy']);
// Route::get('/people/{person}', [PersonController::class, 'show']);
// Route::post('/people/{person}/photo', [PersonController::class, 'uploadPhoto']);

// // ── Gallery photo routes ──
// Route::get('/people/{person}/photos', [PhotoController::class, 'index']);
// Route::post('/people/{person}/photos', [PhotoController::class, 'store']);
// Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ═══════════════════════════════════════════════════════════════
// PUBLIC ROUTES (no authentication required)
// ═══════════════════════════════════════════════════════════════

// Registration
Route::post('/register', [RegisterController::class, 'store']);

// Login
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// ═══════════════════════════════════════════════════════════════
// PROTECTED ROUTES (authentication required)
// ═══════════════════════════════════════════════════════════════

//Route::middleware(['auth:sanctum', 'verified'])->group(function () {
Route::middleware(['auth:sanctum'])->group(function () {
    // ── Auth routes ──
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::get('/user', function (Request $request) {
        return $request->user()->load('person');
    });

    // ── Person routes (all authenticated users can view) ──
    Route::get('/people', [PersonController::class, 'index']);
    Route::get('/people/{person}', [PersonController::class, 'show']);
    Route::get('/tree', [PersonController::class, 'tree']);

    // ── Person mutations (permission-based) ──
    Route::post('/people', [PersonController::class, 'store'])
        ->middleware('can:create,App\Models\Person');

    Route::patch('/people/{person}', [PersonController::class, 'update'])
        ->middleware('can:update,person');

    Route::delete('/people/{person}', [PersonController::class, 'destroy'])
        ->middleware('can:delete,person');

    Route::post('/people/{person}/photo', [PersonController::class, 'uploadPhoto'])
        ->middleware('can:update,person');

    // ── Gallery photo routes ──
    Route::get('/people/{person}/photos', [PhotoController::class, 'index']);

    Route::post('/people/{person}/photos', [PhotoController::class, 'store'])
        ->middleware('can:update,person');

    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);

    // ── Admin routes (admin only) ──
    Route::prefix('admin')->group(function () {

        // User management
        Route::get('/users/pending', [AdminController::class, 'pendingUsers']);
        Route::get('/users/active', [AdminController::class, 'activeUsers']);
        Route::get('/stats', [AdminController::class, 'stats']);

        // Approve/reject registrations
        Route::post('/users/{user}/approve', [AdminController::class, 'approveUser']);
        Route::post('/users/{user}/reject', [AdminController::class, 'rejectUser']);

        // Link users to people
        Route::post('/users/{user}/link', [AdminController::class, 'linkPerson']);
        Route::post('/users/{user}/unlink', [AdminController::class, 'unlinkPerson']);

        // Role management
        Route::patch('/users/{user}/role', [AdminController::class, 'changeRole']);

        // Activate/deactivate
        Route::post('/users/{user}/deactivate', [AdminController::class, 'deactivateUser']);
        Route::post('/users/{user}/reactivate', [AdminController::class, 'reactivateUser']);

        // Password reset
        Route::post('/users/{user}/reset-password', [AdminController::class, 'resetUserPassword']);
    });
});

// ═══════════════════════════════════════════════════════════════
// Email verification routes (Breeze handles these automatically)
// ═══════════════════════════════════════════════════════════════
require __DIR__.'/auth.php';
