<?php namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentRepliedNotification;
use App\Notifications\PostCommentedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        $user = Auth::user();

        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'parent_id' => $request->input('parent_id'),
            'content' => $request->input('content'),
        ]);

        $post->increment('comments_count');

        $comment->load('user.profile');

        // Check if this is a reply or a direct comment
        if (!$request->filled('parent_id')) {
            // Direct comment on post -> notify post author (if not commenting on own post)
            if ($post->user && (int) $post->user_id !== (int) $user->id) {
                $post->loadMissing('group');
                $post->user->notify(
                    new PostCommentedNotification(
                        $user,
                        $post,
                        $comment->content
                    )
                );
            }
        } else {
            // Reply to an existing comment -> notify the comment author with CommentRepliedNotification
            $parentComment = Comment::find($request->input('parent_id'));

            if ($parentComment && $parentComment->user && (int) $parentComment->user_id !== (int) $user->id) {
                $post->loadMissing('group');
                $parentComment->user->notify(
                    new CommentRepliedNotification(
                        $user,
                        $post,
                        $parentComment->id,
                        $comment->content
                    )
                );
            }

            // Also notify post author if post author is a 3rd person (not the commenter and not the parent comment author)
            if (
                $post->user
                && (int) $post->user_id !== (int) $user->id
                && (int) $post->user_id !== (int) ($parentComment?->user_id ?? 0)
            ) {
                $post->loadMissing('group');
                $post->user->notify(
                    new PostCommentedNotification(
                        $user,
                        $post,
                        $comment->content
                    )
                );
            }
        }

        $user = $comment->user;
        $profile = $user?->profile;
        $avatar = $profile && $profile->avatar
            ? (str_starts_with($profile->avatar, 'http') ? $profile->avatar : asset('storage/' . ltrim($profile->avatar, '/')))
            : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff';

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'parent_id' => $comment->parent_id,
                'user_id' => $comment->user_id,
                'created_at_human' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $user?->id,
                    'name' => $user?->name ?? 'User',
                    'avatar' => $avatar,
                ],
                'replies' => [],
            ]
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id && (Auth::user()->role ?? '') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->input('content')
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    public function destroy(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id && (Auth::user()->role ?? '') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $post = $comment->post;
        $comment->delete();

        if ($post && $post->comments_count > 0) {
            $post->decrement('comments_count');
        }

        return response()->json([
            'success' => true
        ]);
    }
}