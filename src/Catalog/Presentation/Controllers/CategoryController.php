<?php

namespace Purdia\Catalog\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Purdia\Catalog\Domain\Models\Category;
use Purdia\Catalog\Presentation\Resources\V1\CategoryResource;
use Purdia\Shared\Support\ApiResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return ApiResponse::success(CategoryResource::collection($categories));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $category = Category::create($request->only(['name', 'slug', 'parent_id', 'description', 'sort_order']));

        return ApiResponse::created(new CategoryResource($category));
    }

    public function show(string $category): JsonResponse
    {
        $category = Category::with('children')->findOrFail($category);

        return ApiResponse::success(new CategoryResource($category));
    }

    public function destroy(string $category): JsonResponse
    {
        Category::findOrFail($category)->delete();

        return ApiResponse::success(message: 'Category deleted successfully.');
    }
}
