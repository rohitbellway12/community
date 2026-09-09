<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Share;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\PostCommentedNotification;
use App\Notifications\PostLikedNotification;
use App\Notifications\PostSharedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function profile(Request $request): View
    {
        $user = $request->user();

        $user->loadCount([
            'posts',
            'comments',
        ]);

        $likesReceivedCount = $user->posts()
            ->withCount('likes')
            ->get()
            ->sum('likes_count');

        $posts = $user->posts()
            ->with([
                'category',
                'tags',
                'media',
            ])
            ->withCount([
                'likes',
                'comments',
            ])
            ->latest()
            ->get();

        return view('community.profile', [
            'user' => $user,
            'posts' => $posts,
            'likesReceivedCount' => $likesReceivedCount,
            'sharesCount' => 0,
        ]);
    }

    public function create(Request $request): View
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        return view('community.create', [
            'user' => $request->user(),
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'content' => [
                'required',
                'string',
                'max:10000',
            ],
            'tags' => [
                'nullable',
                'array',
                'max:5',
            ],
            'tags.*' => [
                'integer',
                'distinct',
                'exists:tags,id',
            ],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $post = Post::create([
                'user_id' => $request->user()->id,
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
            ]);

            $post->tags()->sync($validated['tags'] ?? []);
        });

        return redirect()
            ->route('community.index')
            ->with('success', 'Discussion published successfully.');
    }

    public function activity(Request $request): View
    {
        $posts = $request->user()
            ->posts()
            ->with([
                'category',
                'tags',
                'media',
            ])
            ->withCount([
                'likes',
                'comments',
            ])
            ->latest()
            ->paginate(10);

        return view('community.activity', [
            'user' => $request->user(),
            'posts' => $posts,
        ]);
    }

    public function saved(Request $request): View
    {
        dd('hello');
        $user = $request->user();
        $user->load('profile');

        $posts = Post::query()
            ->whereHas('savedBy', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with([
                'user',
                'category',
                'tags',
                'media',
            ])
            ->withCount([
                'likes',
                'comments',
            ])
            ->latest()
            ->paginate(10);

        $topContributors = User::query()
            ->select([
                'users.id',
                'users.name',
            ])
            ->with([
                'profile:id,user_id,username,avatar',
            ])
            ->withCount([
                'posts' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'comments' => function ($query) {
                    $query->whereNull('deleted_at');
                },
            ])
            ->selectSub(function ($query) {
                $query->from('posts')
                    ->whereColumn('posts.user_id', 'users.id')
                    ->whereNull('posts.deleted_at')
                    ->selectRaw('COUNT(*) * 5');
            }, 'post_points')
            ->selectSub(function ($query) {
                $query->from('comments')
                    ->whereColumn('comments.user_id', 'users.id')
                    ->whereNull('comments.deleted_at')
                    ->selectRaw('COUNT(*) * 3');
            }, 'comment_points')
            ->selectSub(function ($query) {
                $query->from('likes')
                    ->join('posts', 'posts.id', '=', 'likes.post_id')
                    ->whereColumn('posts.user_id', 'users.id')
                    ->whereNull('posts.deleted_at')
                    ->selectRaw('COUNT(*) * 2');
            }, 'like_points')
            ->selectSub(function ($query) {
                $query->from('shares')
                    ->join('posts', 'posts.id', '=', 'shares.post_id')
                    ->whereColumn('posts.user_id', 'users.id')
                    ->whereNull('posts.deleted_at')
                    ->selectRaw('COUNT(*) * 2');
            }, 'share_points')
            ->selectRaw('
                (
                    (
                        SELECT COUNT(*)
                        FROM posts
                        WHERE posts.user_id = users.id
                        AND posts.deleted_at IS NULL
                    ) * 5
                    +
                    (
                        SELECT COUNT(*)
                        FROM comments
                        WHERE comments.user_id = users.id
                        AND comments.deleted_at IS NULL
                    ) * 3
                    +
                    (
                        SELECT COUNT(*)
                        FROM likes
                        INNER JOIN posts ON posts.id = likes.post_id
                        WHERE posts.user_id = users.id
                        AND posts.deleted_at IS NULL
                    ) * 2
                    +
                    (
                        SELECT COUNT(*)
                        FROM shares
                        INNER JOIN posts ON posts.id = shares.post_id
                        WHERE posts.user_id = users.id
                        AND posts.deleted_at IS NULL
                    ) * 2
                ) AS contributor_points
            ')
            ->where(function ($query) {
                $query->whereHas('posts', function ($postQuery) {
                    $postQuery->whereNull('deleted_at');
                })->orWhereHas('comments', function ($commentQuery) {
                    $commentQuery->whereNull('deleted_at');
                });
            })
            ->orderByDesc('contributor_points')
            ->orderByDesc('posts_count')
            ->orderByDesc('comments_count')
            ->limit(4)
            ->get();

        return view('community.saved', [
            'user' => $user,
            'profile' => $user->profile,
            'posts' => $posts,
            'topContributors' => $topContributors,
        ]);
    }

    public function like(Request $request, Post $post): JsonResponse
    {
        if ($request->user() === null) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'redirect' => route('login'),
            ], 401);
        }

        $user = $request->user();

        $like = $post->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();

            $liked = false;
        } else {
            $post->likes()->create([
                'user_id' => $user->id,
            ]);

            $liked = true;

            // Don't notify yourself
            if ($post->user_id !== $user->id) {
                $post->user->notify(
                    new PostLikedNotification($user, $post)
                );
            }
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }

  public function storeComment(
    Request $request,
    Post $post
): JsonResponse {
    if ($request->user() === null) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.',
            'redirect' => route('login'),
        ], 401);
    }

    $validated = $request->validate([
        'content' => [
            'required',
            'string',
            'max:1000',
        ],

        'parent_id' => [
            'nullable',
            'integer',
            'exists:comments,id',
        ],
    ]);

    $user = $request->user();

    /*
    |--------------------------------------------------------------------------
    | Check parent comment
    |--------------------------------------------------------------------------
    */

    $parentComment = null;

    if (!empty($validated['parent_id'])) {

        $parentComment = Comment::find($validated['parent_id']);

        if (!$parentComment) {
            return response()->json([
                'success' => false,
                'message' => 'Parent comment not found.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Parent comment must belong to the same post
        |--------------------------------------------------------------------------
        */

        if ((int) $parentComment->post_id !== (int) $post->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid parent comment.',
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create comment / reply
    |--------------------------------------------------------------------------
    */

    $comment = $post->comments()->create([
        'user_id'   => $user->id,
        'parent_id' => $parentComment?->id,
        'content'   => trim($validated['content']),
    ]);

    $comment->load('user');

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    */

    if ($parentComment) {

        // This is a reply.
        if ($parentComment->user_id !== $user->id) {
            $parentComment->user->notify(
                new PostCommentedNotification(
                    $user,
                    $post,
                    $comment->content
                )
            );
        }

    } else {

        // This is a normal comment.
        if ($post->user_id !== $user->id) {
            $post->user->notify(
                new PostCommentedNotification(
                    $user,
                    $post,
                    $comment->content
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,

        'comment' => [
            'id' => $comment->id,

            'content' => $comment->content,

            'parent_id' => $comment->parent_id,

            'is_reply' => $comment->parent_id !== null,

            'created_at_human' => $comment->created_at->diffForHumans(),

            'user' => [
                'id' => $comment->user->id,

                'name' => $comment->user->name,

                'avatar' => $comment->user->avatar
                    ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&h=100&fit=crop&crop=faces',
            ],
        ],
    ]);
}
    public function loadMoreComments(
        Request $request,
        Post $post
    ): JsonResponse {
        $validated = $request->validate([
            'skip' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $skip = $validated['skip'] ?? 0;

        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->skip($skip)
            ->take(5)
            ->get()
            ->map(fn ($comment) => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at_human' => $comment->created_at->diffForHumans(),
                'user' => [
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar
                        ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&h=100&fit=crop&crop=faces',
                ],
            ]);

        return response()->json([
            'success' => true,
            'comments' => $comments,
        ]);
    }

    public function share(
        Request $request,
        Post $post
    ): JsonResponse {

        if ($request->user() === null) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'redirect' => route('login'),
            ], 401);
        }

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate shares
        |--------------------------------------------------------------------------
        */

        $existingShare = Share::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Already shared
        |--------------------------------------------------------------------------
        */

        if ($existingShare) {
            return response()->json([
                'success' => true,
                'message' => 'Post link copied.',
                'shared' => true,
                'already_shared' => true,
                'shares_count' => $post->shares()->count(),
                'url' => route('community.posts.show', $post),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Share
        |--------------------------------------------------------------------------
        */

        Share::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Notify Post Owner
        |--------------------------------------------------------------------------
        */

        if ($post->user_id !== $user->id) {

            $post->loadMissing('user');

            $post->user->notify(
                new PostSharedNotification(
                    $user,
                    $post
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Post shared successfully.',
            'shared' => true,
            'already_shared' => false,
            'shares_count' => $post->shares()->count(),
            'url' => route('community.posts.show', $post),
        ]);
    }
}
