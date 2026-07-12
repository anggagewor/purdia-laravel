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
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status?->value ?? 'active',
            'email_verified_at' => $this->email_verified_at?->toIsoString(),
            'created_at' => $this->created_at?->toIsoString(),
        ];

        // Append tenant context if available
        if ($this->getAttribute('tenant_role')) {
            $data['role'] = $this->getAttribute('tenant_role');
            $data['role_slug'] = $this->getAttribute('tenant_role_slug');
        }

        if ($this->getAttribute('branches') !== null) {
            $data['branches'] = $this->getAttribute('branches');
        }

        return $data;
    }
}
