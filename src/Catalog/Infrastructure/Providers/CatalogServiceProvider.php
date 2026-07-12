<?php

namespace Purdia\Catalog\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Purdia\Catalog\Domain\Contracts\ProductRepository;
use Purdia\Catalog\Infrastructure\Repositories\EloquentProductRepository;

class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    private function registerRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__.'/../Routes/api.php');
    }
}
