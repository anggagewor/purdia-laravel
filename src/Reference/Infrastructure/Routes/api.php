<?php

use Illuminate\Support\Facades\Route;
use Purdia\Reference\Presentation\Controllers\CountryController;
use Purdia\Reference\Presentation\Controllers\CurrencyController;
use Purdia\Reference\Presentation\Controllers\LookupController;
use Purdia\Reference\Presentation\Controllers\UnitController;

Route::middleware('auth:sanctum')->group(function () {
    // Unified Lookup — single call, multiple data types
    Route::get('/lookups', [LookupController::class, 'index'])->name('lookups.index');
    Route::get('/lookups/{type}', [LookupController::class, 'show'])->name('lookups.show');

    // Detailed reference endpoints
    Route::prefix('references')->as('references.')->group(function () {
        Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
        Route::get('/countries/{country}', [CountryController::class, 'show'])->name('countries.show');

        Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index');
        Route::get('/currencies/{currency}', [CurrencyController::class, 'show'])->name('currencies.show');

        Route::get('/units', [UnitController::class, 'index'])->name('units.index');
        Route::get('/units/convert', [UnitController::class, 'convert'])->name('units.convert');
        Route::get('/units/{category}', [UnitController::class, 'show'])->name('units.show');
    });
});
