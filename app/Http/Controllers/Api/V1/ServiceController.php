<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCategoryResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    /**
     * List all active consultancy services for mobile app.
     *
     * GET /api/v1/services
     * Optional Query Params:
     * - category_id: Filter by category ID
     * - category: Filter by category slug or ID
     * - search: Search keyword in title or description
     * - per_page: Pagination count (default 15, max 50)
     * - page: Page number
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::query()
            ->published()
            ->with('category')
            ->ordered();

        // Filter by category ID
        if ($request->filled('category_id') && is_numeric($request->input('category_id'))) {
            $query->where('service_category_id', $request->integer('category_id'));
        } elseif ($request->filled('category')) {
            $cat = $request->input('category');
            $query->where(function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('service_category_id', (int) $cat);
                } else {
                    $q->whereHas('category', function ($sub) use ($cat) {
                        $sub->where('slug', $cat);
                    });
                }
            });
        }

        // Search in title, short_description or full description
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = min(50, max(1, $request->integer('per_page', 15)));
        $services = $query->paginate($perPage);

        // Fetch active categories with service count for mobile filter tabs
        $categories = ServiceCategory::where('is_active', true)
            ->withCount(['services' => function ($q) {
                $q->published();
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return $this->successResponse([
            'services'   => ServiceResource::collection($services->items()),
            'categories' => ServiceCategoryResource::collection($categories),
            'pagination' => [
                'total'        => $services->total(),
                'current_page' => $services->currentPage(),
                'last_page'    => $services->lastPage(),
                'per_page'     => $services->perPage(),
                'has_more'     => $services->hasMorePages(),
            ],
        ], 'Services retrieved successfully.');
    }

    /**
     * Get detailed view of a single consultancy service.
     *
     * GET /api/v1/services/{id_or_slug}
     */
    public function show(Request $request, $id_or_slug): JsonResponse
    {
        $service = Service::query()
            ->published()
            ->with('category')
            ->where(function ($q) use ($id_or_slug) {
                if (is_numeric($id_or_slug)) {
                    $q->where('id', (int) $id_or_slug)
                      ->orWhere('slug', (string) $id_or_slug);
                } else {
                    $q->where('slug', (string) $id_or_slug);
                }
            })
            ->first();

        if (!$service) {
            return $this->errorResponse('Service not found or currently inactive.', 404);
        }

        // Fetch up to 4 related services in the same category
        $relatedServices = Service::query()
            ->published()
            ->with('category')
            ->where('id', '!=', $service->id)
            ->when($service->service_category_id, function ($q) use ($service) {
                $q->where('service_category_id', $service->service_category_id);
            })
            ->ordered()
            ->take(4)
            ->get();

        return $this->successResponse([
            'service'          => new ServiceResource($service),
            'related_services' => ServiceResource::collection($relatedServices),
        ], 'Service details retrieved successfully.');
    }

    /**
     * Record an inquiry click when user taps WhatsApp/Contact on mobile.
     *
     * POST /api/v1/services/{id_or_slug}/inquiry
     */
    public function recordInquiry(Request $request, $id_or_slug): JsonResponse
    {
        $service = Service::query()
            ->where(function ($q) use ($id_or_slug) {
                if (is_numeric($id_or_slug)) {
                    $q->where('id', (int) $id_or_slug)
                      ->orWhere('slug', (string) $id_or_slug);
                } else {
                    $q->where('slug', (string) $id_or_slug);
                }
            })
            ->first();

        if (!$service) {
            return $this->errorResponse('Service not found.', 404);
        }

        $service->increment('click_count');

        return $this->successResponse([
            'service_id'   => $service->id,
            'title'        => $service->title,
            'click_count'  => $service->click_count,
            'whatsapp_url' => $service->whatsapp_url,
        ], 'Inquiry recorded successfully.');
    }

    /**
     * List all service categories for mobile app.
     *
     * GET /api/v1/service-categories
     */
    public function categories(Request $request): JsonResponse
    {
        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->withCount(['services' => function ($q) {
                $q->published();
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return $this->successResponse(
            ServiceCategoryResource::collection($categories),
            'Service categories retrieved successfully.'
        );
    }
}
