<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PhotoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/people', [PersonController::class, 'index']);
Route::post('/people', [PersonController::class, 'store']);
Route::patch('/people/{person}', [PersonController::class, 'update']);
Route::get('/tree', [PersonController::class, 'tree']);
Route::delete('/people/{person}', [PersonController::class, 'destroy']);
Route::get('/people/{person}', [PersonController::class, 'show']);
Route::post('/people/{person}/photo', [PersonController::class, 'uploadPhoto']);

// ── Gallery photo routes ──
Route::get('/people/{person}/photos', [PhotoController::class, 'index']);
Route::post('/people/{person}/photos', [PhotoController::class, 'store']);
Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);
