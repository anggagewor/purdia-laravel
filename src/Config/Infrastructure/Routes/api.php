<?php

use Illuminate\Support\Facades\Route;
use Purdia\Config\Presentation\Controllers\ConfigController;

Route::middleware('auth:sanctum')->prefix('configs')->as('configs.')->group(function () {
    Route::get('/', [ConfigController::class, 'index'])->name('index');
    Route::get('/{group}', [ConfigController::class, 'show'])->name('show');
    Route::put('/{group}', [ConfigController::class, 'update'])->name('update');
    Route::put('/{group}/bulk', [ConfigController::class, 'bulk'])->name('bulk');
    Route::delete('/{group}/{key}', [ConfigController::class, 'destroy'])->name('destroy');
});
