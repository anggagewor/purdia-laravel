<?php

namespace Purdia\Identity\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Purdia\Identity\Domain\Contracts\UserRepository;
use Purdia\Identity\Infrastructure\Gateway\IdentityGatewayImpl;
use Purdia\Identity\Infrastructure\Repositories\EloquentUserRepository;
use Purdia\Shared\Contracts\Identity\IdentityGateway;

class IdentityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(IdentityGateway::class, IdentityGatewayImpl::class);
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    private function registerRoutes(): void
    {
        \Illuminate\Support\Facades\Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__.'/../Routes/api.php');
    }
}
