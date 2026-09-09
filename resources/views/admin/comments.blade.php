@extends('layouts.admin')

@section('title', 'Community Comments | REIAC Admin Panel')

@section('content')

<div
    x-data="{
        detailModalOpen: false,
        singleDeleteModalOpen: false,
        bulkDeleteModalOpen: false,

        selectedComments: [],
        selectAll: false,

        currentComment: {},
        deleteCommentId: null,

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedComments = Array.from(
                    document.querySelectorAll('.comment-checkbox')
                ).map(el => parseInt(el.value));
            } else {
                this.selectedComments = [];
            }
        },

        openDetailModal(comment) {
            this.currentComment = comment;
            this.detailModalOpen = true;
        },

        openSingleDeleteModal(commentId) {
            this.deleteCommentId = commentId;
            this.singleDeleteModalOpen = true;
        },

        closeSingleDeleteModal() {
            this.singleDeleteModalOpen = false;
            this.deleteCommentId = null;
        },

        openBulkDeleteModal() {
            if (this.selectedComments.length === 0) {
                return;
            }

            this.bulkDeleteModalOpen = true;
        },

        closeBulkDeleteModal() {
            this.bulkDeleteModalOpen = false;
        }
    }"
    class="space-y-6 font-sans text-slate-800"
>


    {{-- =========================================================
         TOAST / SUCCESS MESSAGE
    ========================================================== --}}

    @if(session('success'))

        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3500)"
            class="fixed bottom-5 right-5 z-[100] bg-slate-900 text-white text-xs font-medium px-4 py-3 rounded-xl shadow-2xl border border-slate-700 flex items-center space-x-2.5"
        >

            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Community Comments
            </h1>

            <p class="text-xs text-slate-500 mt-0.5">
                Review, moderate and manage comments and replies across the community.
            </p>

        </div>

    </div>


    {{-- =========================================================
         STAT CARDS
    ========================================================== --}}

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">


        {{-- Total Comments --}}

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                Total Comments
            </span>

            <div class="flex items-baseline justify-between">

                <span class="text-2xl font-extrabold text-slate-900">
                    {{ number_format($totalCommentsCount) }}
                </span>

                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">
                    Live DB
                </span>

            </div>

        </div>


        {{-- Today --}}

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                Comments Today
            </span>

            <div class="flex items-baseline justify-between">

                <span class="text-2xl font-extrabold text-slate-900">
                    {{ number_format($todayCommentsCount) }}
                </span>

                <span class="text-[10px] font-medium text-slate-400">
                    Active engagement
                </span>

            </div>

        </div>


        {{-- Reported --}}

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                Reported Comments
            </span>

            <div class="flex items-baseline justify-between">

                <span class="text-2xl font-extrabold text-slate-900">
                    {{ number_format($reportedCommentsCount) }}
                </span>

                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">
                    Action needed
                </span>

            </div>

        </div>


        {{-- Hidden --}}

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">

            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                Deleted / Hidden
            </span>

            <div class="flex items-baseline justify-between">

                <span class="text-2xl font-extrabold text-slate-900">
                    {{ number_format($hiddenCommentsCount) }}
                </span>

                <span class="text-[10px] font-medium text-slate-400">
                    Filtered out
                </span>

            </div>

        </div>

    </div>


    {{-- =========================================================
         SEARCH & FILTER TOOLBAR
    ========================================================== --}}

    <form
        method="GET"
        action="{{ route('admin.comments') }}"
        class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-3"
    >

        <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full md:w-auto">


            {{-- Search --}}

            <div class="relative w-full sm:w-64">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search comments, users, posts..."
                    class="w-full text-xs pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none"
                >

                <svg
                    class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>

            </div>


            {{-- Status Filter --}}

            <select
                name="status"
                onchange="this.form.submit()"
                class="w-full sm:w-auto text-xs border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 font-medium text-slate-700 outline-none"
            >

                <option value="">
                    All Statuses
                </option>

                <option
                    value="active"
                    {{ request('status') === 'active' ? 'selected' : '' }}
                >
                    Active
                </option>

                <option
                    value="reported"
                    {{ request('status') === 'reported' ? 'selected' : '' }}
                >
                    Reported
                </option>

                {{-- Hidden filter intentionally retained for existing data --}}

                <option
                    value="hidden"
                    {{ request('status') === 'hidden' ? 'selected' : '' }}
                >
                    Hidden
                </option>

            </select>


            {{-- Type Filter --}}

            <select
                name="type"
                onchange="this.form.submit()"
                class="w-full sm:w-auto text-xs border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 font-medium text-slate-700 outline-none"
            >

                <option value="">
                    All Types
                </option>

                <option
                    value="Comment"
                    {{ request('type') === 'Comment' ? 'selected' : '' }}
                >
                    Comments
                </option>

                <option
                    value="Reply"
                    {{ request('type') === 'Reply' ? 'selected' : '' }}
                >
                    Replies
                </option>

            </select>

        </div>


        <div class="flex items-center space-x-3 w-full md:w-auto justify-between md:justify-end">

            <a
                href="{{ route('admin.comments') }}"
                class="text-xs text-slate-400 hover:text-slate-600 font-medium"
            >
                Clear Filters
            </a>

            <button
                type="submit"
                class="px-3 py-1.5 bg-slate-900 text-white rounded-xl text-xs font-bold"
            >
                Filter
            </button>

        </div>

    </form>


    {{-- =========================================================
         BULK ACTION TOOLBAR
    ========================================================== --}}

    <div
        x-show="selectedComments.length > 0"
        x-transition
        x-cloak
        class="bg-slate-900 text-white px-4 py-2.5 rounded-2xl shadow-xl flex items-center justify-between text-xs"
    >

        <div class="flex items-center space-x-2">

            <span class="w-2 h-2 rounded-full bg-amber-400"></span>

            <span>
                Selected
                <strong x-text="selectedComments.length"></strong>
                comments
            </span>

        </div>


        <div class="flex items-center space-x-2">


            {{-- =====================================================
                 HIDE SELECTED FEATURE - COMMENTED OUT
            ====================================================== --}}

            {{--
            <form
                method="POST"
                action="{{ route('admin.comments.bulkAction') }}"
            >

                @csrf

                <input
                    type="hidden"
                    name="action"
                    value="hide"
                >

                <template
                    x-for="id in selectedComments"
                    :key="'hide-' + id"
                >

                    <input
                        type="hidden"
                        name="ids[]"
                        :value="id"
                    >

                </template>

                <button
                    type="submit"
                    class="px-3 py-1 bg-slate-800 hover:bg-slate-700 rounded-lg"
                >
                    Hide Selected
                </button>

            </form>
            --}}


            {{-- DELETE SELECTED --}}

            <button
                type="button"
                @click="openBulkDeleteModal()"
                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg"
            >
                Delete Selected
            </button>

        </div>

    </div>


    {{-- =========================================================
         COMMENTS TABLE
    ========================================================== --}}

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>

                    <tr class="bg-slate-50/70 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/60">


                        {{-- Select All --}}

                        <th class="py-3 px-3 w-10 text-center">

                            <input
                                type="checkbox"
                                x-model="selectAll"
                                @change="toggleSelectAll()"
                                class="rounded border-slate-300 text-amber-500 focus:ring-amber-500"
                            >

                        </th>


                        <th class="py-3 px-4 min-w-[280px]">
                            Comment Content
                        </th>

                        <th class="py-3 px-4">
                            Author
                        </th>

                        <th class="py-3 px-4 min-w-[200px]">
                            On Discussion Post
                        </th>

                        <th class="py-3 px-4 text-center">
                            Type
                        </th>

                        

                        {{-- <th class="py-3 px-4 text-center">
                            Replies
                        </th> --}}

                        <th class="py-3 px-4 text-center">
                            Status
                        </th>

                        <th class="py-3 px-4 text-right">
                            Date
                        </th>

                        <th class="py-3 px-4 text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100 text-xs font-medium">

                    @forelse($comments as $comment)

                        @php

                            $statusVal = is_object($comment->status)
                                ? ($comment->status->value ?? $comment->status->name)
                                : $comment->status;

                            $isReply = !is_null($comment->parent_id);

                            $userModel = $comment->user;

                            $avatarUrl = 'https://ui-avatars.com/api/?name='
                                . urlencode($userModel->name ?? 'User')
                                . '&background=0B132B&color=fff';

                            if ($userModel) {

                                if (!empty($userModel->profile_photo_url)) {

                                    $avatarUrl = $userModel->profile_photo_url;

                                } elseif (!empty($userModel->avatar)) {

                                    $avatarUrl = $userModel->avatar;

                                } elseif (
                                    isset($userModel->profile)
                                    && !empty($userModel->profile->avatar)
                                ) {

                                    $avatarUrl = $userModel->profile->avatar;

                                } elseif (!empty($userModel->profile_photo_path)) {

                                    $avatarUrl = asset(
                                        'storage/' . $userModel->profile_photo_path
                                    );

                                }

                            }


                            $commentData = [
                                'id' => $comment->id,

                                'content' => $comment->content,

                                'status' => ucfirst($statusVal),

                                'likes' => $comment->likes_count ?? 0,

                                'createdAt' => $comment->created_at
                                    ->format('d M Y, h:i A'),

                                'user' => [
                                    'name' => $userModel->name ?? 'Unknown',
                                    'username' => $userModel->username ?? 'user',
                                    'avatar' => $avatarUrl,
                                ],

                                'post' => [
                                    'title' => $comment->post->title ?? 'Deleted Post',
                                ],

                                'replies' => $comment->replies->map(
                                    fn ($r) => [
                                        'id' => $r->id,
                                        'name' => $r->user->name ?? 'User',
                                        'text' => $r->content,
                                        'time' => $r->created_at
                                            ->format('d M Y, h:i A'),
                                    ]
                                )->values(),

                            ];

                        @endphp


                        <tr class="hover:bg-slate-50/60 transition-colors">


                            {{-- Checkbox --}}

                            <td class="py-3 px-3 text-center">

                                <input
                                    type="checkbox"
                                    value="{{ $comment->id }}"
                                    x-model="selectedComments"
                                    class="comment-checkbox rounded border-slate-300 text-amber-500 focus:ring-amber-500"
                                >

                            </td>


                            {{-- Comment Content --}}

                            <td class="py-3 px-4">

                                <button
                                    type="button"
                                    @click='openDetailModal(@json($commentData))'
                                    class="text-left font-normal text-slate-800 hover:text-amber-600 line-clamp-2 leading-relaxed"
                                >
                                    {{ $comment->content }}
                                </button>

                            </td>


                            {{-- Author --}}

                            <td class="py-3 px-4 whitespace-nowrap">

                                <div class="flex items-center space-x-2">

                                    <img
                                        src="{{ $avatarUrl }}"
                                        class="w-7 h-7 rounded-full object-cover border border-slate-200"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($userModel->name ?? 'User') }}&background=0B132B&color=fff'"
                                    >

                                    <div>

                                        <p class="font-bold text-slate-900 leading-tight">
                                            {{ $userModel->name ?? 'Deleted User' }}
                                        </p>

                                        <p class="text-[10px] text-slate-400 font-normal">
                                            {{ '@' . ($userModel->username ?? 'user') }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- Discussion Post --}}

                            <td class="py-3 px-4">

                                @if($comment->post)

                                    <a
                                        href="{{ route('community.posts.show', $comment->post) }}"
                                        target="_blank"
                                        class="font-semibold text-slate-700 hover:underline line-clamp-1"
                                    >
                                        {{ $comment->post->title }}
                                    </a>

                                @else

                                    <span class="text-slate-400">
                                        Deleted Post
                                    </span>

                                @endif

                            </td>


                            {{-- Type --}}

                            <td class="py-3 px-4 text-center whitespace-nowrap">

                                <span
                                    class="px-2.5 py-0.5 rounded-md text-[10px] font-bold
                                    {{ $isReply
                                        ? 'bg-sky-50 text-sky-700'
                                        : 'bg-slate-100 text-slate-700'
                                    }}"
                                >
                                    {{ $isReply ? 'Reply' : 'Comment' }}
                                </span>

                            </td>


                            {{-- Likes --}}
{{-- 
                            <td class="py-3 px-4 text-center font-bold text-slate-800">

                                {{ $comment->likes_count ?? 0 }}

                            </td> --}}


                            {{-- Replies --}}

                            {{-- <td class="py-3 px-4 text-center font-bold text-slate-800">

                                {{ $comment->replies->count() }}

                            </td> --}}


                            {{-- Status --}}

                            <td class="py-3 px-4 text-center whitespace-nowrap">

                                @if($statusVal === 'active')

                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">

                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                                        <span>
                                            Visible
                                        </span>

                                    </span>

                                @elseif($statusVal === 'reported')

                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">

                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>

                                        <span>
                                            Reported
                                        </span>

                                    </span>

                                @else

                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200/60">

                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>

                                        <span>
                                            Hidden
                                        </span>

                                    </span>

                                @endif

                            </td>


                            {{-- Date --}}

                            <td class="py-3 px-4 text-right text-slate-400 text-[11px] whitespace-nowrap">

                                {{ $comment->created_at->format('d M Y, h:i A') }}

                            </td>


                            {{-- Actions --}}

                            <td class="py-3 px-4 text-right whitespace-nowrap">

                                <div class="flex items-center justify-end space-x-1">


                                    {{-- VIEW --}}

                                    <button
                                        type="button"
                                        @click='openDetailModal(@json($commentData))'
                                        class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100"
                                        title="View Comment"
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


                                    {{-- =====================================================
                                         INDIVIDUAL HIDE / RESTORE FEATURE - COMMENTED OUT
                                    ====================================================== --}}

                                    {{--
                                    <form
                                        method="POST"
                                        action="{{ route('admin.comments.toggleHide', $comment->id) }}"
                                        class="inline"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100"
                                            title="{{ $statusVal === 'hidden' ? 'Restore Comment' : 'Hide Comment' }}"
                                        >

                                            @if($statusVal === 'hidden')

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

                                            @else

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
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a9.04 9.04 0 012.122-.363c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"
                                                    />
                                                </svg>

                                            @endif

                                        </button>

                                    </form>
                                    --}}


                                    {{-- DELETE --}}

                                    <button
                                        type="button"
                                        @click="openSingleDeleteModal({{ $comment->id }})"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100"
                                        title="Delete Comment"
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
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />

                                        </svg>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="py-10 text-center text-slate-400"
                            >

                                No comments found in database.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}

        <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50">

            {{ $comments->links() }}

        </div>

    </div>


    {{-- =========================================================
         COMMENT DETAIL MODAL
    ========================================================== --}}

    <div
        x-show="detailModalOpen"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
    >

        <div
            @click.outside="detailModalOpen = false"
            class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 space-y-5 border border-slate-200 overflow-hidden"
        >


            {{-- Header --}}

            <div class="flex items-center justify-between border-b pb-3">

                <div class="flex items-center space-x-2">

                    <span
                        class="text-xs font-bold px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-mono"
                        x-text="'COMMENT #' + currentComment.id"
                    ></span>

                    <span
                        class="text-xs font-bold text-emerald-600 capitalize"
                        x-text="'● ' + currentComment.status"
                    ></span>

                </div>


                <button
                    type="button"
                    @click="detailModalOpen = false"
                    class="p-1 text-slate-400 hover:text-slate-600"
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


            <div class="space-y-4 text-xs">


                {{-- User --}}

                <div class="flex items-center space-x-3 p-3 bg-slate-50 rounded-xl">

                    <img
                        :src="currentComment.user?.avatar"
                        class="w-9 h-9 rounded-full object-cover"
                    >

                    <div>

                        <p
                            class="font-bold text-slate-900"
                            x-text="currentComment.user?.name"
                        ></p>

                        <p
                            class="text-[10px] text-slate-400"
                            x-text="'Commented on: ' + currentComment.post?.title"
                        ></p>

                    </div>

                </div>


                {{-- Comment Content --}}

                <div class="p-4 bg-white border border-slate-200 rounded-xl text-slate-800 leading-relaxed font-medium">

                    <p x-text="currentComment.content"></p>

                </div>


                {{-- Thread Replies --}}

                <div class="space-y-2">

                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[10px]">

                        Thread Replies

                        (
                        <span
                            x-text="currentComment.replies?.length || 0"
                        ></span>
                        )

                    </h4>


                    <template
                        x-if="currentComment.replies && currentComment.replies.length > 0"
                    >

                        <div class="space-y-2 pl-4 border-l-2 border-slate-200">

                            <template
                                x-for="r in currentComment.replies"
                                :key="r.id"
                            >

                                <div class="p-2.5 bg-slate-50 rounded-lg">

                                    <div class="flex justify-between font-bold text-slate-900">

                                        <span x-text="r.name"></span>

                                        <span
                                            class="text-[10px] text-slate-400"
                                            x-text="r.time"
                                        ></span>

                                    </div>

                                    <p
                                        class="text-slate-600 mt-0.5"
                                        x-text="r.text"
                                    ></p>

                                </div>

                            </template>

                        </div>

                    </template>


                    <template
                        x-if="!currentComment.replies || currentComment.replies.length === 0"
                    >

                        <p class="text-slate-400 italic">
                            No direct replies on this comment.
                        </p>

                    </template>

                </div>

            </div>


            {{-- Detail Actions --}}

            <div class="flex justify-end space-x-2 border-t pt-3">


                {{-- =================================================
                     DETAIL MODAL HIDE/RESTORE - COMMENTED OUT
                ================================================== --}}

                {{--
                <form
                    method="POST"
                    :action="'{{ url('/admin/comments') }}/' + currentComment.id + '/toggle-hide'"
                >

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl"
                    >

                        <span
                            x-text="currentComment.status === 'Hidden'
                                ? 'Restore Comment'
                                : 'Hide Comment'"
                        ></span>

                    </button>

                </form>
                --}}


                {{-- Delete --}}

                <button
                    type="button"
                    @click="
                        detailModalOpen = false;
                        openSingleDeleteModal(currentComment.id);
                    "
                    class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl"
                >
                    Delete Comment
                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
         SINGLE DELETE CONFIRMATION MODAL
    ========================================================== --}}

    <div
        x-show="singleDeleteModalOpen"
        x-cloak
        x-transition
        class="fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
    >

        <div
            @click.outside="closeSingleDeleteModal()"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200"
        >


            {{-- Warning Icon + Text --}}

            <div class="flex items-start space-x-4">

                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-rose-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
                        />

                    </svg>

                </div>


                <div>

                    <h3 class="font-bold text-slate-900 text-base">
                        Delete Comment?
                    </h3>

                    <p class="text-xs text-slate-600 leading-relaxed mt-1">

                        This comment will be permanently deleted.
                        This action cannot be undone.

                    </p>

                </div>

            </div>


            {{-- Delete Form --}}

            <form
                method="POST"
                :action="'{{ url('/admin/comments') }}/' + deleteCommentId"
                class="flex justify-end space-x-2 mt-6"
            >

                @csrf

                @method('DELETE')


                <button
                    type="button"
                    @click="closeSingleDeleteModal()"
                    class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl"
                >
                    Delete Comment
                </button>

            </form>

        </div>

    </div>


    {{-- =========================================================
         BULK DELETE CONFIRMATION MODAL
    ========================================================== --}}

    <div
        x-show="bulkDeleteModalOpen"
        x-cloak
        x-transition
        class="fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
    >

        <div
            @click.outside="closeBulkDeleteModal()"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200"
        >


            {{-- Warning Icon + Text --}}

            <div class="flex items-start space-x-4">

                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-rose-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
                        />

                    </svg>

                </div>


                <div>

                    <h3 class="font-bold text-slate-900 text-base">

                        Delete
                        <span x-text="selectedComments.length"></span>
                        selected comments?

                    </h3>


                    <p class="text-xs text-slate-600 leading-relaxed mt-1">

                        These comments will be permanently deleted.
                        This action cannot be undone.

                    </p>

                </div>

            </div>


            {{-- Bulk Delete Form --}}

            <form
                method="POST"
                action="{{ route('admin.comments.bulkAction') }}"
                class="mt-6"
            >

                @csrf


                <input
                    type="hidden"
                    name="action"
                    value="delete"
                >


                {{-- Selected Comment IDs --}}

                <template
                    x-for="id in selectedComments"
                    :key="'delete-' + id"
                >

                    <input
                        type="hidden"
                        name="ids[]"
                        :value="id"
                    >

                </template>


                <div class="flex justify-end space-x-2">


                    <button
                        type="button"
                        @click="closeBulkDeleteModal()"
                        class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl"
                    >
                        Delete Selected
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection