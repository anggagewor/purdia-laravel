<?php

namespace Purdia\Storage\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Purdia\Storage\Domain\Contracts\FileRepository;
use Purdia\Storage\Domain\Contracts\StorageRuleResolver;
use Purdia\Storage\Infrastructure\Repositories\EloquentFileRepository;
use Purdia\Storage\Infrastructure\Repositories\EloquentStorageRuleResolver;

class StorageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FileRepository::class, EloquentFileRepository::class);
        $this->app->bind(StorageRuleResolver::class, EloquentStorageRuleResolver::class);
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
