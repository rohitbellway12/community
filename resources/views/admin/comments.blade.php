@extends('layouts.admin')

@section('title', 'Community Comments | REIAC Admin Panel')

@section('content')

<div
    x-data="{
        detailModalOpen: false,
        selectedComments: [],
        selectAll: false,
        currentComment: {},

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
            this.currentComment = { ...comment };
            this.detailModalOpen = true;
        },

        closeDetailModal() {
            this.detailModalOpen = false;
        }
    }"
    class="p-6 space-y-6 max-w-[1400px] w-full mx-auto font-sans text-slate-800"
    @keydown.escape.window="closeDetailModal()"
>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl p-4 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Community Comments & Moderation</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review user engagement, moderate inappropriate content, hide or delete spam comments.</p>
        </div>

        <div class="flex items-center space-x-2.5">
            <template x-if="selectedComments.length > 0">
                <button
                    type="button"
                    onclick="confirmBulkDelete()"
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h14"/></svg>
                    <span>Delete Selected (<span x-text="selectedComments.length"></span>)</span>
                </button>
            </template>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        
        {{-- Total Comments --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Comments</span>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalCommentsCount) }}</div>
        </div>

        {{-- Visible / Active --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Active / Visible</span>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($activeCommentsCount) }}</div>
        </div>

        {{-- Today --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Posted Today</span>
            <div class="text-2xl font-black text-indigo-600">{{ number_format($todayCommentsCount) }}</div>
        </div>

        {{-- Hidden --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Hidden</span>
            <div class="text-2xl font-black text-slate-600">{{ number_format($hiddenCommentsCount) }}</div>
        </div>

        {{-- Reported --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Reported</span>
            <div class="text-2xl font-black text-rose-600">{{ number_format($reportedCommentsCount) }}</div>
        </div>

    </div>

    {{-- FILTER TABS & SEARCH TOOLBAR --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 space-y-3">
        
        {{-- Status Tabs --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <a href="{{ route('admin.comments') }}"
                   class="px-3 py-1.5 rounded-lg transition {{ !request()->filled('status') ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    All Comments ({{ $totalCommentsCount }})
                </a>
                <a href="{{ route('admin.comments', ['status' => 'active'] + request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'active' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    Visible ({{ $activeCommentsCount }})
                </a>
                <a href="{{ route('admin.comments', ['status' => 'hidden'] + request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'hidden' ? 'bg-white text-slate-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    Hidden ({{ $hiddenCommentsCount }})
                </a>
                <a href="{{ route('admin.comments', ['status' => 'reported'] + request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'reported' ? 'bg-white text-rose-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    Reported ({{ $reportedCommentsCount }})
                </a>
            </div>

            <span class="text-xs text-slate-400 font-medium">
                Showing {{ $comments->firstItem() ?? 0 }}–{{ $comments->lastItem() ?? 0 }} of {{ $comments->total() }} results
            </span>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('admin.comments') }}" class="flex flex-col sm:flex-row items-center gap-2.5">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search comment content, author name, or discussion title..."
                       class="w-full text-xs pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
            </div>

            <div class="w-full sm:w-48">
                <select name="type" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold text-slate-700">
                    <option value="">All Types (Comments & Replies)</option>
                    <option value="Comment" {{ request('type') === 'Comment' ? 'selected' : '' }}>Top-level Comments</option>
                    <option value="Reply" {{ request('type') === 'Reply' ? 'selected' : '' }}>Thread Replies</option>
                </select>
            </div>

            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Filter
                </button>
                @if(request('search') || request('type') || request('status'))
                    <a href="{{ route('admin.comments') }}" class="px-3 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

    </div>

    {{-- COMMENTS TABLE --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3.5 px-3 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="py-3.5 px-4 min-w-[280px]">Comment Content</th>
                        <th class="py-3.5 px-4">Author</th>
                        <th class="py-3.5 px-4 min-w-[180px]">On Discussion</th>
                        <th class="py-3.5 px-4 text-center">Type</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($comments as $comment)
                        @php
                            $rawStatus = $comment->status ?? 'active';
                            $statusVal = is_object($rawStatus) ? ($rawStatus->value ?? (string) $rawStatus) : (string) $rawStatus;
                            $isReply = !is_null($comment->parent_id);
                            $userModel = $comment->user;

                            $authorAvatar = optional($userModel?->profile)->avatar
                                ? asset('storage/' . $userModel->profile->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($userModel->name ?? 'User') . '&background=0B132B&color=fff';

                            $commentPayload = [
                                'id' => $comment->id,
                                'content' => $comment->content,
                                'status' => ucfirst($statusVal),
                                'status_raw' => $statusVal,
                                'is_reply' => $isReply,
                                'likes_count' => (int) ($comment->likes_count ?? 0),
                                'created_at' => $comment->created_at ? $comment->created_at->format('d M Y, h:i A') : 'N/A',
                                'user' => [
                                    'name' => $userModel->name ?? 'Deleted User',
                                    'username' => optional($userModel?->profile)->username ?? 'user',
                                    'avatar' => $authorAvatar
                                ],
                                'post' => [
                                    'title' => $comment->post->title ?? 'Deleted Discussion',
                                    'url' => $comment->post ? route('community.posts.show', $comment->post) : null
                                ],
                                'replies' => $comment->replies->map(fn($r) => [
                                    'id' => $r->id,
                                    'name' => $r->user->name ?? 'User',
                                    'content' => $r->content,
                                    'time' => $r->created_at ? $r->created_at->format('d M Y, h:i A') : ''
                                ])->values()->all()
                            ];
                        @endphp

                        <tr class="hover:bg-slate-50/70 transition-colors">
                            
                            {{-- Checkbox --}}
                            <td class="py-3.5 px-3 text-center">
                                <input type="checkbox" value="{{ $comment->id }}" x-model="selectedComments" class="comment-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>

                            {{-- Comment Content --}}
                            <td class="py-3.5 px-4 max-w-sm">
                                <button type="button" @click='openDetailModal(@json($commentPayload))'
                                        class="text-left font-medium text-slate-800 hover:text-indigo-600 line-clamp-2 leading-relaxed transition">
                                    {{ $comment->content }}
                                </button>
                            </td>

                            {{-- Author --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2.5">
                                    <img src="{{ $authorAvatar }}" class="w-7 h-7 rounded-xl object-cover border border-slate-200 shrink-0">
                                    <div>
                                        <p class="font-bold text-slate-800 leading-tight">{{ $userModel->name ?? 'Deleted User' }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">@ {{ optional($userModel?->profile)->username ?? 'user' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- On Discussion --}}
                            <td class="py-3.5 px-4 max-w-xs">
                                @if($comment->post)
                                    <a href="{{ route('community.posts.show', $comment->post) }}" target="_blank"
                                       class="font-semibold text-slate-700 hover:text-indigo-600 hover:underline line-clamp-1 transition">
                                        {{ $comment->post->title }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Deleted Discussion</span>
                                @endif
                            </td>

                            {{-- Type --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $isReply ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    {{ $isReply ? 'Reply' : 'Comment' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($statusVal === 'active')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Visible</span>
                                    </span>
                                @elseif($statusVal === 'reported')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Reported</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Hidden</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="py-3.5 px-4 text-right text-slate-400 text-[11px] whitespace-nowrap">
                                {{ $comment->created_at?->format('d M Y, h:i A') }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-1.5">
                                    
                                    {{-- View Details --}}
                                    <button type="button" @click='openDetailModal(@json($commentPayload))'
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 bg-slate-100 hover:bg-indigo-50 rounded-xl transition"
                                            title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    {{-- Hide / Restore Toggle Button --}}
                                    <form id="toggle-hide-{{ $comment->id }}" method="POST" action="{{ route('admin.comments.toggleHide', $comment->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                                onclick="confirmToggleHide('toggle-hide-{{ $comment->id }}', '{{ $statusVal === 'hidden' ? 'restore' : 'hide' }}')"
                                                class="p-1.5 rounded-xl transition {{ $statusVal === 'hidden' ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200' }}"
                                                title="{{ $statusVal === 'hidden' ? 'Restore Comment' : 'Hide Comment' }}">
                                            @if($statusVal === 'hidden')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a9.04 9.04 0 012.122-.363c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete Comment --}}
                                    <form id="delete-comment-{{ $comment->id }}" method="POST" action="{{ route('admin.comments.destroy', $comment->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="confirmCommentDelete('delete-comment-{{ $comment->id }}')"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 rounded-xl transition"
                                                title="Delete Comment">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h14"/></svg>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-bold">No comments found matching criteria.</p>
                                <p class="text-xs text-slate-400 mt-1">Try clearing your search query or status filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $comments->links() }}
            </div>
        @endif
    </div>

    {{-- COMMENT DETAIL MODAL --}}
    <div
        x-show="detailModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    >
        <div @click.outside="closeDetailModal()" class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full border border-slate-100 overflow-hidden flex flex-col my-auto max-h-[90vh]">
            
            {{-- Modal Topbar --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <h3 class="text-sm font-black text-slate-800">Comment Details</h3>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600" x-text="'#' + currentComment.id"></span>
                </div>
                <button @click="closeDetailModal()" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-4">
                
                {{-- Author & Discussion Link --}}
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img :src="currentComment.user?.avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 leading-tight" x-text="currentComment.user?.name"></h4>
                                <p class="text-[10px] text-slate-400" x-text="'@' + currentComment.user?.username"></p>
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium" x-text="currentComment.created_at"></span>
                    </div>

                    <div class="text-xs text-slate-600 border-t border-slate-200/60 pt-2 flex items-center space-x-1.5">
                        <span class="text-slate-400 font-bold">Discussion:</span>
                        <a :href="currentComment.post?.url" target="_blank" class="font-bold text-indigo-600 hover:underline truncate" x-text="currentComment.post?.title"></a>
                    </div>
                </div>

                {{-- Comment Content Box --}}
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Comment Text</span>
                    <div class="p-4 bg-white border border-slate-200 rounded-2xl text-xs text-slate-800 leading-relaxed font-medium whitespace-pre-line" x-text="currentComment.content"></div>
                </div>

                {{-- Thread Replies (if any) --}}
                <div class="space-y-2">
                    <h4 class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Thread Replies (<span x-text="currentComment.replies?.length || 0"></span>)</span>
                    </h4>

                    <template x-if="currentComment.replies && currentComment.replies.length > 0">
                        <div class="space-y-2 pl-3 border-l-2 border-slate-200">
                            <template x-for="reply in currentComment.replies" :key="reply.id">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex items-center justify-between text-slate-800 font-bold text-xs mb-1">
                                        <span x-text="reply.name"></span>
                                        <span class="text-[10px] font-normal text-slate-400" x-text="reply.time"></span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed font-medium" x-text="reply.content"></p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!currentComment.replies || currentComment.replies.length === 0">
                        <p class="text-xs text-slate-400 italic font-medium p-2">No direct replies posted on this comment yet.</p>
                    </template>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-between shrink-0">
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                      :class="currentComment.status_raw === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                      x-text="currentComment.status"></span>

                <div class="flex items-center space-x-2">
                    <button type="button" @click="closeDetailModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Close
                    </button>

                    {{-- Toggle Hide Form in Modal --}}
                    <form :action="'{{ url('/community/admin/comments') }}/' + currentComment.id + '/toggle-hide'" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-slate-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition">
                            <span x-text="currentComment.status_raw === 'hidden' ? 'Restore Comment' : 'Hide Comment'"></span>
                        </button>
                    </form>

                    {{-- Delete in Modal --}}
                    <form :action="'{{ route('admin.comments') }}/' + currentComment.id" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure you want to permanently delete this comment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- HIDDEN BULK DELETE FORM --}}
    <form id="bulk-delete-form" method="POST" action="{{ route('admin.comments.bulkAction') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" value="delete">
        <template x-for="id in selectedComments" :key="'bulk-del-' + id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

</div>

<script>
function confirmToggleHide(formId, action) {
    const isRestoring = action === 'restore';
    Swal.fire({
        title: isRestoring ? 'Restore Comment?' : 'Hide Comment?',
        text: isRestoring ? 'This comment will become visible again to all community members.' : 'This comment will be hidden from normal community view.',
        icon: isRestoring ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonText: isRestoring ? 'Yes, Restore' : 'Yes, Hide',
        cancelButtonText: 'Cancel',
        confirmButtonColor: isRestoring ? '#10B981' : '#EAB308',
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
            confirmButton: 'rounded-xl font-bold px-5 py-2.5 shadow-xs',
            cancelButton: 'rounded-xl font-bold px-5 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

function confirmCommentDelete(formId) {
    Swal.fire({
        title: 'Delete Comment Permanently?',
        text: 'This comment and its thread associations will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
            confirmButton: 'rounded-xl font-bold px-5 py-2.5 shadow-xs',
            cancelButton: 'rounded-xl font-bold px-5 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

function confirmBulkDelete() {
    Swal.fire({
        title: 'Delete Selected Comments?',
        text: 'All selected comments will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete All',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
            confirmButton: 'rounded-xl font-bold px-5 py-2.5 shadow-xs',
            cancelButton: 'rounded-xl font-bold px-5 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('bulk-delete-form').submit();
        }
    });
}
</script>

@endsection