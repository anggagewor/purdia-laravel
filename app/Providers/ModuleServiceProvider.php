<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * All module service providers.
     * Add new modules here as they are created.
     */
    protected array $modules = [
        \Purdia\Shared\Providers\SharedServiceProvider::class,
        \Purdia\Identity\Infrastructure\Providers\IdentityServiceProvider::class,
        \Purdia\Authorization\Infrastructure\Providers\AuthorizationServiceProvider::class,
    ];

    public function register(): void
    {
        foreach ($this->modules as $module) {
            $this->app->register($module);
        }
    }

    public function boot(): void
    {
        //
    }
}
