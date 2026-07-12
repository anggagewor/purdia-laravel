<?php

namespace Purdia\Identity\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Identity\Domain\Models\User;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status->value,
            'email_verified_at' => $this->email_verified_at?->toIsoString(),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
