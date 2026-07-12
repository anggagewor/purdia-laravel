<?php

use Illuminate\Support\Facades\Route;
use Purdia\Identity\Presentation\Controllers\AuthController;

Route::prefix('auth')->as('auth.')->group(function () {
    // Public (unauthenticated)
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::put('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    });
});
