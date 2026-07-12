<?php

use Illuminate\Support\Facades\Route;
use Purdia\Tenant\Infrastructure\Middleware\ResolveTenant;
use Purdia\Tenant\Presentation\Controllers\BranchController;
use Purdia\Tenant\Presentation\Controllers\TenantController;

Route::middleware('auth:sanctum')->group(function () {
    // Tenant management (no tenant context needed)
    Route::prefix('tenants')->as('tenants.')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::post('/', [TenantController::class, 'store'])->name('store');
        Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
    });

    // Branch management (requires tenant context)
    Route::middleware(ResolveTenant::class)->prefix('branches')->as('branches.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('/{branch}', [BranchController::class, 'show'])->name('show');
    });
});
