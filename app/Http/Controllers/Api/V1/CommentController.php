<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Post;
use App\Notifications\CommentRepliedNotification;
use App\Notifications\PostCommentedNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use ApiResponse;

    /**
     * List top-level comments for a post (paginated, with first 4 replies each).
     */
    public function index(Request $request, Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->with([
                'user.profile',
                'replies' => function ($q) {
                    $q->where('status', 'active')
                        ->oldest()
                        ->take(4)
                        ->with('user.profile');
                },
                'likes'   => fn ($q) => $q->when($request->user(), fn ($qq) => $qq->where('user_id', $request->user()->id)),
            ])
            ->latest()
            ->withCount('likes')
            ->paginate(10)
            ->withQueryString();

        return $this->successResponse(
            CommentResource::collection($comments->items())->response()->getData(true),
            'Comments retrieved successfully.',
            200,
            [
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page'    => $comments->lastPage(),
                    'per_page'     => $comments->perPage(),
                    'total'        => $comments->total(),
                ],
            ]
        );
    }

    /**
     * Store a comment or a reply to an existing comment.
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'content'   => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $user = $request->user();

        $post->loadMissing('group');
        $isPrivate = $post->visibility === 'private'
            || ($post->group && $post->group->visibility === 'private');

        if ($isPrivate) {
            $canComment = (int) $post->user_id === (int) $user->id
                || ($post->group && (int) $post->group->owner_id === (int) $user->id)
                || ($post->group && $post->group->users()->where('users.id', $user->id)->wherePivot('status', 'active')->exists())
                || in_array($user->role?->value ?? (string) $user->role, ['admin', 'super_admin'], true);

            if (!$canComment) {
                return $this->errorResponse('You cannot comment on this private discussion.', 403);
            }
        }

        $parentComment = null;

        if (!empty($validated['parent_id'])) {
            $parentComment = Comment::find($validated['parent_id']);

            if (!$parentComment) {
                return $this->errorResponse('Parent comment not found.', 422);
            }

            if ((int) $parentComment->post_id !== (int) $post->id) {
                return $this->errorResponse('Invalid parent comment.', 422);
            }
        }

        return DB::transaction(function () use ($post, $user, $validated, $parentComment) {
            $comment = $post->comments()->create([
                'user_id'  => $user->id,
                'parent_id' => $validated['parent_id'] ?? null,
                'content'  => trim($validated['content']),
            ]);

            $post->increment('comments_count');

            $comment->load('user.profile');

            /*
            |--------------------------------------------------------------------------
            | Notifications
            |--------------------------------------------------------------------------
            */
            if (!$parentComment) {
                if ((int) $post->user_id !== (int) $user->id) {
                    $post->user->notify(
                        new PostCommentedNotification($user, $post, $comment->content)
                    );
                }
            } else {
                if ($parentComment->user_id !== $user->id) {
                    $parentComment->user->notify(
                        new CommentRepliedNotification(
                            $user,
                            $post,
                            $parentComment->id,
                            $comment->content
                        )
                    );
                }

                if (
                    $post->user_id !== $user->id
                    && (int) $post->user_id !== (int) $parentComment->user_id
                ) {
                    $post->user->notify(
                        new PostCommentedNotification($user, $post, $comment->content)
                    );
                }
            }

            return $this->successResponse(
                new CommentResource($comment),
                'Comment posted successfully.',
                201
            );
        });
    }

    /**
     * Update a comment (author or admin only).
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        if ((int) $comment->user_id !== (int) $user->id
            && !in_array($user->role?->value ?? (string) $user->role, ['admin', 'super_admin'], true)
        ) {
            return $this->errorResponse('Unauthorized.', 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'content' => $validated['content'],
        ]);

        return $this->successResponse(
            new CommentResource($comment),
            'Comment updated successfully.'
        );
    }

    /**
     * Delete a comment (author or admin only).
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        if ((int) $comment->user_id !== (int) $user->id
            && !in_array($user->role?->value ?? (string) $user->role, ['admin', 'super_admin'], true)
        ) {
            return $this->errorResponse('Unauthorized.', 403);
        }

        $post = $comment->post;

        $comment->delete();

        if ($post && $post->comments_count > 0) {
            $post->decrement('comments_count');
        }

        return $this->successResponse(
            ['deleted' => true],
            'Comment deleted successfully.'
        );
    }

    /**
     * Paginated replies for a comment.
     */
    public function replies(Request $request, Comment $comment): JsonResponse
    {
        if ($request->user() === null) {
            return $this->errorResponse('Authentication required.', 401);
        }

        $comment->loadMissing('post.group');

        $post = $comment->post;
        $isPrivate = $post->visibility === 'private'
            || ($post->group && $post->group->visibility === 'private');

        if ($isPrivate) {
            $user = $request->user();
            $canView = (int) $post->user_id === (int) $user->id
                || ($post->group && (int) $post->group->owner_id === (int) $user->id)
                || ($post->group && $post->group->users()->where('users.id', $user->id)->wherePivot('status', 'active')->exists())
                || in_array($user->role?->value ?? (string) $user->role, ['admin', 'super_admin'], true);

            if (!$canView) {
                return $this->errorResponse('This discussion is private.', 403);
            }
        }

        $replies = $comment->replies()
            ->where('status', 'active')
            ->with([
                'user.profile',
                'likes' => fn ($q) => $q->where('user_id', $request->user()->id),
            ])
            ->withCount('likes')
            ->oldest()
            ->paginate(10)
            ->withQueryString();

        return $this->successResponse(
            CommentResource::collection($replies->items())->response()->getData(true),
            'Replies retrieved successfully.',
            200,
            [
                'pagination' => [
                    'current_page' => $replies->currentPage(),
                    'last_page'    => $replies->lastPage(),
                    'per_page'     => $replies->perPage(),
                    'total'        => $replies->total(),
                ],
            ]
        );
    }

    /**
     * Toggle like on a comment.
     */
    public function like(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        $existing = CommentLike::where('user_id', $user->id)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existing) {
            $existing->delete();
            if ($comment->likes_count > 0) {
                $comment->decrement('likes_count');
            }
            $liked = false;
        } else {
            CommentLike::create([
                'user_id'    => $user->id,
                'comment_id' => $comment->id,
            ]);
            $comment->increment('likes_count');
            $liked = true;
        }

        return $this->successResponse([
            'liked'       => $liked,
            'likes_count' => (int) $comment->likes_count,
        ], $liked ? 'Comment liked.' : 'Comment unliked.');
    }
}
