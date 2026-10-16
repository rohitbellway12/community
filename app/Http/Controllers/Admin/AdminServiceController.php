<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index(Request $request)
    {
        $query = Service::with('category');

        // Search by title or description or WhatsApp
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('service_category_id', $request->category_id);
        }

        // Filter by status (active, hold, inactive)
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active' || $request->status === 'published') {
                $query->where(function ($q) {
                    $q->where('status', 'active')->orWhere(function ($sub) {
                        $sub->whereNull('status')->where('is_published', true);
                    });
                });
            } elseif ($request->status === 'hold' || $request->status === 'on_hold') {
                $query->where('status', 'hold');
            } elseif ($request->status === 'inactive' || $request->status === 'draft') {
                $query->where(function ($q) {
                    $q->where('status', 'inactive')->orWhere(function ($sub) {
                        $sub->whereNull('status')->where('is_published', false);
                    });
                });
            }
        }

        // Order
        $services = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Metrics
        $totalServices = Service::count();
        $activeCount = Service::where(function ($q) {
            $q->where('status', 'active')->orWhere(function ($sub) {
                $sub->whereNull('status')->where('is_published', true);
            });
        })->count();
        $holdCount = Service::where('status', 'hold')->count();
        $inactiveCount = Service::where(function ($q) {
            $q->where('status', 'inactive')->orWhere(function ($sub) {
                $sub->whereNull('status')->where('is_published', false);
            });
        })->count();
        $publishedCount = $activeCount;
        $draftCount = $inactiveCount;
        $totalCategories = ServiceCategory::count();
        $totalClicks = Service::sum('click_count');

        $categories = ServiceCategory::orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();

        return view('admin.services.index', compact(
            'services',
            'categories',
            'totalServices',
            'activeCount',
            'holdCount',
            'inactiveCount',
            'publishedCount',
            'draftCount',
            'totalCategories',
            'totalClicks'
        ));
    }

    /**
     * Show form for creating a new service
     */
    public function create()
    {
        $categories = ServiceCategory::orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();
        return view('admin.services.create', compact('categories'));
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'service_category_id' => 'nullable|exists:service_categories,id',
            'short_description' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'whatsapp_number' => 'required|string|max:30',
            'whatsapp_message' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:active,hold,inactive',
            'is_published' => 'nullable|boolean',
        ]);

        // Generate unique slug
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Service::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        // WhatsApp prefilled message default
        $whatsappMessage = $validated['whatsapp_message'] ?? null;
        if (empty($whatsappMessage)) {
            $whatsappMessage = "Hello REIAC Consultancy, I am interested in {$validated['title']}. Please guide me with more information.";
        }

        // Determine status
        $status = $validated['status'] ?? ($request->has('is_published') && $request->is_published ? 'active' : 'inactive');
        $isPublished = ($status === 'active');

        Service::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'service_category_id' => $validated['service_category_id'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'whatsapp_number' => $validated['whatsapp_number'],
            'whatsapp_message' => $whatsappMessage,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $status,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service listing created successfully!');
    }

    /**
     * Show form for editing an existing service
     */
    public function edit(Service $service)
    {
        $categories = ServiceCategory::orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * Update an existing service
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'service_category_id' => 'nullable|exists:service_categories,id',
            'short_description' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'whatsapp_number' => 'required|string|max:30',
            'whatsapp_message' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:active,hold,inactive',
            'is_published' => 'nullable|boolean',
        ]);

        // Generate unique slug
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Handle image replacement or removal
        $imagePath = $service->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('services', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        // WhatsApp prefilled message default
        $whatsappMessage = $validated['whatsapp_message'] ?? null;
        if (empty($whatsappMessage)) {
            $whatsappMessage = "Hello REIAC Consultancy, I am interested in {$validated['title']}. Please guide me with more information.";
        }

        // Determine status
        $status = $validated['status'] ?? ($request->has('is_published') && $request->is_published ? 'active' : 'inactive');
        $isPublished = ($status === 'active');

        $service->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'service_category_id' => $validated['service_category_id'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'whatsapp_number' => $validated['whatsapp_number'],
            'whatsapp_message' => $whatsappMessage,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $status,
            'is_published' => $isPublished,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Delete a service
     */
    public function destroy(Service $service)
    {
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }

    /**
     * Update service status (active, hold, inactive) directly
     */
    public function updateStatus(Request $request, Service $service)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,hold,inactive',
        ]);

        $service->status = $validated['status'];
        $service->is_published = ($validated['status'] === 'active');
        $service->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $service->status,
                'status_label' => $service->status_label,
                'is_published' => $service->is_published,
                'message' => "Service status updated to {$service->status_label} successfully.",
            ]);
        }

        return redirect()->back()->with(
            'success',
            "Service status updated to {$service->status_label} successfully."
        );
    }

    /**
     * Toggle publish/unpublish status
     */
    public function togglePublish(Request $request, Service $service)
    {
        if ($service->status === 'active') {
            $service->status = 'inactive';
            $service->is_published = false;
        } else {
            $service->status = 'active';
            $service->is_published = true;
        }
        $service->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $service->status,
                'status_label' => $service->status_label,
                'is_published' => $service->is_published,
                'message' => $service->status === 'active' ? 'Service activated & published.' : 'Service deactivated.',
            ]);
        }

        return redirect()->back()->with(
            'success',
            $service->status === 'active' ? 'Service activated & published.' : 'Service deactivated.'
        );
    }

    /**
     * Update order of services
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:services,id',
            'orders.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['orders'] as $item) {
            Service::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully.']);
    }
}
