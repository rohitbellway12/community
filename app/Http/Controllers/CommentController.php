<?php namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $comment->load('user.profile');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user_id' => $comment->user_id,
                'created_at_human' => $comment->created_at->diffForHumans(),
                'user' => [
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->profile && $comment->user->profile->avatar
                        ? asset('storage/' . $comment->user->profile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=0c1b33&color=fff'
                ]
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
            'content' => $request->content
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

        $comment->delete();

        return response()->json([
            'success' => true
        ]);
    }
}