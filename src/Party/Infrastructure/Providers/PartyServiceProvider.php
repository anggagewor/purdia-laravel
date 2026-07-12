<?php

namespace Purdia\Party\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Purdia\Party\Domain\Contracts\PartyRepository;
use Purdia\Party\Infrastructure\Gateway\PartyGatewayImpl;
use Purdia\Party\Infrastructure\Repositories\EloquentPartyRepository;
use Purdia\Shared\Contracts\Party\PartyGateway;

class PartyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PartyRepository::class, EloquentPartyRepository::class);
        $this->app->bind(PartyGateway::class, PartyGatewayImpl::class);
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
