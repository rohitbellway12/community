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

        $topContributors = User::getTopContributors(5);

        return view('community.saved', [
            'user' => $user,
            'profile' => $user->profile,
            'posts' => $posts,
            'topContributors' => $topContributors,
        ]);
    }