<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class PublicServiceController extends Controller
{
    /**
     * Display listing of active consultancy services for public users.
     */
    public function index(Request $request): View
    {
        $categorySlug = $request->input('category');
        $search = trim((string) $request->input('search'));

        $query = Service::query()
            ->published()
            ->with('category')
            ->ordered();

        $activeCategory = null;
        if ($categorySlug && $categorySlug !== 'all') {
            $activeCategory = ServiceCategory::where('slug', $categorySlug)
                ->orWhere('id', is_numeric($categorySlug) ? (int)$categorySlug : 0)
                ->first();

            if ($activeCategory) {
                $query->where('service_category_id', $activeCategory->id);
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(12)->withQueryString();

        $categories = ServiceCategory::where('is_active', true)
            ->withCount(['services' => function ($q) {
                $q->published();
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $totalServices = Service::published()->count();

        return view('services.index', compact(
            'services',
            'categories',
            'activeCategory',
            'search',
            'totalServices'
        ));
    }

    /**
     * Display a single service details page.
     */
    public function show(string $slug): View
    {
        $service = Service::query()
            ->published()
            ->with('category')
            ->where(function ($q) use ($slug) {
                if (is_numeric($slug)) {
                    $q->where('id', (int) $slug)->orWhere('slug', $slug);
                } else {
                    $q->where('slug', $slug);
                }
            })
            ->firstOrFail();

        // Related services in the same category
        $relatedServices = Service::query()
            ->published()
            ->with('category')
            ->where('id', '!=', $service->id)
            ->when($service->service_category_id, function ($q) use ($service) {
                $q->where('service_category_id', $service->service_category_id);
            })
            ->ordered()
            ->take(3)
            ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }

    /**
     * Handle direct WhatsApp inquiry click, increment counter, and redirect.
     */
    public function inquiry(Request $request, Service $service): RedirectResponse|JsonResponse
    {
        $service->increment('click_count');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'click_count'  => $service->click_count,
                'whatsapp_url' => $service->whatsapp_url,
            ]);
        }

        return redirect()->away($service->whatsapp_url);
    }
}
