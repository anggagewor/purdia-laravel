<?php

namespace Purdia\Tenant\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Purdia\Shared\Contracts\Tenant\TenantContextInterface;
use Purdia\Tenant\Application\Resolvers\HeaderTenantResolver;
use Purdia\Tenant\Application\Resolvers\SubdomainTenantResolver;
use Purdia\Tenant\Application\Resolvers\TenantResolverChain;
use Purdia\Tenant\Infrastructure\Gateway\TenantContextAdapter;

class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register resolver chain
        $this->app->singleton(TenantResolverChain::class, function () {
            $chain = new TenantResolverChain();
            $chain->addResolver(new HeaderTenantResolver());
            $chain->addResolver(new SubdomainTenantResolver());

            return $chain;
        });

        // Register context interface for other modules
        $this->app->bind(TenantContextInterface::class, TenantContextAdapter::class);
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
