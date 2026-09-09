<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommunityCommentController extends Controller
{
    /**
     * Display comments.
     */
    public function index(Request $request)
    {
        $query = Comment::with([
            'user',
            'post',
            'replies.user',
            'reports',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $search = $request->input('search');

            $query->where(function ($q) use ($search) {

                $q->where('content', 'like', "%{$search}%")

                    ->orWhereHas('user', function ($uq) use ($search) {

                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");

                    })

                    ->orWhereHas('post', function ($pq) use ($search) {

                        $pq->where('title', 'like', "%{$search}%");

                    });
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->input('status')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Type Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('type')) {

            if ($request->input('type') === 'Comment') {

                $query->whereNull('parent_id');

            } elseif ($request->input('type') === 'Reply') {

                $query->whereNotNull('parent_id');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Paginate
        |--------------------------------------------------------------------------
        */
        $comments = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */
        $totalCommentsCount = Comment::count();

        $todayCommentsCount = Comment::whereDate(
            'created_at',
            today()
        )->count();


        try {

            $reportedCommentsCount = Comment::where(
                'status',
                'reported'
            )->count();

        } catch (\Throwable $e) {

            $reportedCommentsCount = 0;
        }


        try {

            $hiddenCommentsCount = Comment::where(
                'status',
                'hidden'
            )->count();

        } catch (\Throwable $e) {

            $hiddenCommentsCount = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */
        return view('admin.comments', compact(
            'comments',
            'totalCommentsCount',
            'todayCommentsCount',
            'reportedCommentsCount',
            'hiddenCommentsCount'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Update Comment
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);


        $comment->update([
            'content' => $validated['content'],
        ]);


        return redirect()
            ->route('admin.comments')
            ->with(
                'success',
                'Comment updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Hide / Restore Comment
    |--------------------------------------------------------------------------
    */
    public function toggleHide(Comment $comment)
    {
        $currentStatusValue = is_object($comment->status)
            ? ($comment->status->value ?? $comment->status->name)
            : $comment->status;


        $newStatus = $currentStatusValue === 'hidden'
            ? 'active'
            : 'hidden';


        $comment->update([
            'status' => $newStatus,
        ]);


        return redirect()
            ->route('admin.comments')
            ->with(
                'success',
                $newStatus === 'hidden'
                    ? 'Comment hidden successfully.'
                    : 'Comment restored successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Comment
    |--------------------------------------------------------------------------
    */
 public function destroy(Comment $comment)
{
    $comment->delete();

    return back()->with(
        'success',
        'Comment deleted successfully.'
    );
}


    /*
    |--------------------------------------------------------------------------
    | Bulk Action
    |--------------------------------------------------------------------------
    */
   public function bulkAction(Request $request)
{
    $validated = $request->validate([
        'action' => 'required|in:hide,delete',
        'ids' => 'required|array|min:1',
        'ids.*' => 'integer|exists:comments,id',
    ]);

    if ($validated['action'] === 'hide') {

        Comment::whereIn('id', $validated['ids'])
            ->update([
                'status' => 'hidden',
            ]);

        return back()->with(
            'success',
            count($validated['ids']) . ' comments hidden successfully.'
        );
    }

    Comment::whereIn('id', $validated['ids'])
        ->delete();

    return back()->with(
        'success',
        count($validated['ids']) . ' comments deleted successfully.'
    );
}
}