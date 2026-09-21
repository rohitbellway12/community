<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommunityGuideline;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuidelineController extends Controller
{
    use ApiResponse;

    /**
     * Get active community guidelines and rules.
     */
    public function index(Request $request): JsonResponse
    {
        $guidelines = CommunityGuideline::active()
            ->ordered()
            ->get(['id', 'title', 'description', 'icon', 'sort_order']);

        return $this->successResponse(
            $guidelines,
            'Community guidelines retrieved successfully.'
        );
    }
}
