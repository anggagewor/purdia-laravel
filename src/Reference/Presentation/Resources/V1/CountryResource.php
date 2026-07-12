<?php

namespace Purdia\Reference\Presentation\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Purdia\Reference\Domain\Models\Country;

/**
 * @mixin Country
 */
class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'iso2' => $this->iso2,
            'iso3' => $this->iso3,
            'numeric_code' => $this->numeric_code,
            'phone_code' => $this->phone_code,
            'capital' => $this->capital,
            'region' => $this->region,
            'subregion' => $this->subregion,
            'is_active' => $this->is_active,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
        ];
    }
}
