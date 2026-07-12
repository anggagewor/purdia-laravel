<?php

namespace Purdia\Identity\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Identity\Application\Actions\ChangePasswordAction;
use Purdia\Identity\Application\Actions\ForgotPasswordAction;
use Purdia\Identity\Application\Actions\LoginAction;
use Purdia\Identity\Application\Actions\LogoutAction;
use Purdia\Identity\Application\Actions\RefreshTokenAction;
use Purdia\Identity\Application\Actions\RegisterAction;
use Purdia\Identity\Application\Actions\ResetPasswordAction;
use Purdia\Identity\Application\DTOs\ChangePasswordDTO;
use Purdia\Identity\Application\DTOs\ForgotPasswordDTO;
use Purdia\Identity\Application\DTOs\LoginDTO;
use Purdia\Identity\Application\DTOs\RegisterDTO;
use Purdia\Identity\Application\DTOs\ResetPasswordDTO;
use Purdia\Identity\Presentation\Requests\ChangePasswordRequest;
use Purdia\Identity\Presentation\Requests\ForgotPasswordRequest;
use Purdia\Identity\Presentation\Requests\LoginRequest;
use Purdia\Identity\Presentation\Requests\RegisterRequest;
use Purdia\Identity\Presentation\Requests\ResetPasswordRequest;
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

    public function changePassword(ChangePasswordRequest $request, ChangePasswordAction $action): JsonResponse
    {
        $dto = new ChangePasswordDTO(
            currentPassword: $request->validated('current_password'),
            newPassword: $request->validated('new_password'),
        );

        $action->execute(request()->user(), $dto);

        return ApiResponse::success(message: 'Password changed successfully.');
    }

    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $action): JsonResponse
    {
        $dto = new ForgotPasswordDTO(
            email: $request->validated('email'),
        );

        $action->execute($dto);

        // Always return success to prevent email enumeration
        return ApiResponse::success(message: 'If the email exists, a password reset link has been sent.');
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action): JsonResponse
    {
        $dto = new ResetPasswordDTO(
            email: $request->validated('email'),
            token: $request->validated('token'),
            password: $request->validated('password'),
        );

        $action->execute($dto);

        return ApiResponse::success(message: 'Password has been reset successfully.');
    }
}
