@props([
    'title' => ($post->title ?? 'Post') . ' | REIAC Community',
    'active' => 'home',
    'user' => auth()->user(),
    'sidebar' => true,
    'rightbar' => true,
    'notificationsCount' => 0,
    'trendingTopics' => collect(),
    'topContributors' => collect(),
    'categories' => collect(),
    'tags' => collect(),
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
[x-cloak] { display: none !important; }

.community-post-card {
    text-align: left !important;
}

.community-post-title {
    text-align: left !important;
    margin: 0 !important;
    padding: 0 !important;
}

.community-post-title h1,
.community-post-title h2,
.community-post-title h3,
.community-post-title a,
.community-post-title span {
    text-align: left !important;
    margin: 0 !important;
    padding: 0 !important;
    display: block !important;
}

.community-post-content,
.community-post-content * {
    text-align: left !important;
}
</style>
</head>
<body class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-0 flex flex-col">

    <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div
        x-show="mobileMenuOpen"
        class="fixed inset-0 z-50 lg:hidden flex"
        style="display: none;"
    >
        <div
            @click="mobileMenuOpen = false"
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
        ></div>

        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto"
        >
            @auth
                @php
                    $mobileProfile = $user?->profile;
                    $mobileAvatar = $mobileProfile?->avatar
                        ? asset('storage/' . $mobileProfile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff';
                @endphp

                <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                    <img src="{{ $mobileAvatar }}" alt="{{ $user?->name }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $user?->name }}</div>
                        <a href="{{ route('community.profile', $user?->profile?->username ?? $user?->id) }}" class="text-xs text-amber-600 font-semibold hover:underline">
                            View Profile
                        </a>
                    </div>
                </div>
            @endauth

            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">
                <a href="{{ route('community.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-semibold">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Community Home
                </a>
                <a href="{{ route('community.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    All Discussions
                </a>
            </nav>
        </div>
    </div>

    {{-- MAIN LAYOUT CONTAINER --}}
    <main class="max-w-[1520px] mx-auto w-full px-4 lg:px-6 py-6 flex-1 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        <x-community.sidebar
            :top-contributors="$topContributors"
            :categories="$categories"
            :tags="$tags"
        />

        

        {{-- CENTER FEED COMPONENT --}}
        <section class="space-y-5 min-w-0">
            @php
                $availableCategories = $categories ?? collect();
                $availableTags = $tags ?? collect();

                // Fallback only when the show controller does not pass these collections.
                if ($availableCategories->isEmpty()) {
                    $availableCategories = \App\Models\Category::query()->orderBy('name')->get();
                }

                if ($availableTags->isEmpty()) {
                    $availableTags = \App\Models\Tag::query()->orderBy('name')->get();
                }

                $author = $post->user;
                $profile = $author?->profile;

                $authorAvatar = $profile?->avatar
                    ? asset('storage/' . $profile->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($author?->name ?? 'User') . '&background=0c1b33&color=fff';

                $authorUsername = $profile?->username;
                $authorUrl = $authorUsername
                    ? route('community.profile', $authorUsername)
                    : route('community.profile', $author?->id);

                $isOwnPost = auth()->check() && auth()->id() === $post->user_id;
                $mediaItems = $post->media ?? collect();

                $titleLength = mb_strlen($post->title ?? '');
                $contentLength = mb_strlen(strip_tags($post->content ?? $post->body ?? ''));

                $comments = $post->comments ?? collect();

                $commentPayload = $comments
                    ->whereNull('parent_id')
                    ->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'content' => $comment->content,
                            'parent_id' => $comment->parent_id,
                            'created_at_human' => $comment->created_at->diffForHumans(),
                            'showReplies' => true,
                            'user' => [
                                'id' => $comment->user?->id,
                                'name' => $comment->user?->name ?? 'User',
                                'avatar' => $comment->user?->profile?->avatar
                                    ? asset('storage/' . $comment->user->profile->avatar)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user?->name ?? 'User') . '&background=0c1b33&color=fff',
                            ],
                            'replies' => $comment->replies->map(function ($reply) {
                                return [
                                    'id' => $reply->id,
                                    'content' => $reply->content,
                                    'parent_id' => $reply->parent_id,
                                    'created_at_human' => $reply->created_at->diffForHumans(),
                                    'user' => [
                                        'id' => $reply->user?->id,
                                        'name' => $reply->user?->name ?? 'User',
                                        'avatar' => $reply->user?->profile?->avatar
                                            ? asset('storage/' . $reply->user->profile->avatar)
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user?->name ?? 'User') . '&background=0c1b33&color=fff',
                                    ],
                                ];
                            })->values(),
                        ];
                    })->values();
            @endphp

            <div
                class="community-post-card bg-white p-5 lg:p-6 rounded-2xl shadow-sm border border-slate-200/70 space-y-4 min-w-0 text-left"
                x-data="{
                    currentUserId: {{ auth()->check() ? auth()->id() : 'null' }},
                    postOwnerId: {{ (int) $post->user_id }},

                    postMenuOpen: false,
                    postEditOpen: false,
                    postDeleteConfirm: false,
                    postActionLoading: false,

                    postTitle: @js($post->title),
                    postContent: @js($post->content ?? $post->body ?? ''),
                    postCategoryId: @js((string) $post->category_id),

                    selectedTags: @js($post->tags->pluck('id')->map(fn($id) => (int) $id)->values()),
                    availableTags: @js($availableTags->map(fn($tag) => ['id' => (int) $tag->id, 'name' => $tag->name])->values()),

                    likesCount: {{ $post->likes_count ?? 0 }},
                    liked: {{ auth()->check() && $post->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
                    saved: {{ auth()->check() && $post->savedBy()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
                    copied: false,

                    expandedTitle: false,
                    expandedContent: false,
                    expandedTags: false,

                    mediaIndex: 0,
                    newMediaFiles: [],
                    newMediaPreviews: [],
                    newMediaIndex: 0,

                    commentsCount: {{ $post->comments_count ?? $commentPayload->count() }},
                    comments: {{ Js::from($commentPayload) }},
                    hasMoreComments: {{ $post->comments()->whereNull('parent_id')->count() > $commentPayload->count() ? 'true' : 'false' }},
                    loadingMore: false,
                    newCommentText: '',

                    replyingTo: null,
                    replyText: '',

                    editingCommentId: null,
                    editingCommentText: '',
                    openCommentMenuId: null,

                    deleteConfirmComment: null,
                    deleteConfirmIsReply: false,
                    deleteConfirmParentComment: null,

                    commentActionLoading: false,
                    toast: '',
                    toastType: 'success',
                    toastTimer: null,

                    isOwner() {
                        return this.currentUserId !== null &&
                            Number(this.currentUserId) === Number(this.postOwnerId);
                    },

                    toggleTag(id) {
                        id = Number(id);

                        if (this.selectedTags.includes(id)) {
                            this.selectedTags = this.selectedTags.filter(value => Number(value) !== id);
                            return;
                        }

                        if (this.selectedTags.length < 5) {
                            this.selectedTags.push(id);
                        }
                    },

                    openEditPost() {
                        if (!this.isOwner()) return;

                        this.postMenuOpen = false;
                        this.postEditOpen = true;
                        this.postTitle = @js($post->title);
                        this.postContent = @js($post->content ?? $post->body ?? '');
                        this.postCategoryId = @js((string) $post->category_id);
                        this.selectedTags = @js($post->tags->pluck('id')->map(fn($id) => (int) $id)->values());
                        this.newMediaFiles = [];
                        this.newMediaPreviews = [];
                        this.newMediaIndex = 0;

                        this.$nextTick(() => {
                            if (this.$refs.editMediaInput) {
                                this.$refs.editMediaInput.value = '';
                            }
                        });
                    },

                    closeEditPost() {
                        this.postEditOpen = false;
                        this.postMenuOpen = false;
                        this.newMediaFiles = [];
                        this.newMediaPreviews = [];
                        this.newMediaIndex = 0;

                        this.$nextTick(() => {
                            if (this.$refs.editMediaInput) {
                                this.$refs.editMediaInput.value = '';
                            }
                        });
                    },

                    handleEditFiles(event) {
                        const incoming = Array.from(event.target.files || []);

                        if (!incoming.length) return;

                        const remaining = Math.max(0, 10 - this.newMediaFiles.length);
                        const files = incoming.slice(0, remaining);

                        this.newMediaFiles = this.newMediaFiles.concat(files);
                        this.newMediaPreviews.forEach(preview => {
                            if (preview.url?.startsWith('blob:')) URL.revokeObjectURL(preview.url);
                        });

                        this.newMediaPreviews = this.newMediaFiles.map(file => ({
                            url: URL.createObjectURL(file),
                            type: file.type.startsWith('video/') ? 'video' : 'image',
                            name: file.name
                        }));

                        this.newMediaIndex = Math.max(0, this.newMediaPreviews.length - 1);
                        this.syncMediaInput();
                    },

                    syncMediaInput() {
                        const input = this.$refs.editMediaInput;

                        if (!input || typeof DataTransfer === 'undefined') return;

                        const transfer = new DataTransfer();

                        this.newMediaFiles.forEach(file => transfer.items.add(file));
                        input.files = transfer.files;
                    },

                    removeNewMedia(index) {
                        const preview = this.newMediaPreviews[index];

                        if (preview?.url?.startsWith('blob:')) {
                            URL.revokeObjectURL(preview.url);
                        }

                        this.newMediaFiles.splice(index, 1);

                        this.newMediaPreviews = this.newMediaFiles.map(file => ({
                            url: URL.createObjectURL(file),
                            type: file.type.startsWith('video/') ? 'video' : 'image',
                            name: file.name
                        }));

                        this.newMediaIndex = Math.min(
                            this.newMediaIndex,
                            Math.max(0, this.newMediaPreviews.length - 1)
                        );

                        this.syncMediaInput();
                    },

                    previousMedia() {
                        const total = {{ $mediaItems->count() }};
                        if (!total) return;
                        this.mediaIndex = (this.mediaIndex - 1 + total) % total;
                    },

                    nextMedia() {
                        const total = {{ $mediaItems->count() }};
                        if (!total) return;
                        this.mediaIndex = (this.mediaIndex + 1) % total;
                    },

                    previousNewMedia() {
                        const total = this.newMediaPreviews.length;
                        if (!total) return;
                        this.newMediaIndex = (this.newMediaIndex - 1 + total) % total;
                    },

                    nextNewMedia() {
                        const total = this.newMediaPreviews.length;
                        if (!total) return;
                        this.newMediaIndex = (this.newMediaIndex + 1) % total;
                    },

                    toggleLike() {
                        @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                        @endguest

                        fetch('{{ route('community.posts.like', $post) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(async response => {
                            if (response.status === 401) {
                                window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return null;
                            }

                            if (!response.ok) throw new Error('Like request failed.');
                            return response.json();
                        })
                        .then(data => {
                            if (data?.success) {
                                this.liked = Boolean(data.liked);
                                this.likesCount = Number(data.likes_count);
                            }
                        })
                        .catch(error => console.error(error));
                    },

                    toggleSave() {
                        @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                        @endguest

                        fetch('{{ route('community.posts.save', $post) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(async response => {
                            if (response.status === 401) {
                                window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return null;
                            }

                            if (!response.ok) throw new Error('Save request failed.');
                            return response.json();
                        })
                        .then(data => {
                            if (data?.success) this.saved = Boolean(data.saved);
                        })
                        .catch(error => console.error(error));
                    },

                    async sharePost() {
                        const url = @js(route('community.posts.show', $post));

                        try {
                            if (navigator.share) {
                                await navigator.share({
                                    title: @js($post->title),
                                    text: @js(\Illuminate\Support\Str::limit(strip_tags($post->content ?? $post->body ?? ''), 180)),
                                    url
                                });
                                return;
                            }

                            await navigator.clipboard.writeText(url);
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        } catch (error) {
                            if (error?.name !== 'AbortError') {
                                console.error(error);
                            }
                        }
                    },

                    showToast(message, type = 'success') {
                        this.toast = message;
                        this.toastType = type;

                        clearTimeout(this.toastTimer);
                        this.toastTimer = setTimeout(() => {
                            this.toast = '';
                        }, 2500);
                    },

                    async submitComment() {
                        @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                        @endguest

                        const content = this.newCommentText.trim();
                        if (!content) return;

                        this.commentActionLoading = true;

                        try {
                            const response = await fetch('{{ route('community.posts.comment.store', $post) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({ content })
                            });

                            if (response.status === 401) {
                                window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return;
                            }

                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Unable to add comment.');
                            }

                            const comment = data.comment;
                            comment.user = comment.user || {};
                            comment.user.id = comment.user.id ?? this.currentUserId;
                            comment.user.name = comment.user.name ?? 'You';
                            comment.user.avatar = comment.user.avatar ||
                                'https://ui-avatars.com/api/?name=' + encodeURIComponent(comment.user.name) + '&background=0c1b33&color=fff';
                            comment.replies = comment.replies || [];
                            comment.created_at_human = comment.created_at_human || 'Just now';

                            this.comments.unshift(comment);
                            this.commentsCount = Number(this.commentsCount) + 1;
                            this.newCommentText = '';

                            this.showToast('Comment added successfully.');
                        } catch (error) {
                            console.error(error);
                            this.showToast(error.message || 'Unable to add comment.', 'error');
                        } finally {
                            this.commentActionLoading = false;
                        }
                    },

                    startReply(comment) {
                        @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                        @endguest

                        this.replyingTo = {
                            id: Number(comment.id),
                            userName: comment.user?.name || 'User'
                        };

                        this.replyText = '';

                        this.$nextTick(() => this.$refs.replyInput?.focus());
                    },

                    cancelReply() {
                        this.replyingTo = null;
                        this.replyText = '';
                    },

                    async submitReply() {
                        @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                        @endguest

                        if (!this.replyingTo || !this.replyText.trim()) return;

                        const parentId = this.replyingTo.id;
                        const content = this.replyText.trim();

                        this.commentActionLoading = true;

                        try {
                            const response = await fetch('{{ route('community.posts.comment.store', $post) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    content,
                                    parent_id: parentId
                                })
                            });

                            if (response.status === 401) {
                                window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return;
                            }

                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Unable to add reply.');
                            }

                            const parent = this.comments.find(
                                item => Number(item.id) === Number(parentId)
                            );

                            if (parent) {
                                parent.replies = parent.replies || [];
                                parent.replies.push(data.comment);
                                parent.showReplies = true;
                            }

                            this.commentsCount = Number(this.commentsCount) + 1;
                            this.cancelReply();

                            this.showToast('Reply added successfully.');
                        } catch (error) {
                            console.error(error);
                            this.showToast(error.message || 'Unable to add reply.', 'error');
                        } finally {
                            this.commentActionLoading = false;
                        }
                    },

                    isCommentOwner(comment) {
                        return this.currentUserId !== null &&
                            Number(comment?.user?.id) === Number(this.currentUserId);
                    },

                    startEditComment(comment) {
                        if (!this.isCommentOwner(comment)) return;

                        this.openCommentMenuId = null;
                        this.editingCommentId = Number(comment.id);
                        this.editingCommentText = comment.content || '';
                    },

                    cancelEditComment() {
                        this.editingCommentId = null;
                        this.editingCommentText = '';
                    },

                    async updateComment(comment) {
                        if (!this.isCommentOwner(comment) || !this.editingCommentText.trim()) return;

                        this.commentActionLoading = true;

                        try {
                            const response = await fetch(
                                '{{ route('community.comments.update', ['comment' => '__COMMENT_ID__']) }}'
                                    .replace('__COMMENT_ID__', comment.id),
                                {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        content: this.editingCommentText.trim()
                                    })
                                }
                            );

                            if (response.status === 401) {
                                window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return;
                            }

                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Unable to update comment.');
                            }

                            comment.content = data.comment?.content ?? this.editingCommentText.trim();

                            this.cancelEditComment();
                            this.showToast('Comment updated successfully.');
                        } catch (error) {
                            console.error(error);
                            this.showToast(error.message || 'Unable to update comment.', 'error');
                        } finally {
                            this.commentActionLoading = false;
                        }
                    },

                    requestDeleteComment(comment, isReply = false, parent = null) {
                        if (!this.isCommentOwner(comment) || this.commentActionLoading) return;

                        this.openCommentMenuId = null;
                        this.deleteConfirmComment = comment;
                        this.deleteConfirmIsReply = isReply;
                        this.deleteConfirmParentComment = parent;
                    },

                    cancelDeleteComment(force = false) {
                        if (this.commentActionLoading && !force) return;

                        this.deleteConfirmComment = null;
                        this.deleteConfirmIsReply = false;
                        this.deleteConfirmParentComment = null;
                    },

                    async confirmDeleteComment() {
                        const comment = this.deleteConfirmComment;

                        if (!comment || !this.isCommentOwner(comment)) {
                            this.cancelDeleteComment();
                            return;
                        }

                        this.commentActionLoading = true;

                        try {
                            const response = await fetch(
                                '{{ route('community.comments.destroy', ['comment' => '__COMMENT_ID__']) }}'
                                    .replace('__COMMENT_ID__', comment.id),
                                {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                }
                            );

                            if (response.status === 401) {
                                window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return;
                            }

                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Unable to delete comment.');
                            }

                            if (this.deleteConfirmIsReply && this.deleteConfirmParentComment) {
                                this.deleteConfirmParentComment.replies =
                                    (this.deleteConfirmParentComment.replies || [])
                                        .filter(reply => Number(reply.id) !== Number(comment.id));
                            } else {
                                this.comments = this.comments.filter(
                                    item => Number(item.id) !== Number(comment.id)
                                );
                            }

                            this.commentsCount = Math.max(0, Number(this.commentsCount) - 1);
                            this.cancelDeleteComment(true);

                            this.showToast(
                                this.deleteConfirmIsReply ? 'Reply deleted successfully.' : 'Comment deleted successfully.'
                            );
                        } catch (error) {
                            console.error(error);
                            this.showToast(error.message || 'Unable to delete comment.', 'error');
                        } finally {
                            this.commentActionLoading = false;
                        }
                    },

                    loadMoreComments() {
                        if (this.loadingMore || !this.hasMoreComments) return;

                        this.loadingMore = true;

                        fetch(`{{ route('community.posts.comments', $post) }}?skip=${this.comments.length}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(async response => {
                            if (!response.ok) throw new Error('Unable to load comments.');
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && Array.isArray(data.comments)) {
                                const newComments = data.comments.map(c => ({
                                    ...c,
                                    showReplies: true
                                }));
                                this.comments = this.comments.concat(newComments);
                                this.hasMoreComments = Boolean(data.has_more);
                            } else {
                                this.hasMoreComments = false;
                            }
                        })
                        .catch(error => this.showToast(error.message, 'error'))
                        .finally(() => {
                            this.loadingMore = false;
                        });
                    }
                }"
            >

                {{-- HEADER --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <img src="{{ $authorAvatar }}"
                            alt="{{ $author?->name ?? 'User' }}"
                            class="w-10 h-10 rounded-full object-cover shrink-0">

                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-sm truncate flex items-center gap-1.5">
                                <a href="{{ $authorUrl }}" class="hover:underline">
                                    {{ $author?->name ?? 'User' }}
                                </a>
                                @if ($profile?->country)
                                    @php
                                        $isoCode = strtolower(trim($profile->country->iso_code ?: ''));
                                        if (!$isoCode && !empty($profile->country->code) && strlen(trim($profile->country->code)) === 2) {
                                            $isoCode = strtolower(trim($profile->country->code));
                                        }
                                    @endphp
                                    @if ($isoCode)
                                        <img
                                            src="https://flagcdn.com/20x15/{{ $isoCode }}.png"
                                            srcset="https://flagcdn.com/40x30/{{ $isoCode }}.png 2x"
                                            width="20"
                                            height="15"
                                            alt="{{ $profile->country->name }}"
                                            title="{{ $profile->country->name }}"
                                            class="w-4.5 h-3.5 object-cover rounded-xs shadow-2xs inline-block shrink-0 align-middle"
                                            loading="lazy"
                                            onerror="this.style.display='none'"
                                        >
                                    @elseif (!empty($profile->country->flag) && (str_starts_with($profile->country->flag, 'http://') || str_starts_with($profile->country->flag, 'https://')))
                                        <img
                                            src="{{ $profile->country->flag }}"
                                            alt="{{ $profile->country->name }}"
                                            title="{{ $profile->country->name }}"
                                            class="w-4.5 h-3.5 object-cover rounded-xs shadow-2xs inline-block shrink-0 align-middle"
                                            loading="lazy"
                                            onerror="this.style.display='none'"
                                        >
                                    @endif
                                @endif
                            </div>

                            <div class="mt-0.5 flex items-center gap-1.5 text-[11px] text-slate-400">
                                @if($authorUsername)
                                    <span>{{ '@' . $authorUsername }}</span>
                                    <span>•</span>
                                @endif
                                <span>{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($post->category)
                            <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md">
                                {{ strtoupper($post->category->name) }}
                            </span>
                        @endif

                        @if($post->is_solved)
                            <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded-md">
                                ✓ Solved
                            </span>
                        @endif

                        @auth
                            <div class="relative" @click.outside="postMenuOpen = false">
                                {{-- POST ACTIONS / 3-DOT MENU --}}
                                <button type="button"
                                    aria-label="Post options"
                                    title="Post options"
                                    @click.stop="postMenuOpen = !postMenuOpen"
                                    class="w-9 h-9 rounded-full flex items-center justify-center
                                           bg-white border border-slate-200 text-slate-500
                                           hover:bg-slate-100 hover:text-slate-900
                                           shadow-sm transition">
                                    <span class="flex flex-col items-center justify-center gap-[3px]">
                                        <span class="block w-1 h-1 rounded-full bg-current"></span>
                                        <span class="block w-1 h-1 rounded-full bg-current"></span>
                                        <span class="block w-1 h-1 rounded-full bg-current"></span>
                                    </span>
                                </button>

                                <div x-show="postMenuOpen" x-cloak x-transition
                                    class="absolute right-0 top-11 z-[100] w-40 rounded-xl
                                           border border-slate-200 bg-white p-1.5 shadow-2xl">
                                    @if($isOwnPost)
                                        <button type="button"
                                            @click="openEditPost()"
                                            class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold
                                                   text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            Edit post
                                        </button>

                                        <button type="button"
                                            @click="postMenuOpen = false; postDeleteConfirm = true"
                                            class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold
                                                   text-red-600 hover:bg-red-50 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 6h18M8 6V4h8v2m-9 0l1 14h8l1-14M10 11v5m4-5v5"/>
                                            </svg>
                                            Delete post
                                        </button>
                                    @else
                                        <div class="px-3 py-2.5 text-[10px] text-slate-400">
                                            You can only edit your own post.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                {{-- TITLE & CONTENT --}}
                <div class="space-y-1.5 text-left">
                    <div class="community-post-title text-base sm:text-lg font-bold text-slate-900 leading-snug break-words text-left">
                        <h1 :class="expandedTitle ? '' : 'line-clamp-2'" class="m-0 p-0 text-left break-words block font-bold text-slate-900 text-base sm:text-lg leading-snug">
                            <span class="text-left block" x-text="postTitle"></span>
                        </h1>

                        @if($titleLength > 140)
                            <button type="button"
                                @click="expandedTitle = !expandedTitle"
                                class="mt-1 text-[11px] font-bold text-amber-600 hover:underline text-left inline-block">
                                <span x-text="expandedTitle ? 'Show less' : 'Show more'"></span>
                            </button>
                        @endif
                    </div>

                    <div class="community-post-content text-xs sm:text-sm text-slate-600 leading-relaxed break-words text-left">
                        <p class="whitespace-pre-wrap break-words text-left"
                            :class="expandedContent ? '' : 'line-clamp-5'"
                            x-text="postContent"></p>

                        @if($contentLength > 280)
                            <div class="mt-1.5 flex justify-start text-left">
                                <button type="button"
                                    @click="expandedContent = !expandedContent"
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 hover:text-amber-700 hover:underline text-left">
                                    <span x-text="expandedContent ? 'Show less' : 'Show more'"></span>
                                    <svg class="w-3 h-3 transition-transform" :class="expandedContent ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- MEDIA --}}
                @if($mediaItems->isNotEmpty())
                    <div class="relative w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-200">
                        <div class="w-full h-[280px] sm:h-[360px] lg:h-[420px] flex items-center justify-center overflow-hidden">
                            @foreach($mediaItems as $index => $mediaItem)
                                <div x-show="mediaIndex === {{ $index }}" x-cloak
                                    class="w-full h-full flex items-center justify-center overflow-hidden">
                                    @if(($mediaItem->type ?? '') === 'video' || str_starts_with($mediaItem->mime_type ?? '', 'video'))
                                        <video src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                            class="w-full h-full object-contain"
                                            controls preload="metadata"></video>
                                    @else
                                        <img src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                            alt="{{ $post->title }}"
                                            class="w-full h-full object-contain">
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($mediaItems->count() > 1)
                            <button type="button" @click="previousMedia()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow flex items-center justify-center text-xl">
                                ‹
                            </button>

                            <button type="button" @click="nextMedia()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow flex items-center justify-center text-xl">
                                ›
                            </button>

                            <span class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white rounded-full px-3 py-1 text-[10px] font-bold"
                                x-text="(mediaIndex + 1) + ' / {{ $mediaItems->count() }}'"></span>
                        @endif
                    </div>
                @endif

                {{-- TAGS --}}
                @if($post->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($post->tags as $index => $tag)
                            <a href="{{ route('community.index', ['tag' => $tag->slug]) }}"
                                x-show="expandedTags || {{ $index }} < 5"
                                class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium px-2.5 py-1 rounded-lg">
                                #{{ $tag->name }}
                            </a>
                        @endforeach

                        @if($post->tags->count() > 5)
                            <button type="button"
                                @click="expandedTags = !expandedTags"
                                class="text-[11px] font-bold text-amber-600 hover:underline">
                                <span x-text="expandedTags ? 'Show less' : '+{{ $post->tags->count() - 5 }} more'"></span>
                            </button>
                        @endif
                    </div>
                @endif

                {{-- ACTION BAR --}}
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-5">
                        <button type="button"
                            @click="toggleLike()"
                            :class="liked ? 'text-red-500' : 'hover:text-red-500'"
                            class="flex items-center gap-1.5">
                            ♥ <span x-text="likesCount"></span>
                        </button>

                        <button type="button"
                            @click="showAllComments = true"
                            class="flex items-center gap-1.5 hover:text-blue-500">
                            💬 <span x-text="commentsCount"></span>
                        </button>

                        <button type="button"
                            @click="sharePost()"
                            class="flex items-center gap-1.5"
                            :class="copied ? 'text-emerald-600' : 'hover:text-emerald-500'">
                            <span x-text="copied ? 'Copied!' : 'Share'"></span>
                        </button>
                    </div>

                    {{-- <button type="button"
                        @click="toggleSave()"
                        :class="saved ? 'text-amber-500' : 'hover:text-amber-500'"
                        class="flex items-center gap-1.5">
                        <span x-text="saved ? 'Saved' : 'Save'"></span>
                    </button> --}}
                </div>

                {{-- COMMENTS --}}
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Discussion</h2>
                            <p class="text-[10px] text-slate-400"
                                x-text="commentsCount + (commentsCount === 1 ? ' comment' : ' comments')"></p>
                        </div>
                    </div>

                    <div class="flex items-end gap-2">
                        <input type="text"
                            x-model="newCommentText"
                            @keydown.enter.prevent="submitComment()"
                            placeholder="Write a comment..."
                            maxlength="1000"
                            class="flex-1 h-10 bg-slate-50 border border-slate-200 rounded-xl px-3 text-xs focus:outline-none focus:ring-2 focus:ring-amber-200">

                        <button type="button"
                            @click="submitComment()"
                            :disabled="commentActionLoading || !newCommentText.trim()"
                            class="h-10 px-4 rounded-xl bg-[#0b1329] text-white text-xs font-bold disabled:opacity-40">
                            Post
                        </button>
                    </div>

                    <div x-show="toast" x-cloak
                        class="rounded-xl border px-3 py-2 text-[10px] font-semibold"
                        :class="toastType === 'error'
                            ? 'bg-red-50 border-red-100 text-red-600'
                            : 'bg-emerald-50 border-emerald-100 text-emerald-700'">
                        <span x-text="toast"></span>
                    </div>

                    <div class="space-y-3">
                        <template x-for="comment in comments" :key="comment.id">
                            <div>
                                <div class="flex items-start gap-2.5">
                                    <img :src="comment.user.avatar"
                                        class="w-8 h-8 rounded-full object-cover shrink-0">

                                    <div class="flex-1 min-w-0">
                                        <div class="relative rounded-2xl rounded-tl-md bg-slate-50 border border-slate-100 p-3">

                                            <div class="flex items-center gap-2 pr-7">
                                                <span class="text-[11px] font-bold text-slate-900"
                                                    x-text="comment.user.name"></span>
                                                <span class="text-[9px] text-slate-400"
                                                    x-text="comment.created_at_human"></span>
                                            </div>

                                            <template x-if="editingCommentId === Number(comment.id)">
                                                <div class="mt-2">
                                                    <textarea x-model="editingCommentText"
                                                        rows="3"
                                                        maxlength="1000"
                                                        class="w-full bg-white border border-amber-200 rounded-xl p-2 text-xs resize-none focus:outline-none"></textarea>

                                                    <div class="flex gap-2 mt-2">
                                                        <button type="button"
                                                            @click="updateComment(comment)"
                                                            class="px-3 py-1.5 rounded-lg bg-[#0b1329] text-white text-[9px] font-bold">
                                                            Save
                                                        </button>

                                                        <button type="button"
                                                            @click="cancelEditComment()"
                                                            class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-[9px] font-bold">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="editingCommentId !== Number(comment.id)">
                                                <p class="mt-1 text-[11px] leading-relaxed text-slate-700 whitespace-pre-wrap break-words"
                                                    x-text="comment.content"></p>
                                            </template>

                                            <template x-if="isCommentOwner(comment) && editingCommentId !== Number(comment.id)">
                                                <div class="absolute right-1 top-1">
                                                    <button type="button"
                                                        @click.stop="openCommentMenuId = openCommentMenuId === Number(comment.id) ? null : Number(comment.id)"
                                                        class="w-7 h-7 rounded-full text-slate-400 hover:bg-white">
                                                        •••
                                                    </button>

                                                    <div x-show="openCommentMenuId === Number(comment.id)"
                                                        x-cloak
                                                        @click.outside="openCommentMenuId = null"
                                                        class="absolute right-0 top-8 z-50 w-28 rounded-xl border border-slate-200 bg-white p-1 shadow-xl">

                                                        <button type="button"
                                                            @click="startEditComment(comment)"
                                                            class="w-full text-left px-2.5 py-2 rounded-lg text-[10px] font-semibold hover:bg-slate-50">
                                                            Edit
                                                        </button>

                                                        <button type="button"
                                                            @click="requestDeleteComment(comment)"
                                                            class="w-full text-left px-2.5 py-2 rounded-lg text-[10px] font-semibold text-red-600 hover:bg-red-50">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="flex items-center gap-2.5 px-1 pt-1">
                                            <button type="button"
                                                @click="startReply(comment)"
                                                class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-400 hover:text-amber-600 transition">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a4 4 0 014 4v1m0 0l4-4m-4 4l-4-4" />
                                                </svg>
                                                <span>Reply</span>
                                            </button>

                                            <template x-if="comment.replies?.length">
                                                <button type="button"
                                                    @click="comment.showReplies = !comment.showReplies"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 hover:bg-amber-100/80 text-[8.5px] font-bold text-amber-700 transition">
                                                    <svg class="w-2.5 h-2.5 transition-transform duration-200"
                                                        :class="comment.showReplies ? 'rotate-180' : ''"
                                                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                    <span x-text="comment.showReplies ? 'Hide replies' : (comment.replies.length + (comment.replies.length === 1 ? ' reply' : ' replies'))"></span>
                                                </button>
                                            </template>
                                        </div>

                                        <template x-if="comment.replies?.length">
                                            <div x-show="comment.showReplies !== false" x-transition
                                                class="ml-8 mt-2 pl-3 border-l-2 border-amber-200 space-y-2">

                                                <template x-for="reply in comment.replies" :key="reply.id">
                                                    <div class="flex items-start gap-2">
                                                        <img :src="reply.user.avatar"
                                                            class="w-6 h-6 rounded-full object-cover shrink-0 ring-1 ring-slate-200">

                                                        <div class="flex-1 min-w-0">
                                                            <div class="relative rounded-2xl rounded-tl-md bg-slate-50 border border-slate-100 px-2.5 py-2">

                                                                <div class="flex items-center gap-2 pr-6">
                                                                    <span class="text-[10px] font-bold text-slate-800"
                                                                        x-text="reply.user.name"></span>
                                                                    <span
                                                                        x-show="Number(reply.user.id) === Number(currentUserId)"
                                                                        class="shrink-0 rounded-full bg-amber-100 px-1.5 py-0.5 text-[7px] font-bold text-amber-700">You</span>
                                                                    <span class="text-[8px] text-slate-400 shrink-0"
                                                                        x-text="reply.created_at_human"></span>
                                                                </div>

                                                                <template x-if="editingCommentId === Number(reply.id)">
                                                                    <div class="mt-1.5">
                                                                        <textarea x-model="editingCommentText"
                                                                            rows="2"
                                                                            maxlength="1000"
                                                                            class="w-full bg-white border border-amber-200 rounded-xl px-2.5 py-2 text-[10px] focus:outline-none focus:ring-2 focus:ring-amber-100 resize-none"></textarea>

                                                                        <div class="flex gap-2 mt-1.5">
                                                                            <button type="button"
                                                                                @click="updateComment(reply)"
                                                                                class="px-2.5 py-1.5 rounded-lg bg-[#0b1329] text-white text-[8px] font-bold">
                                                                                Save
                                                                            </button>

                                                                            <button type="button"
                                                                                @click="cancelEditComment()"
                                                                                class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-[8px] font-bold">
                                                                                Cancel
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </template>

                                                                <template x-if="editingCommentId !== Number(reply.id)">
                                                                    <p class="mt-0.5 text-[10px] leading-relaxed text-slate-600 whitespace-pre-wrap break-words"
                                                                        x-text="reply.content"></p>
                                                                </template>

                                                                <template x-if="isCommentOwner(reply) && editingCommentId !== Number(reply.id)">
                                                                    <div class="absolute right-1 top-1">
                                                                        <button type="button"
                                                                            @click.stop="openCommentMenuId = openCommentMenuId === Number(reply.id) ? null : Number(reply.id)"
                                                                            class="w-5.5 h-5.5 rounded-full flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-700 transition">
                                                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                                                                <circle cx="5" cy="12" r="1.6" />
                                                                                <circle cx="12" cy="12" r="1.6" />
                                                                                <circle cx="19" cy="12" r="1.6" />
                                                                            </svg>
                                                                        </button>

                                                                        <div x-show="openCommentMenuId === Number(reply.id)"
                                                                            x-cloak
                                                                            @click.outside="openCommentMenuId = null"
                                                                            class="absolute right-0 top-7 z-50 w-28 rounded-xl border border-slate-200 bg-white p-1 shadow-xl">

                                                                            <button type="button"
                                                                                @click="startEditComment(reply)"
                                                                                class="w-full text-left px-2.5 py-2 rounded-lg text-[10px] font-semibold text-slate-600 hover:bg-slate-50">
                                                                                Edit
                                                                            </button>

                                                                            <button type="button"
                                                                                @click="requestDeleteComment(reply, true, comment)"
                                                                                class="w-full text-left px-2.5 py-2 rounded-lg text-[10px] font-semibold text-red-600 hover:bg-red-50">
                                                                                Delete
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="comments.length === 0"
                            class="py-5 text-center text-xs text-slate-400">
                            No comments yet. Be the first to reply!
                        </div>
                    </div>

                    <div x-show="replyingTo" x-cloak
                        class="ml-7 flex gap-2">
                        <input x-ref="replyInput"
                            x-model="replyText"
                            @keydown.enter.prevent="submitReply()"
                            maxlength="1000"
                            :placeholder="'Reply to ' + (replyingTo?.userName || 'User') + '...'"
                            class="flex-1 h-9 bg-slate-50 border border-slate-200 rounded-xl px-3 text-[10px] focus:outline-none">

                        <button type="button"
                            @click="submitReply()"
                            :disabled="commentActionLoading || !replyText.trim()"
                            class="h-9 px-3 rounded-xl bg-[#0b1329] text-white text-[10px] font-bold disabled:opacity-40">
                            Reply
                        </button>

                        <button type="button"
                            @click="cancelReply()"
                            class="h-9 px-2 text-[10px] font-bold text-slate-400">
                            Cancel
                        </button>
                    </div>

                    <div x-show="hasMoreComments" class="text-center pt-2">
                        <button type="button"
                            @click="loadMoreComments()"
                            :disabled="loadingMore"
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-200 text-[10px] font-bold text-slate-600 hover:text-amber-700 transition disabled:opacity-50 shadow-xs">
                            <svg x-show="!loadingMore" class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg x-show="loadingMore" class="w-3 h-3 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loadingMore ? 'Loading comments...' : 'See more comments'"></span>
                        </button>
                    </div>
                </div>

                {{-- EDIT POST MODAL --}}
                <template x-teleport="body">
                    <div x-show="postEditOpen" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6">

                        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                            @click="closeEditPost()"></div>

                        <div class="relative w-full max-w-[700px] max-h-[calc(100vh-32px)] overflow-hidden rounded-2xl bg-white shadow-2xl"
                            @click.stop>

                            <form method="POST"
                                action="{{ route('community.posts.update', ['post' => $post]) }}"
                                enctype="multipart/form-data"
                                @submit="postActionLoading = true">

                                @csrf
                                @method('PUT')

                                <div class="h-16 px-5 sm:px-6 flex items-center justify-between border-b border-slate-200">
                                    <h2 class="text-[17px] font-bold text-slate-900">
                                        Edit a discussion
                                    </h2>

                                    <button type="button"
                                        @click="closeEditPost()"
                                        class="w-9 h-9 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 text-xl">
                                        ×
                                    </button>
                                </div>

                                <div class="overflow-y-auto max-h-[calc(100vh-150px)] px-5 sm:px-6 py-5">

                                    <div class="flex items-center gap-3 mb-5">
                                        <img src="{{ $authorAvatar }}"
                                            class="w-11 h-11 rounded-full object-cover">

                                        <div>
                                            <div class="text-sm font-bold text-slate-900">
                                                {{ $author?->name ?? 'User' }}
                                            </div>

                                            <div class="mt-1 flex gap-2">
                                                <select name="category_id"
                                                    x-model="postCategoryId"
                                                    required
                                                    class="h-8 rounded-full bg-slate-100 border-0 px-3 text-[11px] font-semibold text-slate-700 focus:outline-none">
                                                    @foreach($availableCategories as $category)
                                                        <option value="{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <span class="h-8 px-3 rounded-full bg-slate-100 inline-flex items-center text-[11px] font-semibold text-slate-500">
                                                    Public
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="text"
                                        name="title"
                                        x-model="postTitle"
                                        maxlength="255"
                                        required
                                        class="w-full border-0 p-0 text-lg font-bold text-slate-900 focus:outline-none"
                                        placeholder="Give your discussion a clear title...">

                                    <textarea name="content"
                                        x-model="postContent"
                                        maxlength="10000"
                                        required
                                        rows="7"
                                        class="mt-2 w-full border-0 p-0 text-sm leading-6 text-slate-700 resize-none focus:outline-none whitespace-pre-wrap"
                                        placeholder="What do you want to talk about?"></textarea>

                                    {{-- ALL TAGS --}}
                                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <div class="text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                                    Tags
                                                </div>
                                                <div class="text-[10px] text-slate-400 mt-1">
                                                    Select or unselect tags · Maximum 5
                                                </div>
                                            </div>

                                            <span class="text-[10px] font-bold text-slate-400"
                                                x-text="selectedTags.length + '/5'"></span>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="tag in availableTags" :key="tag.id">
                                                <button type="button"
                                                    @click="toggleTag(tag.id)"
                                                    :class="selectedTags.includes(Number(tag.id))
                                                        ? 'bg-[#0b1329] text-white border-[#0b1329]'
                                                        : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'"
                                                    class="h-8 px-3 rounded-full border text-[10px] font-semibold transition">
                                                    <span x-text="selectedTags.includes(Number(tag.id)) ? '✓ ' : '+ '"></span>
                                                    <span x-text="tag.name"></span>
                                                </button>
                                            </template>
                                        </div>

                                        <template x-for="tag in availableTags" :key="'tag-input-' + tag.id">
                                            <input type="checkbox"
                                                name="tags[]"
                                                :value="tag.id"
                                                :checked="selectedTags.includes(Number(tag.id))"
                                                class="hidden">
                                        </template>
                                    </div>

                                    {{-- EXISTING MEDIA --}}
                                    @if($mediaItems->isNotEmpty())
                                        <div class="mt-5">
                                            <div class="text-[11px] font-bold uppercase tracking-wide text-slate-500 mb-2">
                                                Existing media
                                            </div>

                                            <div class="relative rounded-xl overflow-hidden bg-slate-950">
                                                @foreach($mediaItems as $index => $mediaItem)
                                                    <div x-show="mediaIndex === {{ $index }}" x-cloak
                                                        class="h-[240px] flex items-center justify-center">
                                                        @if(($mediaItem->type ?? '') === 'video' || str_starts_with($mediaItem->mime_type ?? '', 'video'))
                                                            <video src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                                                class="w-full h-full object-contain"
                                                                controls></video>
                                                        @else
                                                            <img src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                                                class="w-full h-full object-contain">
                                                        @endif
                                                    </div>
                                                @endforeach

                                                @if($mediaItems->count() > 1)
                                                    <button type="button"
                                                        @click="previousMedia()"
                                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 shadow text-lg">
                                                        ‹
                                                    </button>

                                                    <button type="button"
                                                        @click="nextMedia()"
                                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 shadow text-lg">
                                                        ›
                                                    </button>

                                                    <span class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-black/60 text-white rounded-full px-2.5 py-1 text-[9px] font-bold"
                                                        x-text="(mediaIndex + 1) + ' / {{ $mediaItems->count() }}'"></span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- ADD MEDIA --}}
                                    <div class="mt-5 rounded-xl border border-dashed border-slate-300 p-4">
                                        <div class="flex items-center justify-between gap-3 mb-3">
                                            <div>
                                                <div class="text-[11px] font-bold text-slate-700">
                                                    Add photos or videos
                                                </div>
                                                <div class="text-[10px] text-slate-400 mt-1">
                                                    JPG, PNG, GIF, WEBP, MP4, MOV, AVI · Max 20MB
                                                </div>
                                            </div>

                                            <label class="shrink-0 cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#0b1329] text-white text-[10px] font-bold hover:bg-slate-800">
                                                Add media
                                                <input type="file"
                                                    x-ref="editMediaInput"
                                                    name="media[]"
                                                    multiple
                                                    accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi"
                                                    @change="handleEditFiles($event)"
                                                    class="hidden">
                                            </label>
                                        </div>

                                        <template x-if="newMediaPreviews.length">
                                            <div class="relative rounded-xl overflow-hidden bg-slate-950">
                                                <template x-for="(preview, index) in newMediaPreviews" :key="preview.name + '-' + index">
                                                    <div x-show="newMediaIndex === index" x-cloak
                                                        class="relative h-[220px] flex items-center justify-center">

                                                        <template x-if="preview.type === 'video'">
                                                            <video :src="preview.url"
                                                                class="w-full h-full object-contain"
                                                                controls></video>
                                                        </template>

                                                        <template x-if="preview.type === 'image'">
                                                            <img :src="preview.url"
                                                                class="w-full h-full object-contain">
                                                        </template>

                                                        <button type="button"
                                                            @click="removeNewMedia(index)"
                                                            class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/70 text-white">
                                                            ×
                                                        </button>
                                                    </div>
                                                </template>

                                                <template x-if="newMediaPreviews.length > 1">
                                                    <div>
                                                        <button type="button"
                                                            @click="previousNewMedia()"
                                                            class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 shadow">
                                                            ‹
                                                        </button>

                                                        <button type="button"
                                                            @click="nextNewMedia()"
                                                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 shadow">
                                                            ›
                                                        </button>

                                                        <span class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-black/60 text-white rounded-full px-2.5 py-1 text-[9px] font-bold"
                                                            x-text="(newMediaIndex + 1) + ' / ' + newMediaPreviews.length"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <div x-show="newMediaPreviews.length === 0"
                                            class="py-5 text-center text-[10px] text-slate-400">
                                            No new media selected.
                                        </div>
                                    </div>
                                </div>

                                <div class="h-[70px] px-5 sm:px-6 border-t border-slate-200 flex items-center justify-end gap-2">
                                    <button type="button"
                                        @click="closeEditPost()"
                                        class="h-10 px-5 rounded-full border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Cancel
                                    </button>

                                    <button type="submit"
                                        :disabled="postActionLoading"
                                        class="h-10 px-5 rounded-full bg-[#ffbd1b] text-slate-950 text-xs font-bold disabled:opacity-50">
                                        <span x-text="postActionLoading ? 'Saving...' : 'Save changes'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- DELETE POST MODAL --}}
                <template x-teleport="body">
                    <div x-show="postDeleteConfirm" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[210] flex items-center justify-center p-4">

                        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                            @click="postDeleteConfirm = false"></div>

                        <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl p-6"
                            @click.stop>

                            <h3 class="text-sm font-bold text-slate-900">
                                Delete this discussion?
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                This post and its discussion will be permanently removed.
                            </p>

                            <form method="POST"
                                action="{{ route('community.posts.destroy', ['post' => $post]) }}"
                                @submit="postActionLoading = true"
                                class="mt-6 flex justify-end gap-2">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="redirect_to" value="{{ $post->group_id ? route('community.groups.show', $post->group_id) : route('community.index') }}">

                                <button type="button"
                                    @click="postDeleteConfirm = false"
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">
                                    Cancel
                                </button>

                                <button type="submit"
                                    :disabled="postActionLoading"
                                    class="px-4 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold disabled:opacity-50">
                                    <span x-text="postActionLoading ? 'Deleting...' : 'Delete'"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- DELETE COMMENT / REPLY MODAL --}}
                <template x-teleport="body">
                    <div x-show="deleteConfirmComment" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[220] flex items-center justify-center p-4">

                        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                            @click="cancelDeleteComment()"></div>

                        <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl p-6"
                            @click.stop>

                            <h3 class="text-sm font-bold text-slate-900">
                                Delete this <span x-text="deleteConfirmIsReply ? 'reply' : 'comment'"></span>?
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                This action cannot be undone.
                            </p>

                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button"
                                    @click="cancelDeleteComment()"
                                    :disabled="commentActionLoading"
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">
                                    Cancel
                                </button>

                                <button type="button"
                                    @click="confirmDeleteComment()"
                                    :disabled="commentActionLoading"
                                    class="px-4 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold disabled:opacity-50">
                                    <span x-text="commentActionLoading ? 'Deleting...' : 'Delete'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </section>

        {{-- RIGHTBAR COMPONENT --}}
        <x-community.rightbar
            :user="$user"
            :trending-topics="$trendingTopics"
        />

    </main>

</body>
</html>