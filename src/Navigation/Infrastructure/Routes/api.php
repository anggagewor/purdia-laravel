<?php

use Illuminate\Support\Facades\Route;
use Purdia\Navigation\Presentation\Controllers\MenuController;
use Purdia\Tenant\Infrastructure\Middleware\ResolveTenant;

Route::middleware(['auth:sanctum', ResolveTenant::class])
    ->prefix('menus')
    ->as('menus.')
    ->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/tree', [MenuController::class, 'tree'])->name('tree');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::put('/reorder', [MenuController::class, 'reorder'])->name('reorder');
        Route::get('/{menu}', [MenuController::class, 'show'])->name('show');
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
    });
