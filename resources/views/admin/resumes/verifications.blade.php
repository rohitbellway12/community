@extends('layouts.admin')

@section('title', 'Social Reshare Verifications')

@section('content')
<div class="w-full space-y-6" x-data="{
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
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-center justify-between text-emerald-800 text-xs font-medium">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.resumes.index') }}" class="hover:text-slate-900 transition flex items-center gap-1 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    <span>Resumes</span>
                </a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">Verifications Queue</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Social Reshare Verifications</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review submitted student proof to approve watermark removal or reject invalid reshares.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.resumes.settings') }}"
               class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition flex items-center gap-2 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Watermark Settings</span>
            </a>
        </div>
    </div>

    {{-- STATUS TABS --}}
    <div class="border-b border-slate-200 flex items-center gap-1">
        <a href="{{ route('admin.resumes.verifications', ['status' => 'pending']) }}"
           class="px-5 py-3 text-xs transition flex items-center gap-2 -mb-px
                  {{ $status === 'pending' ? 'border-b-2 border-slate-900 text-slate-900 font-bold' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium' }}">
            <span>Pending Review</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] {{ $status === 'pending' ? 'bg-amber-100 text-amber-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                {{ $counts['pending'] }}
            </span>
        </a>

        <a href="{{ route('admin.resumes.verifications', ['status' => 'approved']) }}"
           class="px-5 py-3 text-xs transition flex items-center gap-2 -mb-px
                  {{ $status === 'approved' ? 'border-b-2 border-slate-900 text-slate-900 font-bold' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium' }}">
            <span>Approved</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                {{ $counts['approved'] }}
            </span>
        </a>

        <a href="{{ route('admin.resumes.verifications', ['status' => 'rejected']) }}"
           class="px-5 py-3 text-xs transition flex items-center gap-2 -mb-px
                  {{ $status === 'rejected' ? 'border-b-2 border-slate-900 text-slate-900 font-bold' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium' }}">
            <span>Rejected</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] {{ $status === 'rejected' ? 'bg-rose-100 text-rose-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                {{ $counts['rejected'] }}
            </span>
        </a>
    </div>

    {{-- VERIFICATIONS LIST TABLE --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Resume</th>
                        <th class="py-3 px-4">Platform</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4">Proof</th>
                        <th class="py-3 px-4">Submitted</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($verifications as $v)
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- Student --}}
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $v->user->name ?? 'Unknown' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $v->user->email ?? 'N/A' }}</div>
                            </td>

                            {{-- Resume --}}
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800">{{ $v->resume->title ?? 'Untitled' }}</div>
                                <div class="text-[11px] text-slate-400">ID: #{{ $v->resume_id }}</div>
                            </td>

                            {{-- Platform --}}
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px] uppercase">
                                    {{ $v->platform }}
                                </span>
                            </td>

                            {{-- Method --}}
                            <td class="py-3 px-4">
                                @if($v->verification_method === 'instant')
                                    <span class="text-slate-600 font-medium text-[11px]">Instant Share</span>
                                @else
                                    <span class="text-slate-600 font-medium text-[11px]">Screenshot Upload</span>
                                @endif
                            </td>

                            {{-- Proof File / Link --}}
                            <td class="py-3 px-4">
                                @php $proofUrls = $v->proof_urls; @endphp
                                @if(!empty($proofUrls))
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @foreach($proofUrls as $idx => $url)
                                            <button type="button" @click="openProof('{{ $url }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-md font-medium text-[11px] transition cursor-pointer shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Proof {{ count($proofUrls) > 1 ? '#' . ($idx + 1) : '' }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif($v->post_url)
                                    <a href="{{ $v->post_url }}" target="_blank" class="text-slate-700 hover:text-slate-900 underline text-[11px] font-medium inline-flex items-center gap-1">
                                        <span>Link</span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px]">Instant Share</span>
                                @endif
                            </td>

                            {{-- Submitted Date --}}
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                {{ $v->created_at->format('M d, Y h:i A') }}
                            </td>

                            {{-- Status --}}
                            <td class="py-3 px-4">
                                @if($v->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Approved
                                    </span>
                                    @if($v->reviewer)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">by {{ $v->reviewer->name }}</span>
                                    @endif
                                @elseif($v->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Rejected
                                    </span>
                                    @if($v->rejection_reason)
                                        <span class="text-[10px] text-slate-500 block mt-0.5 truncate max-w-[160px]" title="{{ $v->rejection_reason }}">
                                            {{ $v->rejection_reason }}
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 px-4 text-right">
                                @if($v->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.resumes.verifications.approve', $v->id) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve this verification and remove the watermark?')"
                                                    class="px-2.5 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs transition cursor-pointer shadow-2xs">
                                                Approve
                                            </button>
                                        </form>

                                        <button type="button" @click="openReject('{{ route('admin.resumes.verifications.reject', $v->id) }}')"
                                                class="px-2.5 py-1 rounded-md border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 text-slate-700 font-medium text-xs transition cursor-pointer">
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">Reviewed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="font-semibold text-xs text-slate-700">No verifications found</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">When students submit screenshots or share their CV, submissions will appear here.</p>
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
    <div x-show="proofModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-5 shadow-xl border border-slate-200 flex flex-col max-h-[90vh]" @click.away="proofModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900">Student Reshare Proof</h3>
                <button @click="proofModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="my-4 overflow-auto rounded-lg border border-slate-200 bg-slate-950 flex items-center justify-center min-h-[300px]">
                <img :src="proofImageUrl" alt="Proof Screenshot" class="max-h-[60vh] object-contain">
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" @click="proofModalOpen = false" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- REJECT REASON MODAL --}}
    <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-xl border border-slate-200" @click.away="rejectModalOpen = false">
            <h3 class="font-bold text-sm text-slate-900">Reject Reshare Verification</h3>
            <p class="text-xs text-slate-500 mt-1">Specify why this proof was rejected (e.g. invalid screenshot, hashtag missing, or inaccessible link).</p>

            <form :action="rejectFormAction" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rejection Reason <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" required
                              placeholder="e.g. The uploaded screenshot does not show the platform link or hashtag."
                              class="w-full text-xs border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:outline-none focus:border-slate-400 transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-1">
                    <button type="button" @click="rejectModalOpen = false"
                            class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-medium text-xs hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs transition">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
