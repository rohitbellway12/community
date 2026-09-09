 public function saved(Request $request): View
    {
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