<?php

namespace Purdia\Catalog\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Catalog\Domain\Models\Brand;
use Purdia\Catalog\Presentation\Resources\V1\BrandResource;
use Purdia\Shared\Support\ApiResponse;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return ApiResponse::success(BrandResource::collection($brands));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $brand = Brand::create($request->only(['name', 'slug', 'description']));

        return ApiResponse::created(new BrandResource($brand));
    }

    public function show(string $brand): JsonResponse
    {
        $brand = Brand::findOrFail($brand);

        return ApiResponse::success(new BrandResource($brand));
    }

    public function destroy(string $brand): JsonResponse
    {
        Brand::findOrFail($brand)->delete();

        return ApiResponse::success(message: 'Brand deleted successfully.');
    }
}
