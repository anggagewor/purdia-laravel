<?php

use Illuminate\Support\Facades\Route;
use Purdia\Document\Presentation\Controllers\SequenceController;
use Purdia\Tenant\Infrastructure\Middleware\ResolveTenant;

Route::middleware(['auth:sanctum', ResolveTenant::class])
    ->prefix('documents')
    ->as('documents.')
    ->group(function () {
        // Sequence management
        Route::get('/sequences', [SequenceController::class, 'index'])->name('sequences.index');
        Route::post('/sequences', [SequenceController::class, 'store'])->name('sequences.store');
        Route::get('/sequences/{sequence}', [SequenceController::class, 'show'])->name('sequences.show');

        // Number generation
        Route::post('/generate', [SequenceController::class, 'generate'])->name('generate');
        Route::get('/preview', [SequenceController::class, 'preview'])->name('preview');
    });
