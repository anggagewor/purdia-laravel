<?php

use Illuminate\Support\Facades\Route;
use Purdia\Party\Presentation\Controllers\PartyController;
use Purdia\Tenant\Infrastructure\Middleware\ResolveTenant;

Route::middleware(['auth:sanctum', ResolveTenant::class])
    ->prefix('parties')
    ->as('parties.')
    ->group(function () {
        Route::get('/', [PartyController::class, 'index'])->name('index');
        Route::get('/{party}', [PartyController::class, 'show'])->name('show');
        Route::post('/persons', [PartyController::class, 'storePerson'])->name('persons.store');
        Route::post('/organizations', [PartyController::class, 'storeOrganization'])->name('organizations.store');
        Route::delete('/{party}', [PartyController::class, 'destroy'])->name('destroy');
    });
