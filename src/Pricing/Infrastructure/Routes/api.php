<?php

use Illuminate\Support\Facades\Route;
use Purdia\Pricing\Presentation\Controllers\PricingController;
use Purdia\Tenant\Infrastructure\Middleware\ResolveTenant;

Route::middleware(['auth:sanctum', ResolveTenant::class])
    ->prefix('pricing')
    ->as('pricing.')
    ->group(function () {
        // Engine endpoints
        Route::post('/resolve', [PricingController::class, 'resolve'])->name('resolve');
        Route::post('/discount', [PricingController::class, 'applyDiscount'])->name('discount');

        // Price Lists
        Route::get('/price-lists', [PricingController::class, 'priceLists'])->name('price-lists.index');
        Route::post('/price-lists', [PricingController::class, 'storePriceList'])->name('price-lists.store');

        // Discounts
        Route::get('/discounts', [PricingController::class, 'discounts'])->name('discounts.index');
        Route::post('/discounts', [PricingController::class, 'storeDiscount'])->name('discounts.store');

        // Promotions
        Route::get('/promotions', [PricingController::class, 'promotions'])->name('promotions.index');
        Route::post('/promotions', [PricingController::class, 'storePromotion'])->name('promotions.store');
    });
