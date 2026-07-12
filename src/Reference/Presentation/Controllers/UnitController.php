<?php

namespace Purdia\Reference\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Reference\Domain\Models\Unit;
use Purdia\Reference\Domain\Models\UnitCategory;
use Purdia\Reference\Domain\Models\UnitConversion;
use Purdia\Reference\Presentation\Resources\V1\UnitCategoryResource;
use Purdia\Shared\Support\ApiResponse;

class UnitController extends Controller
{
    /**
     * List all unit categories with their units.
     */
    public function index(): JsonResponse
    {
        $categories = UnitCategory::with('units')->orderBy('name')->get();

        return ApiResponse::success(UnitCategoryResource::collection($categories));
    }

    /**
     * Get units for a specific category.
     */
    public function show(string $category): JsonResponse
    {
        $category = UnitCategory::with('units')->where('slug', $category)->firstOrFail();

        return ApiResponse::success(new UnitCategoryResource($category));
    }

    /**
     * Convert a value from one unit to another.
     * GET /api/references/units/convert?from=kg&to=g&value=5
     */
    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['required', 'string'],
            'to' => ['required', 'string'],
            'value' => ['required', 'numeric'],
        ]);

        $fromUnit = Unit::where('symbol', $request->get('from'))->firstOrFail();
        $toUnit = Unit::where('symbol', $request->get('to'))->firstOrFail();

        $conversion = UnitConversion::where('from_unit_id', $fromUnit->id)
            ->where('to_unit_id', $toUnit->id)
            ->first();

        if (! $conversion) {
            return ApiResponse::success([
                'error' => 'No direct conversion available between these units.',
            ]);
        }

        $value = (float) $request->get('value');
        $result = $value * (float) $conversion->factor;

        return ApiResponse::success([
            'from' => [
                'unit' => $fromUnit->symbol,
                'value' => $value,
            ],
            'to' => [
                'unit' => $toUnit->symbol,
                'value' => $result,
            ],
            'factor' => (float) $conversion->factor,
        ]);
    }
}
