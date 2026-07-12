<?php

use Illuminate\Support\Facades\Route;
use Purdia\Storage\Presentation\Controllers\FileController;

Route::middleware('auth:sanctum')->prefix('files')->as('files.')->group(function () {
    Route::post('/', [FileController::class, 'upload'])->name('upload');
    Route::get('/{file}', [FileController::class, 'show'])->name('show');
    Route::get('/{file}/download', [FileController::class, 'download'])->name('download');
    Route::delete('/{file}', [FileController::class, 'destroy'])->name('destroy');

    // Access control
    Route::post('/{file}/access', [FileController::class, 'grantAccess'])->name('access.grant');
    Route::delete('/{file}/access', [FileController::class, 'revokeAccess'])->name('access.revoke');

    // Query by entity
    Route::get('/entity/{entityType}/{entityId}', [FileController::class, 'byEntity'])->name('entity');
});
