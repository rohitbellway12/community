<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponse;

    /**
     * Get active promotional banners for mobile app home screen/slider.
     */
    public function index(Request $request): JsonResponse
    {
        $banners = Banner::active()
            ->orderBy('sort_order', 'asc')
            ->orderByDesc('id')
            ->get()
            ->map(function ($b) {
                return [
                    'id'          => $b->id,
                    'title'       => $b->title,
                    'description' => $b->description,
                    'image_url'   => $b->image_url,
                    'link_url'    => $b->link_url,
                    'button_text' => $b->button_text,
                    'sort_order'  => (int) $b->sort_order,
                ];
            });

        return $this->successResponse(
            $banners,
            'Active banners retrieved successfully.'
        );
    }
}
