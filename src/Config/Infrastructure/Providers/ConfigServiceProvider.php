<?php

namespace Purdia\Config\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Purdia\Config\Domain\Contracts\ConfigRepository;
use Purdia\Config\Infrastructure\Gateway\ConfigGatewayImpl;
use Purdia\Config\Infrastructure\Repositories\EloquentConfigRepository;
use Purdia\Shared\Contracts\Config\ConfigGateway;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ConfigRepository::class, EloquentConfigRepository::class);
        $this->app->bind(ConfigGateway::class, ConfigGatewayImpl::class);
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
