<?php

namespace Purdia\Config\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Config\Domain\Models\Config;

/**
 * @mixin Config
 */
class ConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'key' => $this->key,
            'value' => $this->typed_value,
            'type' => $this->type,
            'updated_at' => $this->updated_at?->toIsoString(),
        ];
    }
}
