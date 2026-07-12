<?php

namespace Purdia\Reference\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Reference\Domain\Models\Country;
use Purdia\Reference\Domain\Models\Currency;
use Purdia\Reference\Domain\Models\Language;
use Purdia\Reference\Domain\Models\LookupType;
use Purdia\Reference\Domain\Models\TaxCategory;
use Purdia\Reference\Domain\Models\Timezone;
use Purdia\Reference\Domain\Models\UnitCategory;
use Purdia\Shared\Support\ApiResponse;

class LookupController extends Controller
{
    /**
     * Unified lookup endpoint.
     * GET /api/lookups?types=country,currency,gender,timezone
     *
     * Returns multiple reference datasets in a single request.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'types' => ['required', 'string'],
        ]);

        $types = array_map('trim', explode(',', $request->get('types')));
        $result = [];

        foreach ($types as $type) {
            $result[$type] = $this->resolve($type);
        }

        return ApiResponse::success($result);
    }

    /**
     * Get a single lookup type.
     * GET /api/lookups/{type}
     */
    public function show(string $type): JsonResponse
    {
        $result = $this->resolve($type);

        return ApiResponse::success($result);
    }

    private function resolve(string $type): array
    {
        return match ($type) {
            'country' => $this->getCountries(),
            'currency' => $this->getCurrencies(),
            'timezone' => $this->getTimezones(),
            'language' => $this->getLanguages(),
            'tax-category' => $this->getTaxCategories(),
            'unit' => $this->getUnits(),
            default => $this->getLookupItems($type),
        };
    }

    private function getCountries(): array
    {
        return Country::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'iso2', 'iso3', 'phone_code'])
            ->toArray();
    }

    private function getCurrencies(): array
    {
        return Currency::where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'name', 'code', 'symbol', 'decimal_places'])
            ->toArray();
    }

    private function getTimezones(): array
    {
        return Timezone::where('is_active', true)
            ->orderBy('utc_offset')
            ->get(['id', 'name', 'code', 'offset', 'utc_offset'])
            ->toArray();
    }

    private function getLanguages(): array
    {
        return Language::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'native_name'])
            ->toArray();
    }

    private function getTaxCategories(): array
    {
        return TaxCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'rate', 'description'])
            ->toArray();
    }

    private function getUnits(): array
    {
        return UnitCategory::with('units:id,category_id,name,symbol,is_base')
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->toArray();
    }

    private function getLookupItems(string $typeSlug): array
    {
        $type = LookupType::where('slug', $typeSlug)->first();

        if (! $type) {
            return [];
        }

        return $type->items()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'sort_order'])
            ->toArray();
    }
}
