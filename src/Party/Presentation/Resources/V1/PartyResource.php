<?php

namespace Purdia\Party\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Party\Domain\Models\Party;

/**
 * @mixin Party
 */
class PartyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'display_name' => $this->display_name,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'person' => new PersonResource($this->whenLoaded('person')),
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('role')),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
