<?php

use Illuminate\Support\Facades\Route;
use Purdia\Authorization\Presentation\Controllers\PermissionController;
use Purdia\Authorization\Presentation\Controllers\RoleController;

Route::middleware('auth:sanctum')->group(function () {
    // Roles
    Route::apiResource('roles', RoleController::class);
    Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');

    // Permissions
    Route::apiResource('permissions', PermissionController::class);
});
