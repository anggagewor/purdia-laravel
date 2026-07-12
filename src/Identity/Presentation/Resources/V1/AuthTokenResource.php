<?php

namespace Purdia\Identity\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Identity\Application\DTOs\AuthTokenDTO;

/**
 * @mixin AuthTokenDTO
 */
class AuthTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_at' => $this->expiresAt,
        ];
    }
}
