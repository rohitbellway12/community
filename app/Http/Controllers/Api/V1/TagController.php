<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    use ApiResponse;

    /**
     * List popular and searchable tags for post creation and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->trim();
        $sort   = $request->input('sort', 'popular'); // 'popular' or 'latest' or 'alphabetical'
        $limit  = min($request->integer('limit', 30), 100);

        $query = Tag::query()
            ->withCount(['posts' => function ($q) {
                $q->where('status', 'published');
            }]);

        if ($search->isNotEmpty()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $query = match ($sort) {
            'alphabetical' => $query->orderBy('name', 'asc'),
            'latest'       => $query->latest('id'),
            default        => $query->orderByDesc('posts_count')->orderBy('name', 'asc'),
        };

        $tags = $query->take($limit)->get();

        return $this->successResponse(
            TagResource::collection($tags),
            'Tags retrieved successfully.'
        );
    }
}
