@php
    $isPass = isset($isPass) ? $isPass : ($attempt->result === 'pass');
    $passingPercentage = isset($passingPercentage) ? $passingPercentage : round(($test->passing_marks / max(1, $test->total_marks)) * 100);
    $accuracy = isset($accuracy) ? $accuracy : ($attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0);
    $matchedSlab = isset($matchedSlab) ? $matchedSlab : \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
    $certNumber = isset($certNumber) ? $certNumber : ('REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT));
    $issueDate = isset($issueDate) ? $issueDate : ($attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y'));
@endphp

<style>
    @media print {
        .cert-export-wrapper {
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
<div id="certificate-export-wrapper" class="cert-export-wrapper w-full box-border" style="padding: 6px; background-color: #ffffff; box-sizing: border-box;">
<div id="certificate-print-card" class="cert-print-card bg-white border-4 border-[#0b1329] p-3 sm:p-4 relative text-[#0b1329] w-full max-w-[950px] mx-auto box-border shadow-none" style="background-color: #ffffff; color: #0b1329; border: 4px solid #0b1329; box-sizing: border-box;">
    <div class="border-2 border-amber-500/70 p-6 sm:p-8 relative bg-gradient-to-b from-amber-50/20 via-white to-amber-50/10 box-border" style="border: 2px solid rgba(245, 158, 11, 0.7); position: relative;">

        {{-- CORNER ORNAMENTS --}}
        <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-amber-600" style="position: absolute; top: 8px; left: 8px; width: 16px; height: 16px; border-top: 2px solid #d97706; border-left: 2px solid #d97706;"></div>
        <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-amber-600" style="position: absolute; top: 8px; right: 8px; width: 16px; height: 16px; border-top: 2px solid #d97706; border-right: 2px solid #d97706;"></div>
        <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-amber-600" style="position: absolute; bottom: 8px; left: 8px; width: 16px; height: 16px; border-bottom: 2px solid #d97706; border-left: 2px solid #d97706;"></div>
        <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-amber-600" style="position: absolute; bottom: 8px; right: 8px; width: 16px; height: 16px; border-bottom: 2px solid #d97706; border-right: 2px solid #d97706;"></div>

        {{-- WATERMARK SEAL --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none" style="position: absolute; inset: 0; display: flex; align-items: center; justify-center: center; opacity: 0.03; pointer-events: none;">
            <svg class="w-96 h-96 text-[#0b1329]" fill="currentColor" viewBox="0 0 24 24" style="width: 24rem; height: 24rem; color: #0b1329;">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>

        {{-- INSTITUTION & CANDIDATE IDENTITY HEADER --}}
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-200 relative z-10" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; position: relative; z-index: 10;">
            <div class="flex items-center gap-3.5" style="display: flex; align-items: center; gap: 0.875rem;">
                @if($attempt->candidate_photo)
                    <img src="{{ asset('storage/' . $attempt->candidate_photo) }}"
                         alt="{{ $attempt->user->name ?? 'Candidate' }}"
                         class="w-16 h-16 rounded-xl object-cover border-2 border-amber-400 shadow-sm shrink-0 bg-slate-100"
                         style="width: 4rem; height: 4rem; border-radius: 0.75rem; object-fit: cover; border: 2px solid #fbbf24; flex-shrink: 0;"
                         crossorigin="anonymous">
                @elseif($attempt->user && $attempt->user->avatar)
                    <img src="{{ str_starts_with($attempt->user->avatar, 'http') ? $attempt->user->avatar : asset('storage/' . $attempt->user->avatar) }}"
                         alt="{{ $attempt->user->name ?? 'Candidate' }}"
                         class="w-16 h-16 rounded-xl object-cover border-2 border-amber-400 shadow-sm shrink-0 bg-slate-100"
                         style="width: 4rem; height: 4rem; border-radius: 0.75rem; object-fit: cover; border: 2px solid #fbbf24; flex-shrink: 0;"
                         crossorigin="anonymous">
                @else
                    <div class="w-14 h-14 rounded-xl bg-[#0b1329] text-amber-400 flex items-center justify-center font-black text-xl shadow-md shrink-0 border border-amber-400/40" style="width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; background-color: #0b1329; color: #fbbf24; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.25rem; flex-shrink: 0; border: 1px solid rgba(251, 191, 36, 0.4);">
                        {{ strtoupper(substr($attempt->user->name ?? 'C', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="text-lg font-black tracking-wider text-[#0b1329] uppercase" style="font-size: 1.125rem; font-weight: 900; letter-spacing: 0.05em; color: #0b1329; text-transform: uppercase;">
                        {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}
                    </div>
                    <div class="text-[11px] font-bold tracking-widest text-slate-500 uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #64748b; text-transform: uppercase;">
                        {{ $test->agency_name ? 'Authorized Examination & Assessment Portal' : 'Global Korean Language & Educational Proficiency' }}
                    </div>
                </div>
            </div>

            <div class="text-right text-[11px] font-medium text-slate-500 shrink-0" style="text-align: right; font-size: 11px; color: #64748b; flex-shrink: 0; line-height: 1.5;">
                <div>Certificate Ref: <strong class="font-mono text-slate-900 font-bold" style="font-family: monospace; color: #0f172a;">{{ $certNumber }}</strong></div>
                <div>Date of Issue: <strong class="text-slate-800 font-bold" style="color: #1e293b;">{{ $issueDate }}</strong></div>
            </div>
        </div>

        {{-- CERTIFICATE TITLE & CANDIDATE DEDICATION --}}
        <div class="text-center my-6 relative z-10" style="text-align: center; margin-top: 1.5rem; margin-bottom: 1.5rem; position: relative; z-index: 10;">
            <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-amber-100/70 border border-amber-300 text-amber-900 text-[10px] font-black uppercase tracking-widest mb-2" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 2px 12px; border-radius: 9999px; background-color: rgba(254, 243, 199, 0.7); border: 1px solid #fcd34d; color: #78350f; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
                ★ Official Academic Credential ★
            </div>

            <h1 class="text-3xl font-black uppercase tracking-wider text-[#0b1329]" style="font-size: 1.875rem; line-height: 2.25rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #0b1329; margin: 0;">
                Certificate of Achievement
            </h1>
            <p class="text-xs text-slate-500 font-medium italic mt-1" style="font-size: 0.75rem; color: #64748b; font-style: italic; margin-top: 0.25rem;">
                This is to officially certify that
            </p>

            {{-- CANDIDATE NAME --}}
            <div class="my-4" style="margin-top: 1rem; margin-bottom: 1rem;">
                <div class="inline-block border-b-2 border-[#0b1329] pb-1 px-10" style="display: inline-block; border-bottom: 2px solid #0b1329; padding-bottom: 0.25rem; padding-left: 2.5rem; padding-right: 2.5rem;">
                    <span class="text-3xl font-black text-[#0b1329] tracking-wide uppercase" style="font-size: 1.875rem; font-weight: 900; color: #0b1329; letter-spacing: 0.025em; text-transform: uppercase;">
                        {{ $attempt->user->name ?? 'Candidate' }}
                    </span>
                </div>
                <div class="text-[11px] font-bold text-slate-500 mt-1" style="font-size: 11px; font-weight: 700; color: #64748b; margin-top: 0.25rem;">
                    Candidate ID / Username: <span class="font-mono text-slate-800" style="font-family: monospace; color: #1e293b;">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</span>
                </div>
            </div>

            <p class="text-xs text-slate-600 font-medium max-w-xl mx-auto leading-relaxed" style="font-size: 0.75rem; color: #475569; max-width: 36rem; margin-left: auto; margin-right: auto; line-height: 1.625;">
                has successfully appeared and completed the official examination for
            </p>

            {{-- TEST TITLE & LEVEL --}}
            <div class="mt-1.5" style="margin-top: 0.375rem;">
                <div class="text-xl font-black text-[#0b1329] tracking-tight" style="font-size: 1.25rem; font-weight: 900; color: #0b1329; letter-spacing: -0.025em;">
                    {{ $test->title }}
                </div>
                <div class="inline-block px-3 py-0.5 mt-1 rounded-md bg-slate-100 border border-slate-200 text-[11px] font-bold text-slate-700" style="display: inline-block; padding: 2px 12px; margin-top: 0.25rem; border-radius: 0.375rem; background-color: #f1f5f9; border: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #334155;">
                    {{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}
                </div>
            </div>
        </div>

        {{-- SCORE & PERFORMANCE CREDENTIALS --}}
        <div class="grid grid-cols-4 gap-3 my-6 relative z-10" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.75rem; margin-top: 1.5rem; margin-bottom: 1.5rem; position: relative; z-index: 10;">
            <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs" style="background-color: #ffffff; padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; text-align: center;">
                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400" style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8;">Score Obtained</div>
                <div class="text-2xl font-black text-[#0b1329] mt-0.5" style="font-size: 1.5rem; font-weight: 900; color: #0b1329; margin-top: 0.125rem;">
                    {{ $attempt->score_obtained }} <span class="text-xs font-bold text-slate-400" style="font-size: 0.75rem; font-weight: 700; color: #94a3b8;">/ {{ $test->total_marks }}</span>
                </div>
                <div class="text-[10px] text-slate-500 font-semibold mt-0.5" style="font-size: 10px; color: #64748b; font-weight: 600; margin-top: 0.125rem;">Total Marks</div>
            </div>

            <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs" style="background-color: #ffffff; padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; text-align: center;">
                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400" style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8;">Percentage</div>
                <div class="text-2xl font-black {{ $isPass ? 'text-emerald-700' : 'text-slate-800' }} mt-0.5" style="font-size: 1.5rem; font-weight: 900; color: {{ $isPass ? '#047857' : '#1e293b' }}; margin-top: 0.125rem;">
                    {{ $attempt->percentage }}%
                </div>
                <div class="text-[10px] text-slate-500 font-semibold mt-0.5" style="font-size: 10px; color: #64748b; font-weight: 600; margin-top: 0.125rem;">Cutoff: {{ $passingPercentage }}%</div>
            </div>

            <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs" style="background-color: #ffffff; padding: 0.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; text-align: center;">
                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400" style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8;">Accuracy</div>
                <div class="text-2xl font-black text-amber-600 mt-0.5" style="font-size: 1.5rem; font-weight: 900; color: #d97706; margin-top: 0.125rem;">
                    {{ $accuracy }}%
                </div>
                <div class="text-[10px] text-slate-500 font-semibold mt-0.5" style="font-size: 10px; color: #64748b; font-weight: 600; margin-top: 0.125rem;">{{ $attempt->correct_count }} of {{ $attempt->answered_count }} Correct</div>
            </div>

            <div class="bg-white p-3 rounded-xl border {{ $isPass ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200' }} text-center shadow-2xs" style="background-color: #ffffff; padding: 0.75rem; border-radius: 0.75rem; border: 1px solid {{ $isPass ? '#86efac' : '#e2e8f0' }}; text-align: center;">
                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400" style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8;">Result Standing</div>
                <div class="text-lg font-black {{ $isPass ? 'text-emerald-800' : 'text-rose-700' }} mt-0.5 truncate" style="font-size: 1.125rem; font-weight: 900; color: {{ $isPass ? '#065f46' : '#be123c' }}; margin-top: 0.125rem;">
                    @if($matchedSlab)
                        {{ $matchedSlab->name }}
                    @elseif($isPass)
                        QUALIFIED (PASS)
                    @else
                        COMPLETED
                    @endif
                </div>
                <div class="text-[10px] text-slate-500 font-semibold mt-0.5" style="font-size: 10px; color: #64748b; font-weight: 600; margin-top: 0.125rem;">
                    {{ $isPass ? 'Qualifying Standard Met' : 'Assessment Completed' }}
                </div>
            </div>
        </div>

        {{-- SIGNATURES & OFFICIAL SECURITY SEAL --}}
        <div class="pt-6 border-t border-slate-200 relative z-10 flex items-center justify-between gap-4" style="padding-top: 1.5rem; border-top: 1px solid #e2e8f0; position: relative; z-index: 10; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <div class="text-left" style="text-align: left;">
                <div class="font-serif italic text-lg font-bold text-slate-800 tracking-wider" style="font-family: Georgia, serif; font-style: italic; font-size: 1.125rem; font-weight: 700; color: #1e293b; letter-spacing: 0.05em;">
                    {{ $test->controller_name ?: 'Kang Min-Seok' }}
                </div>
                <div class="w-40 border-b border-slate-400 my-1" style="width: 10rem; border-bottom: 1px solid #94a3b8; margin-top: 0.25rem; margin-bottom: 0.25rem;"></div>
                <div class="text-[11px] font-extrabold text-slate-900 uppercase tracking-wide" style="font-size: 11px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.025em;">
                    Controller of Examinations
                </div>
                <div class="text-[9px] text-slate-500 font-medium" style="font-size: 9px; color: #64748b; font-weight: 500;">
                    {{ $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Assessment Council' }}
                </div>
            </div>

            <div class="flex flex-col items-center justify-center shrink-0" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                <div class="w-20 h-20 rounded-full border-4 border-amber-400 bg-gradient-to-br from-amber-300 via-amber-400 to-amber-500 text-[#0b1329] p-1 flex flex-col items-center justify-center shadow-md text-center select-none" style="width: 5rem; height: 5rem; border-radius: 9999px; border: 4px solid #fbbf24; background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 50%, #f59e0b 100%); color: #0b1329; padding: 4px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; user-select: none;">
                    <div class="w-full h-full rounded-full border border-amber-600/40 flex flex-col items-center justify-center p-1" style="width: 100%; height: 100%; border-radius: 9999px; border: 1px solid rgba(217, 119, 6, 0.4); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4px;">
                        <span class="text-[8px] font-black uppercase tracking-widest leading-none" style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; line-height: 1;">{{ $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC' }}</span>
                        <span class="text-xs my-0.5" style="font-size: 12px; margin-top: 2px; margin-bottom: 2px;">★</span>
                        <span class="text-[7px] font-black uppercase tracking-wider leading-none" style="font-size: 7px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1;">VERIFIED</span>
                        <span class="text-[6px] font-bold tracking-tighter opacity-80 mt-0.5" style="font-size: 6px; font-weight: 700; opacity: 0.8; margin-top: 2px;">CERTIFICATE</span>
                    </div>
                </div>
                <span class="text-[9px] font-bold font-mono text-slate-500 mt-1" style="font-size: 9px; font-weight: 700; font-family: monospace; color: #64748b; margin-top: 0.25rem;">{{ $certNumber }}</span>
            </div>

            <div class="text-right" style="text-align: right;">
                <div class="font-serif italic text-lg font-bold text-slate-800 tracking-wider" style="font-family: Georgia, serif; font-style: italic; font-size: 1.125rem; font-weight: 700; color: #1e293b; letter-spacing: 0.05em;">
                    {{ $test->director_name ?: 'Dr. Rajesh Sharma' }}
                </div>
                <div class="w-40 border-b border-slate-400 my-1 ml-auto" style="width: 10rem; border-bottom: 1px solid #94a3b8; margin-top: 0.25rem; margin-bottom: 0.25rem; margin-left: auto;"></div>
                <div class="text-[11px] font-extrabold text-slate-900 uppercase tracking-wide" style="font-size: 11px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.025em;">
                    Academic Director
                </div>
                <div class="text-[9px] text-slate-500 font-medium" style="font-size: 9px; color: #64748b; font-weight: 500;">
                    {{ $test->agency_name ? ($test->agency_name . ' Directorate') : 'Global Education Board' }}
                </div>
            </div>
        </div>

        {{-- VERIFICATION FOOTER NOTICE --}}
        <div class="mt-5 pt-3 border-t border-slate-100 text-center text-[9px] text-slate-400 font-medium" style="margin-top: 1.25rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; text-align: center; font-size: 9px; color: #94a3b8; font-weight: 500;">
            This is an official system-verified electronic certificate issued by {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}.
            Authenticity can be verified at: <span class="font-mono text-slate-600 font-bold" style="font-family: monospace; color: #475569; font-weight: 700;">{{ route('tests.student.certificate', ['test' => $test->id, 'attempt' => $attempt->id]) }}</span>
        </div>

    </div>
</div>
</div>
