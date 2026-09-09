@extends('layouts.admin')

@section('title', 'Community Posts | REIAC Admin Panel')

@section('content')

<div
    x-data="{
        loading: false,

        viewModalOpen: false,
        editModalOpen: false,
        confirmModalOpen: false,
        reviewReportModalOpen: false,

        activeTab: 'all',

        toast: {
            show: false,
            message: ''
        },

        selectedPosts: [],
        selectAll: false,

        currentPost: {
            id: null,
            title: '',
            content: '',
            category_id: '',
            status: '',
            is_featured: false
        },

        showToast(msg) {
            this.toast.message = msg;
            this.toast.show = true;

            setTimeout(() => {
                this.toast.show = false;
            }, 3500);
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedPosts = Array.from(
                    document.querySelectorAll('.post-checkbox')
                ).map(el => parseInt(el.value));
            } else {
                this.selectedPosts = [];
            }
        },

        openViewModal(post) {
            this.currentPost = post;
            this.viewModalOpen = true;
            document.body.classList.add('overflow-hidden');
        },

        openEditModal(post) {
            this.currentPost = {
                id: post.id,
                title: post.title || '',
                content: post.content || '',
                category_id: post.category_id || '',
                status: post.status || '',
                is_featured: post.is_featured || false
            };

            this.editModalOpen = true;
            document.body.classList.add('overflow-hidden');
        },

        closeEditModal() {
            this.editModalOpen = false;
            document.body.classList.remove('overflow-hidden');
        },

        closeViewModal() {
            this.viewModalOpen = false;
            document.body.classList.remove('overflow-hidden');
        },

        openReviewModal(post) {
            this.currentPost = post;
            this.reviewReportModalOpen = true;
            document.body.classList.add('overflow-hidden');
        },

        closeAllModals() {
            this.viewModalOpen = false;
            this.editModalOpen = false;
            this.confirmModalOpen = false;
            this.reviewReportModalOpen = false;

            document.body.classList.remove('overflow-hidden');
        }
    }"
    class="space-y-6 font-sans text-slate-800"
    @keydown.escape.window="closeAllModals()"
>

    {{-- ============================================================
         TOAST
    ============================================================= --}}
    <div
        x-show="toast.show"
        x-transition
        x-cloak
        class="fixed bottom-5 right-5 z-[10000] bg-slate-900 text-white text-xs font-medium px-4 py-3 rounded-xl shadow-2xl border border-slate-700 flex items-center space-x-2.5"
    >
        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
        <span x-text="toast.message"></span>
    </div>


    {{-- ============================================================
         SUCCESS MESSAGE
    ============================================================= --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif


    {{-- ============================================================
         ERROR MESSAGE
    ============================================================= --}}
    @if ($errors->any())
        <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-200 text-xs">
            <p class="font-bold mb-2">Please fix the following errors:</p>

            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ============================================================
         HEADER
    ============================================================= --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Community Posts
            </h1>

            <p class="text-xs text-slate-500 mt-0.5">
                Manage community discussions, content, engagement and moderation.
            </p>
        </div>

        <div>
            <a
                href="{{ route('community.posts.create') }}"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition-all flex items-center space-x-1.5"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                <span>Create Post</span>
            </a>
        </div>

    </div>


    {{-- ============================================================
         SUMMARY CARDS
    ============================================================= --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <div class="flex items-center justify-between text-slate-400 mb-1">

                <span class="text-[11px] font-bold uppercase tracking-wider">
                    Total Posts
                </span>

                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                    />
                </svg>

            </div>

            <span class="text-2xl font-extrabold text-slate-900">
                {{ $posts->total() }} 
            </span>

        </div>


        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <div class="flex items-center justify-between text-slate-400 mb-1">

                <span class="text-[11px] font-bold uppercase tracking-wider">
                    Published
                </span>

            </div>

            <span class="text-2xl font-extrabold text-emerald-600">
                {{ $posts->where('status', 'published')->count() }}
            </span>

        </div>


        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <div class="flex items-center justify-between text-slate-400 mb-1">

                <span class="text-[11px] font-bold uppercase tracking-wider">
                    Drafts
                </span>

            </div>

            <span class="text-2xl font-extrabold text-amber-600">
                {{ $posts->where('status', 'draft')->count() }}
            </span>

        </div>


        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <div class="flex items-center justify-between text-slate-400 mb-1">

                <span class="text-[11px] font-bold uppercase tracking-wider">
                    Featured
                </span>

            </div>

            <span class="text-2xl font-extrabold text-indigo-600">
                {{ $posts->where('is_featured', true)->count() }}
            </span>

        </div>

    </div>


    {{-- ============================================================
         FILTERS
    ============================================================= --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4">

        <form
            method="GET"
            action="{{ route('admin.posts') }}"
            class="flex flex-col md:flex-row gap-3"
        >

            <div class="flex-1">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search posts or authors..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-indigo-500 focus:ring-indigo-500"
                >

            </div>


            <select
                name="status"
                class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-indigo-500 focus:ring-indigo-500"
            >

                <option value="">
                    All Status
                </option>

                <option
                    value="published"
                    {{ request('status') === 'published' ? 'selected' : '' }}
                >
                    Published
                </option>

                <option
                    value="draft"
                    {{ request('status') === 'draft' ? 'selected' : '' }}
                >
                    Draft
                </option>

                <option
                    value="archived"
                    {{ request('status') === 'archived' ? 'selected' : '' }}
                >
                    Archived
                </option>

            </select>


            <button
                type="submit"
                class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition"
            >
                Filter
            </button>

        </form>

    </div>


    {{-- ============================================================
         POSTS TABLE
    ============================================================= --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="bg-slate-50 border-b border-slate-200">

                    <tr>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Post
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Author
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Category
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Status
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Views
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Likes
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Comments
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Created
                        </th>

                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($posts as $post)

                        @php
    $rawStatus = $post->status ?? 'published';
    $statusValue = is_object($rawStatus) && property_exists($rawStatus, 'value') 
        ? $rawStatus->value 
        : (string) $rawStatus;

    $avatarUrl = optional($post->user?->profile)->avatar
        ? asset('storage/' . $post->user->profile->avatar)
        : null;

    $postForModal = [
        'id' => $post->id,
        'title' => $post->title,
        'content' => $post->content,
        'category_id' => $post->category_id,
        'status' => $statusValue,
        'is_featured' => (bool) ($post->is_featured ?? false),
    ];
@endphp

                        <tr class="hover:bg-slate-50/70 transition">

                            {{-- POST --}}
                            <td class="px-5 py-4 max-w-xs">

                                <div class="min-w-0">

                                    <p class="text-sm font-bold text-slate-900 truncate">
                                        {{ $post->title }}
                                    </p>

                                    <p class="text-[11px] text-slate-400 mt-1 line-clamp-2">
                                        {{ Str::limit($post->content, 100) }}
                                    </p>

                                </div>

                            </td>


                            {{-- AUTHOR --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-2.5">

                                    @if($avatarUrl)

                                        <img
                                            src="{{ $avatarUrl }}"
                                            alt="{{ $post->user->name ?? 'User' }}"
                                            class="w-8 h-8 rounded-full object-cover"
                                        >

                                    @else

                                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                            {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                                        </div>

                                    @endif


                                    <div class="min-w-0">

                                        <p class="text-xs font-semibold text-slate-800 truncate">
                                            {{ $post->user->name ?? 'Unknown' }}
                                        </p>

                                        <p class="text-[10px] text-slate-400 truncate">
                                            {{ optional($post->user?->profile)->username
                                                ? '@' . $post->user->profile->username
                                                : 'user'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- CATEGORY --}}
                            <td class="px-5 py-4">

                                <span class="text-xs text-slate-600">
                                    {{ $post->category->name ?? 'General' }}
                                </span>

                            </td>


                            {{-- STATUS --}}
                            <td class="px-5 py-4">

                                @php
                                    $statusClass = match(strtolower($statusValue)) {
                                        'published' => 'bg-emerald-50 text-emerald-700',
                                        'draft' => 'bg-amber-50 text-amber-700',
                                        'archived' => 'bg-slate-100 text-slate-600',
                                        'blocked' => 'bg-red-50 text-red-700',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp

                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusClass }}"
                                >
                                    {{ ucfirst($statusValue) }} 
                                </span>

                            </td>


                            {{-- VIEWS --}}
                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ number_format($post->views_count ?? 0) }} 
                            </td>


                            {{-- LIKES --}}
                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ number_format($post->likes_count ?? 0) }} 
                            </td>


                            {{-- COMMENTS --}}
                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ number_format($post->comments_count ?? 0) }} 
                            </td>


                            {{-- CREATED --}}
                            <td class="px-5 py-4 whitespace-nowrap">

                                <span class="text-[11px] text-slate-500">
                                    {{ $post->created_at?->format('d M Y') }} 
                                </span>

                            </td>


                            {{-- ACTIONS --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center justify-end gap-1.5">


                                    {{-- VIEW --}}
                                    <button
                                        type="button"
                                        @click="openViewModal(@js($postForModal))" 
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors"
                                        title="View Details"
                                    >

                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />

                                        </svg>

                                    </button>


                                    {{-- ==================================================
                                         EDIT BUTTON
                                    =================================================== --}}
                                    <button
                                        type="button"
                                        @click="openEditModal(@js($postForModal))" 
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors"
                                        title="Edit Post"
                                    >

                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16.5 3.5a2.121 2.121 0 013 3L12 14l-4 1 1-4 8.5-8.5z"
                                            />

                                        </svg>

                                    </button>


                                  

                                    


                                    {{-- DELETE --}}
                                    <form
                                        method="POST"
                                        action="{{ route('community.posts.destroy', $post) }}" 
                                        onsubmit="return confirm('Are you sure you want to delete this post?');"
                                        class="inline"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Delete Post"
                                        >

                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h14"
                                                />

                                            </svg>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="px-5 py-12 text-center"
                            >

                                <p class="text-sm font-semibold text-slate-600">
                                    No posts found.
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    Try changing your search or filters.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($posts->hasPages()) 

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $posts->links() }} 
            </div>

        @endif

    </div>


    {{-- ============================================================
         VIEW POST MODAL
    ============================================================= --}}
    <div
        x-show="viewModalOpen" 
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
    >

        <div
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
            @click="closeViewModal()" 
        ></div>


        <div
            x-show="viewModalOpen" 
            x-transition
            class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl"
        >

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">

                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Post Details
                    </h2>
                </div>

                <button
                    type="button"
                    @click="closeViewModal()" 
                    class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <div class="p-6 space-y-5">

                <div>

                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Title
                    </p>

                    <h3
                        class="text-xl font-bold text-slate-900 mt-1"
                        x-text="currentPost.title" 
                    ></h3>

                </div>


                <div>

                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Content
                    </p>

                    <p
                        class="mt-2 text-sm leading-6 text-slate-600 whitespace-pre-line"
                        x-text="currentPost.content" 
                    ></p>

                </div>


                <div class="grid grid-cols-2 gap-4">

                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-[10px] font-bold uppercase text-slate-400">
                            Status
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-900"
                            x-text="currentPost.status" 
                        ></p>

                    </div>


                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-[10px] font-bold uppercase text-slate-400">
                            Featured
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-900"
                            x-text="currentPost.is_featured ? 'Yes' : 'No'" 
                        ></p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         EDIT POST MODAL
    ============================================================= --}}
    <div
        x-show="editModalOpen" 
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
    >

        {{-- BACKDROP --}}
        <div
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
            @click="closeEditModal()" 
        ></div>


        {{-- MODAL --}}
        <div
            x-show="editModalOpen" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl"
            @click.stop
        >

            {{-- HEADER --}}
            <div class="sticky top-0 z-20 flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-white">

                <div>

                    <h2 class="text-lg font-bold text-slate-900">
                        Edit Post
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Update community post information.
                    </p>

                </div>


                <button
                    type="button"
                    @click="closeEditModal()" 
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition"
                >

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>

                </button>

            </div>


            {{-- FORM --}}
            <form
                method="POST"
                :action="'{{ url('/admin/posts') }}/' + currentPost.id" 
                enctype="multipart/form-data"
            >

                @csrf
                @method('PATCH')


                <div class="p-6 space-y-5">


                    {{-- TITLE --}}
                    <div>

                        <label
                            for="edit_title"
                            class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2"
                        >
                            Post Title
                        </label>

                        <input
                            id="edit_title"
                            type="text"
                            name="title"
                            x-model="currentPost.title" 
                            required
                            maxlength="255"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                            placeholder="Enter post title"
                        >

                    </div>


                    {{-- CATEGORY --}}
                    <div>

                        <label
                            for="edit_category"
                            class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2"
                        >
                            Category
                        </label>

                        <select
                            id="edit_category"
                            name="category_id"
                            x-model="currentPost.category_id" 
                            required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                        >

                            <option value="">
                                Select Category
                            </option>

                            @foreach($categories as $category)

                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- CONTENT --}}
                    <div>

                        <label
                            for="edit_content"
                            class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2"
                        >
                            Content
                        </label>

                        <textarea
                            id="edit_content"
                            name="content"
                            x-model="currentPost.content" 
                            rows="9"
                            required
                            class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                            placeholder="Write post content..."
                        ></textarea>

                    </div>


                    {{-- STATUS --}}
                    <div>

                        <label
                            for="edit_status"
                            class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2"
                        >
                            Status
                        </label>

                        <select
                            id="edit_status"
                            name="status"
                            x-model="currentPost.status" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                        >

                            <option value="published">
                                Published
                            </option>

                            <option value="draft">
                                Draft
                            </option>

                            <option value="archived">
                                Archived
                            </option>

                            <option value="blocked">
                                Blocked
                            </option>

                        </select>

                    </div>


                    {{-- FEATURED --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                        <label class="flex items-center gap-3 cursor-pointer">

                            <input
                                type="checkbox"
                                name="is_featured"
                                value="1"
                                x-model="currentPost.is_featured" 
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >

                            <div>

                                <p class="text-sm font-semibold text-slate-800">
                                    Featured Post
                                </p>

                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Mark this post as featured in the community.
                                </p>

                            </div>

                        </label>

                    </div>


                    {{-- MEDIA --}}
                    <div>

                        <label
                            for="edit_media"
                            class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2"
                        >
                            Add Media
                        </label>

                        <input
                            id="edit_media"
                            type="file"
                            name="media[]"
                            multiple
                            accept=".jpg,.jpeg,.png,.webp,.mp4"
                            class="block w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
                        >

                        <p class="mt-1.5 text-[10px] text-slate-400">
                            JPG, JPEG, PNG, WEBP or MP4. Maximum 10MB per file.
                        </p>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="sticky bottom-0 flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">

                    <button
                        type="button"
                        @click="closeEditModal()" 
                        class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 transition"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition"
                    >
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>


</div>

@endsection