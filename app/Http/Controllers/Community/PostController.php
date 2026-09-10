<?php

namespace App\Http\Controllers\Community;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Like;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Share;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display active posts for normal community users.
     */
public function index(Request $request)
{
    $sort = $request->input('sort', 'latest');
    $categorySlug = $request->input('category');
    $tagSlug = $request->input('tag');
    $groupSlug = $request->input('group');
    $search = $request->string('search')->trim();

    $user = $request->user();

    $categories = Category::query()
        ->orderBy('name')
        ->get();

    $tags = Tag::query()
        ->orderBy('name')
        ->get();

    $activeCategory = $categorySlug
        ? Category::where('slug', $categorySlug)->first()
        : null;

    $activeTag = $tagSlug
        ? Tag::where('slug', $tagSlug)->first()
        : null;

    $activeGroup = null;

    if ($groupSlug) {
        $activeGroup = Group::where('slug', $groupSlug)
            ->orWhere('id', $groupSlug)
            ->first();

        if (! $activeGroup) {
            abort(404, 'Group not found.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Posts
    |--------------------------------------------------------------------------
    | Only published + public posts are returned.
    | Group membership is NOT required for public posts.
    */

    $postsQuery = Post::query()
        ->where('status', 'published');

    if ($activeGroup) {
        $postsQuery->where('group_id', $activeGroup->id);
        if ($activeGroup->visibility === 'private') {
            if (!$user) {
                $postsQuery->whereRaw('1 = 0');
            } else {
                $isMemberOrOwner = (int) $activeGroup->owner_id === (int) $user->id
                    || $activeGroup->users()->where('users.id', $user->id)->wherePivot('status', 'active')->exists()
                    || in_array($user->role?->value ?? (string) $user->role, ['admin', 'super_admin'], true);

                if (!$isMemberOrOwner) {
                    $postsQuery->whereRaw('1 = 0');
                }
            }
        }
    } else {
        $postsQuery->where(function ($q) use ($user) {
            $q->where(function ($sub) {
                $sub->where('visibility', 'public')
                    ->where(function ($gSub) {
                        $gSub->whereNull('group_id')
                             ->orWhereHas('group', function ($g) {
                                 $g->where('visibility', 'public');
                             });
                    });
            });

            if ($user) {
                $q->orWhere('user_id', $user->id)
                  ->orWhereHas('group', function ($g) use ($user) {
                      $g->where('owner_id', $user->id)
                        ->orWhereHas('users', function ($uq) use ($user) {
                            $uq->where('users.id', $user->id)
                               ->where('group_user.status', 'active');
                        });
                  });
            }
        });
    }

    $postsQuery
        ->with([
            'user.profile.country',
            'category',
            'media',
            'tags',
            'group.users' => function ($query) use ($user) {
                if ($user) {
                    $query->where('user_id', $user->id);
                }
            },
            'comments' => function ($query) {
                $query
                    ->whereNull('parent_id')
                    ->latest()
                    ->take(4)
                    ->with([
                        'user.profile',
                        'replies' => function ($replyQuery) {
                            $replyQuery->oldest()->with('user.profile');
                        },
                    ]);
            },
        ])
        ->withCount([
            'likes',
            'comments',
            'shares',
        ])
        ->when($activeCategory, function ($query) use ($activeCategory) {
            $query->where('category_id', $activeCategory->id);
        })
        ->when($activeTag, function ($query) use ($activeTag) {
            $query->whereHas('tags', function ($tagQuery) use ($activeTag) {
                $tagQuery->where('tags.id', $activeTag->id);
            });
        })
        ->when($search->isNotEmpty(), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('tags', function ($tagQuery) use ($search) {
                        $tagQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user.profile', function ($profileQuery) use ($search) {
                        $profileQuery->where('username', 'like', "%{$search}%");
                    });
            });
        });

    if ($sort === 'trending') {
        $postsQuery->orderByRaw(
            '(likes_count * 2 + comments_count * 3 + shares_count * 2 + views_count * 0.1)
            / POW(TIMESTAMPDIFF(HOUR, created_at, NOW()) + 2, 1.5) DESC'
        );
    } elseif (in_array($sort, ['discussed', 'most_discussed'], true)) {
        $postsQuery
            ->orderByDesc('comments_count')
            ->orderByDesc('likes_count')
            ->orderByDesc('created_at');
    } else {
        $postsQuery->latest();
    }

    $posts = $postsQuery
        ->paginate(10)
        ->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | Trending Topics
    |--------------------------------------------------------------------------
    */

    $trendingTopics = Tag::query()
        ->select([
            'tags.id',
            'tags.name',
            'tags.slug',
        ])
        ->selectRaw('COUNT(DISTINCT post_tag.post_id) as posts_count')
        ->selectRaw('COUNT(DISTINCT likes.id) as total_likes')
        ->join('post_tag', 'post_tag.tag_id', '=', 'tags.id')
        ->join('posts', 'posts.id', '=', 'post_tag.post_id')
        ->leftJoin('likes', 'likes.post_id', '=', 'posts.id')
        ->whereNull('posts.deleted_at')
        ->where('posts.status', 'published')
        ->where('posts.visibility', 'public')
        ->groupBy(
            'tags.id',
            'tags.name',
            'tags.slug'
        )
        ->havingRaw('COUNT(DISTINCT likes.id) > 0')
        ->orderByDesc('total_likes')
        ->orderByDesc('posts_count')
        ->limit(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Top Contributors
    |--------------------------------------------------------------------------
    */

    $topContributors = User::getTopContributors(5);

    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

    if ($user) {
        $user->load('profile');

        $user->loadCount([
            'posts',
            'comments',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Stats
    |--------------------------------------------------------------------------
    */

    $profileStats = [];

    if ($user) {
        $profileStats = [
            'posts' => Post::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->count(),

            'comments' => Comment::where(
                'user_id',
                $user->id
            )->count(),

            'likes_received' => Like::whereHas(
                'post',
                function ($query) use ($user) {
                    $query
                        ->where('user_id', $user->id)
                        ->whereNull('deleted_at');
                }
            )->count(),

            'shares' => Share::where(
                'user_id',
                $user->id
            )->count(),
        ];
    }

    return view('community.index', compact(
        'posts',
        'categories',
        'tags',
        'activeCategory',
        'activeTag',
        'activeGroup',
        'sort',
        'search',
        'trendingTopics',
        'topContributors',
        'profileStats',
        'user'
    ));
}

    /**
     * Show create post page.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('community.create', compact('categories', 'tags'));
    }

    /**
     * Store a new post.
     */
public function store(Request $request)
{
    @ini_set('max_execution_time', 300);
    @ini_set('memory_limit', '512M');

    $validated = $request->validate([
        'title' => 'required|string|max:255',

        // Content is saved exactly as submitted.
        'content' => 'required|string',

        'category_id' => 'required|exists:categories,id',

        'group_id' => 'nullable|exists:groups,id',

        'visibility' => [
            'nullable',
            'in:public,private',
        ],

        'media' => 'nullable|array|max:10',

        'media.*' => [
            'file',
            'max:102400',
            function ($attribute, $value, $fail) {
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    return;
                }
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'qt', '3gp', 'ogg'];
                $ext = strtolower($value->getClientOriginalExtension());
                $mime = strtolower($value->getMimeType() ?: '');

                $isImage = str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $isVideo = str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'qt', '3gp', 'ogg']);

                if (!$isImage && !$isVideo) {
                    $fail("The {$attribute} must be an image (jpg, jpeg, png, webp, gif) or video (mp4, mov, avi, webm, mkv).");
                }
            },
        ],

        // TAGS
        'tags' => 'nullable|array|max:5',
        'tags.*' => [
            'integer',
            'distinct',
            'exists:tags,id',
        ],
    ]);

    /*
    |--------------------------------------------------------------------------
    | Determine Visibility
    |--------------------------------------------------------------------------
    */

    if (empty($validated['group_id'])) {
        $visibility = 'public';
    } else {
        $group = Group::findOrFail($validated['group_id']);

        $isMember = (int) $group->owner_id === (int) $request->user()->id
            || $group->users()
                ->where('user_id', $request->user()->id)
                ->wherePivot('status', 'active')
                ->exists();

        if (!$isMember) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'You must be a member of this group to create posts here.'
                );
        }

        if ($group->visibility === 'private') {
            $visibility = 'private';
        } else {
            $visibility = $validated['visibility'] ?? 'public';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Post + Tags + Media
    |--------------------------------------------------------------------------
    */

    DB::transaction(function () use ($request, $validated, $visibility) {

        $post = Post::create([
            'user_id' => $request->user()->id,

            'category_id' => $validated['category_id'],

            'group_id' => $validated['group_id'] ?? null,

            'title' => $validated['title'],

            'slug' => Str::slug($validated['title']) . '-' . uniqid(),

            // Preserve exact content
            'content' => $validated['content'],

            'visibility' => $visibility,

            'status' => PostStatus::PUBLISHED,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SAVE TAGS
        |--------------------------------------------------------------------------
        */

        $post->tags()->sync($validated['tags'] ?? []);

        /*
        |--------------------------------------------------------------------------
        | Upload Media
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('media')) {

            foreach ($request->file('media') as $index => $file) {

                if (!$file->isValid()) {
                    continue;
                }

                $filePath = $file->store('post-media', 'public');

                $mimeType = $file->getMimeType() ?: '';
                $ext = strtolower($file->getClientOriginalExtension());

                $isVideo = str_starts_with($mimeType, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'qt', '3gp', 'ogg']);
                $type = $isVideo ? 'video' : 'image';

                PostMedia::create([
                    'post_id' => $post->id,

                    'type' => $type,

                    'file_path' => $filePath,

                    'mime_type' => $mimeType,

                    'file_size' => $file->getSize(),

                    'sort_order' => $index,
                ]);
            }
        }
    });

    return back()->with(
        'success',
        'Post created successfully.'
    );
}

    /**
     * Display a single post.
     */
    public function show(Request $request, Post $post)
    {
        $post->loadMissing('group');
        $isPrivate = $post->visibility === 'private'
            || ($post->group && $post->group->visibility === 'private');

        if ($isPrivate) {
            if (!Auth::check()) {
                return redirect()->guest(route('login'))->with('error', 'Please log in to view this private discussion.');
            }

            $currentUser = Auth::user();
            $canView = (int) $post->user_id === (int) $currentUser->id
                || ($post->group && (int) $post->group->owner_id === (int) $currentUser->id)
                || ($post->group && $post->group->users()->where('users.id', $currentUser->id)->wherePivot('status', 'active')->exists())
                || in_array($currentUser->role?->value ?? (string) $currentUser->role, ['admin', 'super_admin'], true);

            if (!$canView) {
                abort(403, 'This discussion is private and only accessible to group members.');
            }
        }

        $post->increment('views_count');

        $post->load([
            'user.profile.country',
            'category',
            'tags',
            'media',
            'comments' => function ($query) {
                $query
                    ->whereNull('parent_id')
                    ->latest()
                    ->take(4)
                    ->with([
                        'user.profile',
                        'replies' => function ($replyQuery) {
                            $replyQuery->oldest()->with('user.profile');
                        },
                    ]);
            },
        ]);

        $post->loadCount([
            'likes',
            'comments',
            'shares',
        ]);

        $relatedPosts = Post::query()
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->where(function ($gq) {
                $gq->whereNull('group_id')
                   ->orWhereHas('group', function ($g) {
                       $g->where('visibility', 'public');
                   });
            })
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->with(['user.profile', 'category'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->limit(3)
            ->get();

        $user = $request->user();

        if ($user) {
            $user->load('profile');
            $user->loadCount(['posts', 'comments']);
        }

        // Fetch variables needed for sidebar/rightbar components
        $topContributors = User::getTopContributors(5);

        $trendingTopics = Category::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5)
            ->get();

        return view('community.post-show', compact(
            'post',
            'relatedPosts',
            'user',
            'topContributors',
            'trendingTopics'
        ));
    }

    /**
     * Show edit post page.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = Category::all();
        $tags = Tag::all();

        $post->load('tags', 'media');

        return view(
            'community.edit',
            compact('post', 'categories', 'tags')
        );
    }

    /**
     * Update an existing post.
     */
    public function update(Request $request, Post $post)
{
    Gate::authorize('update', $post);

    @ini_set('max_execution_time', 300);
    @ini_set('memory_limit', '512M');

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'category_id' => ['required', 'exists:categories,id'],
        'content' => ['required', 'string'],

        'media' => ['nullable', 'array', 'max:10'],

        'media.*' => [
            'nullable',
            'file',
            'max:102400',
            function ($attribute, $value, $fail) {
                if (!$value instanceof \Illuminate\Http\UploadedFile) {
                    return;
                }
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'qt', '3gp', 'ogg'];
                $ext = strtolower($value->getClientOriginalExtension());
                $mime = strtolower($value->getMimeType() ?: '');

                $isImage = str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $isVideo = str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'qt', '3gp', 'ogg']);

                if (!$isImage && !$isVideo) {
                    $fail("The {$attribute} must be an image (jpg, jpeg, png, webp, gif) or video (mp4, mov, avi, webm, mkv).");
                }
            },
        ],

        'tags' => ['nullable', 'array'],
        'tags.*' => ['exists:tags,id'],
    ]);

    return DB::transaction(function () use ($request, $post, $validated) {

        // Update post
        $post->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
        ]);

        // Add new media
        if ($request->hasFile('media')) {

            $sortOrder = $post->media()->count();

            foreach ($request->file('media') as $file) {

                if (! $file->isValid()) {
                    continue;
                }

                $path = $file->store('post-media', 'public');

                $mimeType = $file->getMimeType() ?: '';
                $ext = strtolower($file->getClientOriginalExtension());

                $isVideo = str_starts_with($mimeType, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'qt', '3gp', 'ogg']);
                $type = $isVideo ? 'video' : 'image';

                $post->media()->create([
                    'type' => $type,
                    'file_path' => $path,
                    'mime_type' => $mimeType,
                    'file_size' => $file->getSize(),
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        // Sync tags
        $post->tags()->sync($validated['tags'] ?? []);

        return redirect()
            ->route('community.posts.show', $post)
            ->with('success', 'Discussion updated successfully.');
    });
}

    /**
     * Delete post.
     */
    public function destroy(Request $request, Post $post)
    {
        Gate::authorize('delete', $post);

        $groupId = $post->group_id;
        $postId = $post->id;

        $post->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Discussion deleted successfully.',
            ]);
        }

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))
                ->with('success', 'Discussion deleted successfully.');
        }

        $previousUrl = url()->previous();
        if (
            str_contains($previousUrl, '/posts/' . $postId) ||
            $previousUrl === route('community.posts.show', ['post' => $postId])
        ) {
            if ($groupId) {
                return redirect()
                    ->route('community.groups.show', $groupId)
                    ->with('success', 'Discussion deleted successfully.');
            }

            return redirect()
                ->route('community.index')
                ->with('success', 'Discussion deleted successfully.');
        }

        return redirect()
            ->back()
            ->with('success', 'Discussion deleted successfully.');
    }

    /**
     * Toggle saved post.
     */
    public function toggleSave(Request $request, Post $post)
    {
        $user = $request->user();

        $savedPost = $post
            ->savedBy()
            ->where('user_id', $user->id)
            ->first();

        if ($savedPost) {
            $savedPost->delete();
            $isSaved = false;
        } else {
            $post->savedBy()->create([
                'user_id' => $user->id,
            ]);

            $isSaved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $isSaved,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Display both Active and Deactive/Draft/Archived posts for Admin panel.
     */
    public function adminIndex(Request $request)
    {
        $search     = $request->string('search')->trim();
        $status     = $request->input('status');
        $categoryId = $request->input('category_id');

        $posts = Post::query()
            ->with(['user.profile', 'category', 'media'])
            ->withCount(['comments', 'likes'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        // Exact DB summary statistics
        $totalPostsCount     = Post::count();
        $publishedPostsCount = Post::where('status', 'published')->count();
        $draftPostsCount     = Post::where('status', 'draft')->count();
        $archivedPostsCount  = Post::where('status', 'archived')->count();
        $solvedPostsCount    = Post::where('is_solved', true)->count();

        return view('admin.posts', compact(
            'posts', 
            'categories',
            'totalPostsCount',
            'publishedPostsCount',
            'draftPostsCount',
            'archivedPostsCount',
            'solvedPostsCount'
        ));
    }

    /**
     * Update post status.
     */
    public function updateStatus(Request $request, Post $post)
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $post->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Post status updated successfully.'
        );
    }

    /**
     * Toggle post solved status.
     */
    public function toggleSolved(Post $post)
    {
        $post->update([
            'is_solved' => ! $post->is_solved,
        ]);

        return back()->with(
            'success',
            'Post solved status updated.'
        );
    }

    /**
     * Update an existing post from the admin panel.
     */
    public function adminUpdate(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string'],
            'is_solved' => ['nullable', 'boolean'],
            'media.*' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,mp4',
                'max:102400',
            ],
        ]);

        return DB::transaction(function () use ($request, $post, $validated) {
            $post->update([
                'title' => $validated['title'],
                'category_id' => $validated['category_id'],
                'content' => $validated['content'],
                'status' => $validated['status'],
                'is_solved' => $request->has('is_solved'),
            ]);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('post-media', 'public');

                    $post->media()->create([
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                    ]);
                }
            }

            return redirect()
                ->route('admin.posts')
                ->with('success', 'Post updated successfully.');
        });
    }

    /**
     * Delete a post from the admin panel.
     */
    public function adminDestroy(Post $post)
    {
        $post->tags()->detach();
        $post->delete();

        return back()->with(
            'success',
            'Post deleted successfully.'
        );
    }

    /**
     * Permanently delete post (Admin).
     */
    public function forceDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        $post->tags()->detach();

        $post->forceDelete();

        return back()->with(
            'success',
            'Post permanently deleted.'
        );
    }

    /**
     * Restore soft-deleted post (Admin).
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        $post->restore();

        return back()->with(
            'success',
            'Post restored successfully.'
        );
    }
}
