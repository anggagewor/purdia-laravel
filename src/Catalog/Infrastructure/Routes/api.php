<?php

use Illuminate\Support\Facades\Route;
use Purdia\Catalog\Presentation\Controllers\BrandController;
use Purdia\Catalog\Presentation\Controllers\CategoryController;
use Purdia\Catalog\Presentation\Controllers\ProductController;
use Purdia\Tenant\Infrastructure\Middleware\ResolveTenant;

Route::middleware(['auth:sanctum', ResolveTenant::class])
    ->prefix('catalog')
    ->as('catalog.')
    ->group(function () {
        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Brands
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}', [BrandController::class, 'show'])->name('brands.show');
        Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
    });
