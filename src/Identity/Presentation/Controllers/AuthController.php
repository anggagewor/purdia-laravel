<?php

namespace Purdia\Identity\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Identity\Application\Actions\LoginAction;
use Purdia\Identity\Application\Actions\LogoutAction;
use Purdia\Identity\Application\Actions\RefreshTokenAction;
use Purdia\Identity\Application\Actions\RegisterAction;
use Purdia\Identity\Application\DTOs\LoginDTO;
use Purdia\Identity\Application\DTOs\RegisterDTO;
use Purdia\Identity\Presentation\Requests\LoginRequest;
use Purdia\Identity\Presentation\Requests\RegisterRequest;
use Purdia\Identity\Presentation\Resources\V1\AuthTokenResource;
use Purdia\Identity\Presentation\Resources\V1\UserResource;
use Purdia\Shared\Support\ApiResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        $dto = new RegisterDTO(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $token = $action->execute($dto);

        return ApiResponse::created(new AuthTokenResource($token));
    }

    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $dto = new LoginDTO(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $token = $action->execute($dto);

        return ApiResponse::success(new AuthTokenResource($token));
    }

    public function logout(LogoutAction $action): JsonResponse
    {
        $action->execute(request()->user());

        return ApiResponse::success(message: 'Successfully logged out.');
    }

    public function refresh(RefreshTokenAction $action): JsonResponse
    {
        $token = $action->execute(request()->user());

        return ApiResponse::success(new AuthTokenResource($token));
    }

    public function me(): JsonResponse
    {
        return ApiResponse::success(new UserResource(request()->user()));
    }
}
