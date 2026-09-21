@extends('layouts.admin')

@section('title', 'Profile Activities Management')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{
         triggers: {{ json_encode($availableTriggers) }},
         addModalOpen: false,
         editModalOpen: false,
         newItem: {
             key: '',
             title: '',
             description: '',
             points: 15,
             action_url: '',
             action_label: '',
             icon: 'camera',
             status: true,
             sort_order: {{ $activities->count() + 1 }}
         },
         editItem: {
             id: null,
             key: '',
             title: '',
             description: '',
             points: 20,
             action_url: '',
             action_label: '',
             status: true,
             sort_order: 0
         },
         onTriggerSelect(key) {
             if (this.triggers[key]) {
                 const t = this.triggers[key];
                 this.newItem.key = key;
                 this.newItem.title = t.title;
                 this.newItem.description = t.description;
                 this.newItem.points = t.default_points;
                 this.newItem.action_url = t.action_url;
                 this.newItem.action_label = t.action_label;
                 this.newItem.icon = t.icon;
             }
         },
         openEdit(act) {
             this.editItem = {
                 id: act.id,
                 key: act.key,
                 title: act.title,
                 description: act.description || '',
                 points: act.points,
                 action_url: act.action_url || '',
                 action_label: act.action_label || 'Complete',
                 status: Boolean(act.status),
                 sort_order: act.sort_order || 0
             };
             this.editModalOpen = true;
         }
     }">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-sm font-semibold shadow-xs">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">User Profile Activities & Progress Bar</h1>
            <p class="text-xs text-slate-500 mt-0.5">Admin can configure activities, automated action URLs, and percentage points that drive the user profile progress bar.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <span class="text-xs font-semibold px-3 py-2 rounded-xl border bg-white shadow-xs {{ $totalActivePoints === 100 ? 'text-emerald-700 border-emerald-200' : 'text-amber-700 border-amber-200' }}">
                Active Points: <strong class="font-black">{{ $totalActivePoints }}%</strong> / 100%
            </span>

            <button type="button"
                    @click="addModalOpen = true"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Activity</span>
            </button>
        </div>
    </div>

    {{-- STATS CARDS & LIVE PREVIEW --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Activities</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ $activeCount }} / {{ $activities->count() }}</div>
            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Tasks required for 100% profile strength</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Active Points</div>
            <div class="text-2xl font-extrabold mt-1.5 {{ $totalActivePoints === 100 ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $totalActivePoints }}%
            </div>
            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">
                {{ $totalActivePoints === 100 ? 'Optimal (Sum equals 100%)' : 'Recommended: adjust points to total 100%' }}
            </div>
        </div>

        {{-- Live Progress Bar Preview --}}
        <div class="bg-gradient-to-br from-slate-900 to-indigo-950 p-5 rounded-2xl border border-slate-800 text-white shadow-xs flex flex-col justify-center">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-300">Live User Preview</span>
                <span class="text-amber-400 font-black">60% Complete</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2.5 mt-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-400 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: 60%"></div>
            </div>
            <div class="text-[10px] text-slate-400 font-medium mt-2">
                Calculated automatically on user profiles based on active activities below.
            </div>
        </div>
    </div>

    {{-- ACTIVITIES TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">Order</th>
                        <th class="py-3.5 px-4">Activity Name & Target URL</th>
                        <th class="py-3.5 px-4 text-center">System Trigger</th>
                        <th class="py-3.5 px-4 text-center">Weight (%)</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($activities as $act)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                #{{ $act->sort_order }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold shrink-0">
                                        @if($act->key === 'avatar')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @elseif($act->key === 'bio')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        @elseif($act->key === 'location')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($act->key === 'email_verified')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @elseif($act->key === 'first_post')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        @elseif($act->key === 'join_group')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @elseif($act->key === 'first_test' || $act->key === 'pass_test')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                        @elseif($act->key === 'cover_image')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @elseif($act->key === 'like_post')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        @elseif($act->key === 'save_post')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                        @elseif($act->key === 'share_post')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                        @elseif($act->key === 'follow_user')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-900 truncate flex items-center gap-1.5">
                                            <span>{{ $act->title }}</span>
                                            <span class="text-[10px] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded font-mono font-bold">
                                                {{ $act->action_url }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-medium truncate max-w-md mt-0.5">
                                            {{ $act->description }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $act->key }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="text-sm font-black text-slate-900">
                                    +{{ $act->points }}%
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('admin.profile-activities.toggleStatus', $act) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="Click to toggle status"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition {{ $act->status ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        <span class="w-2 h-2 rounded-full {{ $act->status ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $act->status ? 'Active' : 'Disabled' }}</span>
                                    </button>
                                </form>
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button"
                                            @click="openEdit({{ $act->toJson() }})"
                                            class="px-3 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl border border-indigo-200 transition">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.profile-activities.destroy', $act) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this activity?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition"
                                                title="Delete Activity">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 text-sm">
                                No profile activities configured yet. Click "Add Activity" above to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- =========================================================================
         ADD ACTIVITY MODAL (WITH PRE-CONFIGURED ACTION DROPDOWN)
         ========================================================================= --}}
    <div x-show="addModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="addModalOpen = false">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-100"
             @click.away="addModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black text-slate-900">Add Profile Activity</h3>
                    <p class="text-xs text-slate-400 font-medium">Select an action type below. URL and validation will be auto-filled!</p>
                </div>
                <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
            </div>

            <form action="{{ route('admin.profile-activities.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Action Type Dropdown --}}
                <div class="bg-indigo-50/60 p-3.5 rounded-2xl border border-indigo-100">
                    <label class="block text-xs font-extrabold text-indigo-950 mb-1">
                        Select Action Type (Auto-configured Trigger)
                    </label>
                    <select @change="onTriggerSelect($event.target.value)"
                            class="w-full px-3.5 py-2.5 text-xs font-bold rounded-xl border border-indigo-200 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        <option value="">-- Choose an Action Trigger --</option>
                        <template x-for="(trig, key) in triggers" :key="key">
                            <option :value="key" x-text="trig.title + ' (Auto URL: ' + trig.action_url + ')'"></option>
                        </template>
                    </select>
                    <p class="text-[10px] text-indigo-700 font-medium mt-1">
                        Selecting an action automatically fills the verified URL, system key, and recommended points.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">System Trigger Key</label>
                    <input type="text" name="key" x-model="newItem.key" required readonly
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold bg-slate-50 rounded-xl border border-slate-200 text-slate-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Activity Title (Shown on User Profile)</label>
                    <input type="text" name="title" x-model="newItem.title" required
                           class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description / Instructions</label>
                    <textarea name="description" x-model="newItem.description" rows="2"
                              class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Points Weight (%)</label>
                        <input type="number" name="points" x-model="newItem.points" min="1" max="100" required
                               class="w-full px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="newItem.sort_order"
                               class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Action URL</label>
                        <input type="text" name="action_url" x-model="newItem.action_url"
                               class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Button Label</label>
                        <input type="text" name="action_label" x-model="newItem.action_label"
                               class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="status" id="add_status" value="1" x-model="newItem.status" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="add_status" class="text-xs font-bold text-slate-700">Activate this activity immediately</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition">
                        Save Activity
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================================
         EDIT ACTIVITY MODAL
         ========================================================================= --}}
    <div x-show="editModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="editModalOpen = false">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-100"
             @click.away="editModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black text-slate-900">Edit Profile Activity</h3>
                    <p class="text-xs text-slate-400 font-medium">Update points weightage, URL, and instructions for this task.</p>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
            </div>

            <form :action="'{{ route('admin.profile-activities.index') }}/' + editItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Activity Title</label>
                    <input type="text" name="title" x-model="editItem.title" required
                           class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description / Instructions</label>
                    <textarea name="description" x-model="editItem.description" rows="2"
                              class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Points Weight (%)</label>
                        <input type="number" name="points" x-model="editItem.points" min="1" max="100" required
                               class="w-full px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="editItem.sort_order"
                               class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Action Button URL</label>
                        <input type="text" name="action_url" x-model="editItem.action_url"
                               class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Action Button Label</label>
                        <input type="text" name="action_label" x-model="editItem.action_label"
                               class="w-full px-3.5 py-2 text-xs font-medium rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="status" id="modal_status" value="1" x-model="editItem.status" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="modal_status" class="text-xs font-bold text-slate-700">Enable this activity in progress calculation</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
