<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Public API routes
Route::post('/login', [ApiController::class, 'login']);

// Protected API routes (Sanctum Tokens)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/courses', [ApiController::class, 'courses']);
    Route::get('/subjects', [ApiController::class, 'subjects']);
    Route::post('/attendance/verify-face', [ApiController::class, 'verifyFace']);
    Route::post('/attendance/scan-qr', [ApiController::class, 'scanQr']);
});
