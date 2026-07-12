<?php

namespace Purdia\Authorization\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Purdia\Authorization\Application\Exceptions\PermissionDeniedException;
use Purdia\Shared\Contracts\Authorization\AuthorizationGateway;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        private readonly AuthorizationGateway $authorization,
    ) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            throw new PermissionDeniedException($permission);
        }

        if (! $this->authorization->userCan((string) $user->id, $permission)) {
            throw new PermissionDeniedException($permission);
        }

        return $next($request);
    }
}
