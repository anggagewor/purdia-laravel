<?php

namespace Purdia\Reference\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Reference\Domain\Models\Country;
use Purdia\Reference\Presentation\Resources\V1\CountryResource;
use Purdia\Shared\Support\ApiResponse;

class CountryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Country::with('currency');

        if ($request->has('region')) {
            $query->where('region', $request->get('region'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('iso2', 'like', "%{$search}%")
                    ->orWhere('iso3', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $countries = $query->orderBy('name')->get();

        return ApiResponse::success(CountryResource::collection($countries));
    }

    public function show(string $country): JsonResponse
    {
        $country = Country::with('currency')->findOrFail($country);

        return ApiResponse::success(new CountryResource($country));
    }
}
