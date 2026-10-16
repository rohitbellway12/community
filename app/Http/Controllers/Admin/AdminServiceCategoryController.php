<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminServiceCategoryController extends Controller
{
    /**
     * Display service categories list
     */
    public function index()
    {
        $categories = ServiceCategory::withCount('services')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(15);

        $totalCategories = ServiceCategory::count();
        $activeCategories = ServiceCategory::where('is_active', true)->count();

        return view('admin.services.categories', compact(
            'categories',
            'totalCategories',
            'activeCategories'
        ));
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'slug' => 'nullable|string|max:190|unique:service_categories,slug',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (ServiceCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        ServiceCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'briefcase',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Service category created successfully!');
    }

    /**
     * Update category
     */
    public function update(Request $request, ServiceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'slug' => 'nullable|string|max:190|unique:service_categories,slug,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? $category->icon,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Service category updated successfully!');
    }

    /**
     * Delete category
     */
    public function destroy(ServiceCategory $category)
    {
        // Nullify foreign keys or delete
        $category->delete();

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Service category deleted successfully!');
    }
}
