<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Share;
use App\Models\Tag;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    use ApiResponse;

    protected const ALLOWED_IMAGE_EXTS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif', 'avif', 'bmp', 'tiff', 'svg', 'ico'];
    protected const ALLOWED_VIDEO_EXTS = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'qt', '3gp', 'ogg', 'wmv'];

    /**
     * List posts with search, filter, and sort.
     * Public access: only published + public posts.
     */
    public function index(Request $request): JsonResponse
    {
        $sort       = $request->input('sort', 'latest');
        $tagSlug    = $request->input('tag');
        $groupSlug  = $request->input('group');
        $search     = $request->string('search')->trim();
        $user       = $request->user();

        $postsQuery = Post::query()
            ->where('status', PostStatus::PUBLISHED->value)
            ->with([
                'user.profile.country',
                'category',
                'media',
                'tags',
            ])
            ->withCount(['likes', 'comments', 'shares']);

        if ($groupSlug) {
            $activeGroup = \App\Models\Group::where('slug', $groupSlug)
                ->orWhere('id', $groupSlug)
                ->first();

            if (!$activeGroup) {
                return $this->errorResponse('Group not found.', 404);
            }

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
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', function ($cq) use ($request) {
                    $cq->where('slug', $request->input('category'));
                });
            })
            ->when($request->filled('tag'), function ($query) use ($request) {
                $query->whereHas('tags', function ($tagQuery) use ($request) {
                    $tagQuery->where('slug', $request->input('tag'));
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
                '(likes_count * 2 + comments_count * 3 + shares_count * 2 + views_count * 0.1) '
                . '/ POW(TIMESTAMPDIFF(HOUR, created_at, NOW()) + 2, 1.5) DESC'
            );
        } elseif (in_array($sort, ['discussed', 'most_discussed'], true)) {
            $postsQuery->orderByDesc('comments_count')
                ->orderByDesc('likes_count')
                ->orderByDesc('created_at');
        } else {
            $postsQuery->latest();
        }

        $posts = $postsQuery->paginate(10)->withQueryString();

        return $this->successResponse(
            PostResource::collection($posts)->response()->getData(true),
            'Posts retrieved successfully.'
        );
    }

    /**
     * Show a single post with comments, media, tags, etc.
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        if (!(new \App\Policies\PostPolicy)->view($request->user(), $post)) {
            return $this->errorResponse('This discussion is private.', 403);
        }

        $post->increment('views_count');

        $post->load([
            'user.profile.country',
            'category',
            'tags',
            'media',
            'group',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->latest()
                    ->take(4)
                    ->with([
                        'user.profile',
                        'replies' => function ($replyQuery) {
                            $replyQuery->oldest()->with('user.profile');
                        },
                    ]);
            },
            'likes'   => fn ($q) => $q->when($request->user(), fn ($qq) => $qq->where('user_id', $request->user()->id)),
            'savedBy' => fn ($q) => $q->when($request->user(), fn ($qq) => $qq->where('user_id', $request->user()->id)),
        ]);

        $post->loadCount(['likes', 'comments', 'shares']);

        return $this->successResponse(
            new PostResource($post),
            'Post retrieved successfully.'
        );
    }

    /**
     * Store a new post.
     */
    public function store(Request $request): JsonResponse
    {
        @ini_set('max_execution_time', 300);
        @ini_set('memory_limit', '512M');

        if (!empty($_FILES['media']['error'])) {
            foreach ((array) $_FILES['media']['error'] as $err) {
                if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                    return $this->errorResponse(
                        'The uploaded file exceeds the server upload limit. Please try with a smaller image or compressed photo.',
                        422
                    );
                }
            }
        }

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'group_id'    => ['nullable', 'integer', 'exists:groups,id'],
            'visibility'  => ['nullable', 'in:public,private'],
            'tags'        => ['nullable', 'array', 'max:5'],
            'tags.*'      => ['integer', 'distinct', 'exists:tags,id'],
            'media'       => ['nullable', 'array', 'max:10'],
            'media.*'     => ['file', 'max:102400', $this->mediaRule()],
        ], [
            'title.required'        => 'Please provide a title for your discussion.',
            'content.required'      => 'Please write some content for your discussion.',
            'category_id.required'  => 'Please select a category.',
            'category_id.exists'    => 'The selected category is invalid.',
            'media.max'             => 'You can upload at most 10 media files.',
            'media.*.max'           => 'Each file cannot exceed 100MB.',
            'media.*.file'          => 'The uploaded file failed to transfer. The image might be too large or the connection dropped.',
            'media.*.uploaded'      => 'The uploaded file failed to transfer. The image might be too large or the connection dropped.',
        ]);

        $user = $request->user();

        $visibility = 'public';

        if (!empty($validated['group_id'])) {
            $group = \App\Models\Group::findOrFail($validated['group_id']);

            $isMember = (int) $group->owner_id === (int) $user->id
                || $group->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('status', 'active')
                    ->exists();

            if (!$isMember) {
                return $this->errorResponse(
                    'You must be a member of this group to create posts here.',
                    403
                );
            }

            $visibility = $group->visibility === 'private'
                ? 'private'
                : ($validated['visibility'] ?? 'public');
        } else {
            $visibility = $validated['visibility'] ?? 'public';
        }

        $post = DB::transaction(function () use ($request, $validated, $visibility, $user) {
            $post = Post::create([
                'user_id'    => $user->id,
                'category_id'  => $validated['category_id'],
                'group_id'    => $validated['group_id'] ?? null,
                'title'       => $validated['title'],
                'slug'        => Str::slug($validated['title']) . '-' . uniqid(),
                'content'     => $validated['content'],
                'visibility'  => $visibility,
                'status'      => PostStatus::PUBLISHED,
            ]);

            $post->tags()->sync($validated['tags'] ?? []);

            if ($request->hasFile('media')) {
                $sortOrder = 0;

                foreach ($request->file('media') as $file) {
                    if (!$file->isValid()) {
                        continue;
                    }

                    $filePath  = $file->store('post-media', 'public');
                    $mimeType  = strtolower($file->getMimeType() ?: '');
                    $ext       = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension());
                    $isVideo   = str_starts_with($mimeType, 'video/') || in_array($ext, self::ALLOWED_VIDEO_EXTS);

                    $post->media()->create([
                        'file_path'  => $filePath,
                        'file_type'  => $isVideo ? 'video' : 'image',
                        'file_size'  => $file->getSize(),
                        'mime_type'  => $mimeType,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }

            return $post;
        });

        $post->load([
            'user.profile.country',
            'category',
            'tags',
            'media',
        ]);
        $post->loadCount(['likes', 'comments', 'shares']);

        return $this->successResponse(
            new PostResource($post),
            'Post created successfully.',
            201
        );
    }

    /**
     * Update an existing post (author only).
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        @ini_set('max_execution_time', 300);
        @ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'group_id'    => ['nullable', 'integer', 'exists:groups,id'],
            'visibility'  => ['nullable', 'in:public,private'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['integer', 'distinct', 'exists:tags,id'],
            'media'       => ['nullable', 'array', 'max:10'],
            'media.*'     => ['nullable', 'file', 'max:102400', $this->mediaRule()],
            'deleted_media' => ['nullable', 'array'],
            'deleted_media.*' => ['integer', 'exists:post_media,id'],
        ]);

        return DB::transaction(function () use ($request, $post, $validated) {
            $post->update([
                'title'       => $validated['title'],
                'category_id'  => $validated['category_id'],
                'group_id'    => $validated['group_id'] ?? null,
                'content'     => $validated['content'],
            ]);

            if (isset($validated['visibility']) && !$post->group_id) {
                $post->update(['visibility' => $validated['visibility']]);
            }

            $post->tags()->sync($validated['tags'] ?? []);

            if (!empty($validated['deleted_media'])) {
                $post->media()
                    ->whereIn('id', $validated['deleted_media'])
                    ->each(function ($media) {
                        if ($media->file_path) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
                        }
                        $media->delete();
                    });
            }

            if ($request->hasFile('media')) {
                $sortOrder = $post->media()->count();

                foreach ($request->file('media') as $file) {
                    if (!$file->isValid()) {
                        continue;
                    }

                    $filePath  = $file->store('post-media', 'public');
                    $mimeType  = strtolower($file->getMimeType() ?: '');
                    $ext       = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension());
                    $isVideo   = str_starts_with($mimeType, 'video/') || in_array($ext, self::ALLOWED_VIDEO_EXTS);

                    $post->media()->create([
                        'file_path'  => $filePath,
                        'file_type'  => $isVideo ? 'video' : 'image',
                        'file_size'  => $file->getSize(),
                        'mime_type'  => $mimeType,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }

            $post->load([
                'user.profile.country',
                'category',
                'tags',
                'media',
            ]);

            return $this->successResponse(
                new PostResource($post),
                'Post updated successfully.'
            );
        });
    }

    /**
     * Delete a post (author only).
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        foreach ($post->media as $media) {
            if ($media->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
            }
        }

        $post->delete();

        return $this->successResponse(
            ['deleted' => true],
            'Post deleted successfully.'
        );
    }

    /**
     * Toggle like on a post.
     */
    public function like(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $post->increment('likes_count');
            $liked = true;

            if ((int) $post->user_id !== (int) $user->id) {
                $post->loadMissing('user');
                if ($post->user) {
                    $post->user->notify(
                        new \App\Notifications\PostLikedNotification($user, $post)
                    );
                }
            }
        }

        $post->loadCount('likes');

        return $this->successResponse([
            'liked'      => $liked,
            'likes_count' => (int) $post->likes_count,
        ], $liked ? 'Post liked.' : 'Post unliked.');
    }

    /**
     * Toggle save on a post.
     */
    public function save(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $saved = $post->savedBy()
            ->where('user_id', $user->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
        } else {
            $post->savedBy()->create(['user_id' => $user->id]);
            $isSaved = true;
        }

        return $this->successResponse([
            'saved' => $isSaved,
        ], $isSaved ? 'Post saved.' : 'Post unsaved.');
    }

    /**
     * Share a post.
     */
    public function share(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $existingShare = Share::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingShare) {
            return $this->successResponse([
                'shared'         => true,
                'already_shared'  => true,
                'shares_count'   => (int) $post->shares_count,
                'url'             => route('community.posts.show', $post),
            ], 'Post link copied.');
        }

        Share::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $post->increment('shares_count');

        if ((int) $post->user_id !== (int) $user->id) {
            $post->loadMissing('user');
            if ($post->user) {
                $post->user->notify(
                    new \App\Notifications\PostSharedNotification($user, $post)
                );
            }
        }

        $post->loadCount('shares');

        return $this->successResponse([
            'shared'          => true,
            'already_shared'  => false,
            'shares_count'    => (int) $post->shares_count,
            'url'             => route('community.posts.show', $post),
        ], 'Post shared successfully.');
    }

    /**
     * Mark / unmark a post as solved (author only).
     */
    public function markSolved(Request $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        $post->update([
            'is_solved' => !$post->is_solved,
        ]);

        return $this->successResponse([
            'is_solved' => (bool) $post->is_solved,
        ], $post->is_solved ? 'Post marked as solved.' : 'Post marked as unsolved.');
    }

    /**
     * Validation rule for media: allow images and videos only.
     */
    protected function mediaRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if (!$value instanceof \Illuminate\Http\UploadedFile) {
                return;
            }

            $origExt = strtolower($value->getClientOriginalExtension() ?: '');
            $guessedExt = strtolower($value->guessExtension() ?: '');
            $ext = $origExt ?: $guessedExt;
            $mime = strtolower($value->getMimeType() ?: '');

            $isImage = str_starts_with($mime, 'image/')
                || in_array($ext, self::ALLOWED_IMAGE_EXTS)
                || (in_array($mime, ['application/octet-stream', 'binary/octet-stream']) && in_array($ext, self::ALLOWED_IMAGE_EXTS));

            $isVideo = str_starts_with($mime, 'video/')
                || in_array($ext, self::ALLOWED_VIDEO_EXTS)
                || (in_array($mime, ['application/octet-stream', 'binary/octet-stream']) && in_array($ext, self::ALLOWED_VIDEO_EXTS));

            if (!$isImage && !$isVideo) {
                $name = $value->getClientOriginalName() ?: 'file';
                $fail("The file '{$name}' is not supported. Please upload an image (JPG, PNG, WEBP, HEIC) or video (MP4, MOV).");
            }
        };
    }
}
