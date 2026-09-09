<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $tags = Tag::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->withCount('posts')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('tags.index', compact('tags', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:tags,name',
            ],
        ]);

        $name = trim($validated['name']);

        Tag::create([
            'name' => $name,
            'slug' => $this->generateUniqueSlug($name),
        ]);

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tags', 'name')->ignore($tag->id),
            ],
        ]);

        $name = trim($validated['name']);

        $tag->update([
            'name' => $name,
            'slug' => $this->generateUniqueSlug($name, $tag->id),
        ]);

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $tag->posts()->detach();
        $tag->delete();

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag deleted successfully.');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'tag';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Tag::where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}