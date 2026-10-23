@extends('layouts.admin')

@section('title', 'Resume Share Verifications Queue')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="{
    proofModalOpen: false,
    proofImageUrl: '',
    rejectModalOpen: false,
    rejectFormAction: '',
    openProof(url) {
        this.proofImageUrl = url;
        this.proofModalOpen = true;
    },
    openReject(action) {
        this.rejectFormAction = action;
        this.rejectModalOpen = true;
    }
}">

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

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.resumes.index') }}" class="text-xs font-bold text-slate-400 hover:text-teal-600 transition flex items-center gap-1">
                    <span>←</span> Back to Resumes
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-teal-600 uppercase tracking-wider">Moderation</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Social Reshare Verifications</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review student share screenshots to verify guidelines compliance and approve watermark removal.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.resumes.settings') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition">
                Configure Guidelines &amp; Mode
            </a>
        </div>
    </div>

    {{-- TABS --}}
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 text-xs font-bold">
        <a href="{{ route('admin.resumes.verifications', ['status' => 'pending']) }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2
                  {{ $status === 'pending' ? 'bg-amber-500 text-slate-950 shadow-xs font-black' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>⏳ Pending Review</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'pending' ? 'bg-slate-950 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $counts['pending'] }}
            </span>
        </a>

        <a href="{{ route('admin.resumes.verifications', ['status' => 'approved']) }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2
                  {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>✓ Approved</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'approved' ? 'bg-emerald-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $counts['approved'] }}
            </span>
        </a>

        <a href="{{ route('admin.resumes.verifications', ['status' => 'rejected']) }}"
           class="px-4 py-2 rounded-xl transition flex items-center gap-2
                  {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-xs font-black' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>✕ Rejected</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'rejected' ? 'bg-rose-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $counts['rejected'] }}
            </span>
        </a>
    </div>

    {{-- VERIFICATIONS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Student</th>
                        <th class="py-3.5 px-4">Resume</th>
                        <th class="py-3.5 px-4">Platform</th>
                        <th class="py-3.5 px-4">Method</th>
                        <th class="py-3.5 px-4">Proof / Screenshot</th>
                        <th class="py-3.5 px-4">Submitted At</th>
                        <th class="py-3.5 px-4">Status / Reviewer</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($verifications as $v)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-extrabold flex items-center justify-center text-xs">
                                        {{ substr($v->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $v->user->name ?? 'Unknown' }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $v->user->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $v->resume->title ?? 'Untitled Resume' }}</span>
                                <span class="text-[11px] text-slate-400 block">ID: #{{ $v->resume_id }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700">
                                    {{ $v->platform }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                @if($v->verification_method === 'instant')
                                    <span class="text-[11px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                                        ⚡ Instant Share
                                    </span>
                                @else
                                    <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                                        📸 Screenshot Proof
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if($v->proof_image)
                                    <button type="button" @click="openProof('{{ asset('storage/' . $v->proof_image) }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-bold text-[11px] transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>View Screenshot</span>
                                    </button>
                                @elseif($v->post_url)
                                    <a href="{{ $v->post_url }}" target="_blank" class="text-blue-600 hover:underline text-[11px] font-bold">
                                        Open Post Link ↗
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">No proof file (Instant)</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $v->created_at->format('M d, Y h:i A') }}
                            </td>

                            <td class="py-3.5 px-4">
                                @if($v->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Approved
                                    </span>
                                    @if($v->reviewer)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">by {{ $v->reviewer->name }}</span>
                                    @endif
                                @elseif($v->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                        ✕ Rejected
                                    </span>
                                    @if($v->rejection_reason)
                                        <span class="text-[10px] text-rose-500 block mt-0.5 italic max-w-xs truncate" title="{{ $v->rejection_reason }}">
                                            "{{ $v->rejection_reason }}"
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        ⏳ Pending Review
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                @if($v->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.resumes.verifications.approve', $v->id) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve this verification and remove the watermark?')"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[11px] transition shadow-xs cursor-pointer">
                                                Approve
                                            </button>
                                        </form>

                                        <button type="button" @click="openReject('{{ route('admin.resumes.verifications.reject', $v->id) }}')"
                                                class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition cursor-pointer">
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="text-4xl mb-2">✨</div>
                                <p class="font-bold text-sm text-slate-600">No verifications in this list</p>
                                <p class="text-xs text-slate-400 mt-0.5">When students submit screenshots or share their CV, requests will show up here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($verifications->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $verifications->links() }}
            </div>
        @endif
    </div>

    {{-- SCREENSHOT PROOF LIGHTBOX MODAL --}}
    <div x-show="proofModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 flex flex-col max-h-[90vh]" @click.away="proofModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-black text-sm text-slate-900">Student Reshare Proof Screenshot</h3>
                <button @click="proofModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>
            </div>
            <div class="my-4 overflow-auto rounded-xl border border-slate-200 bg-slate-900 flex items-center justify-center min-h-[300px]">
                <img :src="proofImageUrl" alt="Proof Screenshot" class="max-h-[60vh] object-contain">
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" @click="proofModalOpen = false" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- REJECT REASON MODAL --}}
    <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100" @click.away="rejectModalOpen = false">
            <h3 class="font-black text-base text-slate-900">Reject Reshare Verification</h3>
            <p class="text-xs text-slate-500 mt-1">Specify why this proof was rejected (e.g. invalid screenshot, hashtag missing, deleted post).</p>

            <form :action="rejectFormAction" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Rejection Reason *</label>
                    <textarea name="rejection_reason" rows="3" required
                              placeholder="e.g. The uploaded screenshot does not show the #REIACCommunity hashtag or platform link."
                              class="w-full text-xs border border-slate-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="rejectModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-xs transition">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
