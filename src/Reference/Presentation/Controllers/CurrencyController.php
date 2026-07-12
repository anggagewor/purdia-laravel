<?php

namespace Purdia\Reference\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Reference\Domain\Models\Currency;
use Purdia\Reference\Presentation\Resources\V1\CurrencyResource;
use Purdia\Shared\Support\ApiResponse;

class CurrencyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Currency::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $currencies = $query->orderBy('code')->get();

        return ApiResponse::success(CurrencyResource::collection($currencies));
    }

    public function show(string $currency): JsonResponse
    {
        $currency = Currency::findOrFail($currency);

        return ApiResponse::success(new CurrencyResource($currency));
    }
}
