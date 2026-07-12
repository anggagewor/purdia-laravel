<?php

namespace Purdia\Shared\Providers;

use Illuminate\Support\ServiceProvider;
use Purdia\Shared\Exceptions\ApiExceptionRenderer;
use Purdia\Shared\Exceptions\DomainException;

class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerExceptionRendering();
    }

    private function registerExceptionRendering(): void
    {
        $exceptions = $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);

        $exceptions->renderable(function (DomainException $e, $request) {
            return (new ApiExceptionRenderer())->render($request, $e);
        });
    }
}
