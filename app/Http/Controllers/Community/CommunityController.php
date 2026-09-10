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

        $topContributors = User::getTopContributors(5);

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
            if ($post->likes_count > 0) {
                $post->decrement('likes_count');
            }

            $liked = false;

            // Clean up unread like notification if unliked
            if ($post->user) {
                $post->user->unreadNotifications()
                    ->where('type', PostLikedNotification::class)
                    ->get()
                    ->filter(function ($n) use ($user, $post) {
                        $d = is_array($n->data) ? $n->data : [];
                        return (int) ($d['user_id'] ?? 0) === (int) $user->id
                            && (int) ($d['post_id'] ?? 0) === (int) $post->id;
                    })
                    ->each->delete();
            }
        } else {
            $post->likes()->create([
                'user_id' => $user->id,
            ]);
            $post->increment('likes_count');

            $liked = true;

            // Don't notify yourself
            if ((int) $post->user_id !== (int) $user->id) {
                $post->loadMissing(['user', 'group']);
                if ($post->user) {
                    $post->user->notify(
                        new PostLikedNotification($user, $post)
                    );
                }
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

        $skip = (int) ($validated['skip'] ?? 0);
        $take = 5;

        // Total count of top-level comments
        $totalTopLevel = $post->comments()
            ->whereNull('parent_id')
            ->count();

        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with([
                'user.profile',
                'replies' => function ($q) {
                    $q->oldest()->with('user.profile');
                },
            ])
            ->latest()
            ->skip($skip)
            ->take($take)
            ->get()
            ->map(function ($comment) {
                $user = $comment->user;
                $profile = $user?->profile;
                $avatar = $profile?->avatar
                    ? (str_starts_with($profile->avatar, 'http') ? $profile->avatar : asset('storage/' . ltrim($profile->avatar, '/')))
                    : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff';

                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'parent_id' => $comment->parent_id,
                    'created_at_human' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'id' => $user?->id,
                        'name' => $user?->name ?? 'User',
                        'avatar' => $avatar,
                    ],
                    'replies' => $comment->replies->map(function ($reply) {
                        $replyUser = $reply->user;
                        $replyProfile = $replyUser?->profile;
                        $replyAvatar = $replyProfile?->avatar
                            ? (str_starts_with($replyProfile->avatar, 'http') ? $replyProfile->avatar : asset('storage/' . ltrim($replyProfile->avatar, '/')))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($replyUser?->name ?? 'User') . '&background=0c1b33&color=fff';

                        return [
                            'id' => $reply->id,
                            'content' => $reply->content,
                            'parent_id' => $reply->parent_id,
                            'created_at_human' => $reply->created_at->diffForHumans(),
                            'user' => [
                                'id' => $replyUser?->id,
                                'name' => $replyUser?->name ?? 'User',
                                'avatar' => $replyAvatar,
                            ],
                        ];
                    })->values(),
                ];
            });

        $hasMore = ($skip + $comments->count()) < $totalTopLevel;

        return response()->json([
            'success' => true,
            'comments' => $comments,
            'has_more' => $hasMore,
            'total_top_level' => $totalTopLevel,
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
