@extends('layouts.admin')

@section('title', 'Events & Contest Management')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="eventManagement()">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-800 text-xs shadow-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <span>⚠️</span>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside pl-4 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Engagement & Growth</span>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500">App Referral & Activity Events</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-0.5">Events & Referral Contests</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Start/end manual contests with custom scoring weights. Top 5 participants automatically appear on the mobile app home top bar.
            </p>
        </div>

        <button
            type="button"
            @click="openCreateModal()"
            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition flex items-center gap-2 shrink-0 active:scale-95"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create New Event</span>
        </button>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Events --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Contests</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalEvents) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Created events</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                🏆
            </div>
        </div>

        {{-- Running / Live Events --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Live / Running Now</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($runningEventsCount) }}</div>
                <div class="text-xs text-emerald-600 font-medium mt-0.5">Active & in date window</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                ⚡
            </div>
        </div>

        {{-- Active Status Events --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Enabled Events</div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($activeEventsCount) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Status = Active</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                🎯
            </div>
        </div>

        {{-- Total App Referrals --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Referrals</div>
                <div class="text-2xl font-black text-indigo-600 mt-1">{{ number_format($totalReferralsCount) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">All-time invited users</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                👥
            </div>
        </div>
    </div>

    {{-- HOW SCORING & LEADERBOARD WORKS NOTICE --}}
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/90 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs shadow-xs">
        <div class="flex items-start gap-3">
            <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-lg shrink-0">
                💡
            </span>
            <div>
                <h3 class="font-extrabold text-amber-950 text-sm">Automated Event Scoring & App Integration</h3>
                <p class="text-amber-800 text-xs mt-0.5">
                    User scores are calculated dynamically during each event period:
                    <span class="font-bold text-slate-900 bg-white/90 px-1.5 py-0.5 rounded border border-amber-300 ml-1">(Posts × Weight) + (Comments × Weight) + (Likes × Weight) + (Referrals × Weight)</span>.
                    The top 5 winners are automatically served to the mobile app home top bar as an interactive banner.
                </p>
            </div>
        </div>
        <div class="text-[11px] text-amber-800 font-medium shrink-0 bg-white/80 px-3 py-1.5 rounded-xl border border-amber-200/60">
            📲 Endpoint: <code>/api/v1/active-event</code>
        </div>
    </div>

    {{-- EVENTS TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Configured Contests</h2>
            <span class="text-xs text-slate-400">Sorted by sort order & start date</span>
        </div>

        @if($events->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3.5 px-4">Event Details</th>
                            <th class="py-3.5 px-4">Date Duration</th>
                            <th class="py-3.5 px-4 text-center">Point Weights</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($events as $event)
                            <tr class="hover:bg-slate-50/50 transition">
                                {{-- Event Details --}}
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0">
                                            @if($event->banner_image_url)
                                                <img src="{{ $event->banner_image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs font-bold">
                                                    No Img
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-900 text-sm truncate max-w-xs">{{ $event->title }}</div>
                                            <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                                <span>Slug: <span class="font-mono text-slate-600">{{ $event->slug }}</span></span>
                                                <span>•</span>
                                                <a href="{{ Route::has('events.show') ? route('events.show', $event->slug) : (Route::has('community.events.show') ? route('community.events.show', $event->slug) : url('/events/' . $event->slug)) }}" target="_blank" class="text-amber-600 hover:underline font-semibold flex items-center gap-0.5">
                                                    Rules Page ↗
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date Duration & Status --}}
                                <td class="py-4 px-4">
                                    <div class="space-y-1">
                                        <div class="text-slate-700 font-medium">
                                            <span class="text-slate-400 text-[10px] uppercase font-bold">Start:</span>
                                            {{ $event->start_at->format('M d, Y h:i A') }}
                                        </div>
                                        <div class="text-slate-700 font-medium">
                                            <span class="text-slate-400 text-[10px] uppercase font-bold">End:</span>
                                            {{ $event->end_at->format('M d, Y h:i A') }}
                                        </div>

                                        {{-- Timing Pill --}}
                                        <div>
                                            @if($event->isRunning())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Live Now
                                                </span>
                                            @elseif(now()->lt($event->start_at))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                    Starts in {{ now()->diffForHumans($event->start_at, true) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                                    Ended {{ $event->end_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Scoring Weights --}}
                                <td class="py-4 px-4 text-center">
                                    <div class="inline-grid grid-cols-2 gap-1.5 text-[10px]">
                                        <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold border border-amber-200">
                                            Ref: +{{ $event->referrals_weight }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold border border-blue-200">
                                            Post: +{{ $event->posts_weight }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                            Comm: +{{ $event->comments_weight }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                            Like: +{{ $event->likes_weight }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Status Toggle --}}
                                <td class="py-4 px-4 text-center">
                                    <form action="{{ route('admin.events.updateStatus', $event) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1 rounded-full text-[11px] font-bold transition {{ $event->status === 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : ($event->status === 'draft' ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200') }}"
                                            title="Click to toggle status"
                                        >
                                            {{ ucfirst($event->status) }}
                                        </button>
                                    </form>
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    {{-- Leaderboard --}}
                                    <a
                                        href="{{ route('admin.events.leaderboard', $event) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs transition"
                                        title="View Leaderboard"
                                    >
                                        🏆 Scores
                                    </a>

                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        @click="openEditModal({{ json_encode($event) }})"
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition"
                                    >
                                        Edit
                                    </button>

                                    {{-- Delete --}}
                                    <form
                                        action="{{ route('admin.events.destroy', $event) }}"
                                        method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($events->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $events->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center text-slate-400">
                <div class="text-4xl mb-3">🏆</div>
                <div class="text-base font-bold text-slate-700">No Events Created Yet</div>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">Create your first community contest to encourage referrals, posts, and member interaction!</p>
                <button
                    type="button"
                    @click="openCreateModal()"
                    class="mt-4 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition"
                >
                    Create First Event
                </button>
            </div>
        @endif
    </div>

    {{-- CREATE / EDIT MODAL --}}
    <div
        x-show="modalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.outside="modalOpen = false"
            class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8"
        >
            {{-- Modal Header --}}
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900" x-text="isEdit ? 'Edit Event & Scoring' : 'Create New Event'"></h3>
                    <p class="text-xs text-slate-400" x-text="isEdit ? 'Update contest timeline, weights, and rules' : 'Set up contest duration, scoring weights, and rules'"></p>
                </div>
                <button @click="modalOpen = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center font-bold text-sm">✕</button>
            </div>

            {{-- Form --}}
            <form :action="formAction" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Event Title <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        name="title"
                        x-model="form.title"
                        required
                        placeholder="e.g. Diwali Mega Referral Contest 2026"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-hidden"
                    >
                </div>

                {{-- Banner Image & Link URL --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Banner Image (App Header)</label>
                        <input
                            type="file"
                            name="banner_image"
                            accept="image/*"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
                        >
                        <p class="text-[10px] text-slate-400 mt-1">Recommended 1200x320px landscape format</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Banner Action Link URL (Optional)</label>
                        <input
                            type="text"
                            name="banner_link_url"
                            x-model="form.banner_link_url"
                            placeholder="Leave empty for default rules page"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-hidden"
                        >
                    </div>
                </div>

                {{-- Date Range --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Start Date & Time <span class="text-rose-500">*</span></label>
                        <input
                            type="datetime-local"
                            name="start_at"
                            x-model="form.start_at"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-hidden"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">End Date & Time <span class="text-rose-500">*</span></label>
                        <input
                            type="datetime-local"
                            name="end_at"
                            x-model="form.end_at"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-hidden"
                        >
                    </div>
                </div>

                {{-- Scoring Point Weights --}}
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Point Scoring Weights</label>
                        <span class="text-[11px] text-slate-400">Points awarded per action</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-amber-700 mb-1">👥 Referral</label>
                            <input
                                type="number"
                                step="any"
                                name="referrals_weight"
                                x-model="form.referrals_weight"
                                required
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-blue-700 mb-1">📝 Post</label>
                            <input
                                type="number"
                                step="any"
                                name="posts_weight"
                                x-model="form.posts_weight"
                                required
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-700 mb-1">💬 Comment</label>
                            <input
                                type="number"
                                step="any"
                                name="comments_weight"
                                x-model="form.comments_weight"
                                required
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-rose-700 mb-1">❤️ Like</label>
                            <input
                                type="number"
                                step="any"
                                name="likes_weight"
                                x-model="form.likes_weight"
                                required
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            >
                        </div>
                    </div>
                </div>

                {{-- Status & Sort Order --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status <span class="text-rose-500">*</span></label>
                        <select
                            name="status"
                            x-model="form.status"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-hidden bg-white"
                        >
                            <option value="draft">Draft (Hidden)</option>
                            <option value="active">Active (Visible / Live)</option>
                            <option value="inactive">Inactive (Disabled)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Priority / Sort Order</label>
                        <input
                            type="number"
                            name="sort_order"
                            x-model="form.sort_order"
                            placeholder="0"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-hidden"
                        >
                    </div>
                </div>

                {{-- Rules and Regulations --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Event Rules & Regulations (Displayed on redirect page)</label>
                    <textarea
                        name="rules"
                        x-model="form.rules"
                        rows="5"
                        placeholder="Write details about event eligibility, prizes, terms & conditions..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-hidden"
                    ></textarea>
                </div>

                {{-- Submit Buttons --}}
                <div class="pt-2 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button
                        type="button"
                        @click="modalOpen = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs shadow-xs transition active:scale-95"
                        x-text="isEdit ? 'Save Changes' : 'Create Event'"
                    ></button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function eventManagement() {
    return {
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('admin.events.store') }}',
        form: {
            id: null,
            title: '',
            banner_link_url: '',
            start_at: '',
            end_at: '',
            posts_weight: 5,
            comments_weight: 3,
            likes_weight: 2,
            referrals_weight: 10,
            status: 'active',
            sort_order: 0,
            rules: ''
        },

        openCreateModal() {
            this.isEdit = false;
            this.formAction = '{{ route('admin.events.store') }}';
            const now = new Date();
            const nextMonth = new Date();
            nextMonth.setDate(now.getDate() + 30);

            const formatForInput = (d) => {
                const pad = (n) => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            };

            this.form = {
                id: null,
                title: '',
                banner_link_url: '',
                start_at: formatForInput(now),
                end_at: formatForInput(nextMonth),
                posts_weight: 5,
                comments_weight: 3,
                likes_weight: 2,
                referrals_weight: 10,
                status: 'active',
                sort_order: 0,
                rules: "1. Eligible only for verified app community members.\n2. Points are counted only for activity performed within the contest duration.\n3. Top 5 participants at the end of the event will be declared winners.\n4. Any spamming or duplicate referrals will result in account disqualification."
            };
            this.modalOpen = true;
        },

        openEditModal(event) {
            this.isEdit = true;
            this.formAction = '{{ route('admin.events.index') }}/' + event.id;

            const formatIsoForInput = (isoString) => {
                if (!isoString) return '';
                const d = new Date(isoString);
                const pad = (n) => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            };

            this.form = {
                id: event.id,
                title: event.title || '',
                banner_link_url: event.banner_link_url || '',
                start_at: formatIsoForInput(event.start_at),
                end_at: formatIsoForInput(event.end_at),
                posts_weight: event.posts_weight ?? 5,
                comments_weight: event.comments_weight ?? 3,
                likes_weight: event.likes_weight ?? 2,
                referrals_weight: event.referrals_weight ?? 10,
                status: event.status || 'draft',
                sort_order: event.sort_order ?? 0,
                rules: event.rules || ''
            };
            this.modalOpen = true;
        }
    };
}
</script>
@endsection
