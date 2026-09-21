<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * List all active categories for post creation and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query()
            ->where('status', true);

        if ($request->boolean('with_counts')) {
            $query->withCount(['posts' => function ($q) {
                $q->where('status', 'published');
            }]);
        }

        $categories = $query->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return $this->successResponse(
            CategoryResource::collection($categories),
            'Categories retrieved successfully.'
        );
    }
}
