<?php

namespace Purdia\Pricing\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Purdia\Pricing\Application\Engine\PricingEngineImpl;
use Purdia\Shared\Contracts\Pricing\PricingEngine;

class PricingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PricingEngine::class, PricingEngineImpl::class);
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
