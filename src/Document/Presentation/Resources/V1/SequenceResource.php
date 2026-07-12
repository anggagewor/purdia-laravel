<?php

namespace Purdia\Document\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Document\Domain\Models\Sequence;

/**
 * @mixin Sequence
 */
class SequenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'branch_id' => $this->branch_id,
            'type' => $this->type,
            'prefix' => $this->prefix,
            'format' => $this->format,
            'current_number' => $this->current_number,
            'reset_frequency' => $this->reset_frequency->value,
            'last_reset_at' => $this->last_reset_at?->toIsoString(),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
