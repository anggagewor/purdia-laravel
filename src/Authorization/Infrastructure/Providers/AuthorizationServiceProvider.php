<?php

namespace Purdia\Authorization\Infrastructure\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Purdia\Authorization\Domain\Contracts\PermissionRepository;
use Purdia\Authorization\Domain\Contracts\RoleRepository;
use Purdia\Authorization\Infrastructure\Gateway\AuthorizationGatewayImpl;
use Purdia\Authorization\Infrastructure\Repositories\EloquentPermissionRepository;
use Purdia\Authorization\Infrastructure\Repositories\EloquentRoleRepository;
use Purdia\Shared\Contracts\Authorization\AuthorizationGateway;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PermissionRepository::class, EloquentPermissionRepository::class);
        $this->app->bind(RoleRepository::class, EloquentRoleRepository::class);
        $this->app->bind(AuthorizationGateway::class, AuthorizationGatewayImpl::class);
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->registerGate();
    }

    private function registerRoutes(): void
    {
        \Illuminate\Support\Facades\Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__.'/../Routes/api.php');
    }

    private function registerGate(): void
    {
        Gate::before(function ($user, string $ability) {
            $gateway = app(AuthorizationGateway::class);

            return $gateway->userCan((string) $user->id, $ability) ?: null;
        });
    }
}
