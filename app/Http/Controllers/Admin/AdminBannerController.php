<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBannerController extends Controller
{
    /**
     * Display all banners for admin management
     */
    public function index()
    {
        $banners = Banner::latest()->paginate(15);
        $totalBanners = Banner::count();
        $activeBannersCount = Banner::where('status', 'active')->count();
        $latestActiveBanner = Banner::where('status', 'active')->latest()->first();

        return view('admin.banners.index', compact(
            'banners',
            'totalBanners',
            'activeBannersCount',
            'latestActiveBanner'
        ));
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'link_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        Banner::create([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'link_url' => $validated['link_url'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner created successfully!');
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'link_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = $banner->image_path;
        if ($request->hasFile('image')) {
            // Delete old file if stored locally
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        $banner->update([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'link_url' => $validated['link_url'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'status' => $validated['status'] ?? $banner->status,
            'sort_order' => $validated['sort_order'] ?? $banner->sort_order,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner updated successfully!');
    }

    /**
     * Toggle active/inactive status
     */
    public function updateStatus(Request $request, Banner $banner)
    {
        $newStatus = $banner->status === 'active' ? 'inactive' : 'active';
        $banner->update(['status' => $newStatus]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Banner status updated to ' . ucfirst($newStatus),
            ]);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner status updated to ' . ucfirst($newStatus));
    }

    /**
     * Remove the specified banner
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner deleted successfully!');
    }
}
