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
        .cert-print-card {
            border: 3px double #0b1329 !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
        }
        .cert-header-row {
            flex-direction: row !important;
            text-align: left !important;
            justify-content: space-between !important;
        }
        .cert-cand-row {
            flex-direction: row !important;
            text-align: left !important;
            justify-content: center !important;
        }
        .cert-stats-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }
        .cert-sigs-row {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
        }
    }
</style>

<div id="certificate-export-wrapper" class="cert-export-wrapper w-full box-border" style="background-color: #ffffff; box-sizing: border-box;">
<div id="certificate-print-card" class="cert-print-card bg-white border-2 sm:border-4 border-[#0b1329] p-1.5 sm:p-3 md:p-4 relative text-[#0b1329] w-full max-w-[950px] mx-auto box-border shadow-sm sm:shadow-md" style="background-color: #ffffff; color: #0b1329; box-sizing: border-box;">
    <div class="border sm:border-2 border-amber-500/70 p-3 sm:p-6 md:p-8 relative bg-gradient-to-b from-amber-50/20 via-white to-amber-50/10 box-border" style="position: relative; box-sizing: border-box;">

        {{-- CORNER ORNAMENTS --}}
        <div class="absolute top-1 left-1 sm:top-2 sm:left-2 w-3 h-3 sm:w-4 sm:h-4 border-t-2 border-l-2 border-amber-600"></div>
        <div class="absolute top-1 right-1 sm:top-2 sm:right-2 w-3 h-3 sm:w-4 sm:h-4 border-t-2 border-r-2 border-amber-600"></div>
        <div class="absolute bottom-1 left-1 sm:bottom-2 sm:left-2 w-3 h-3 sm:w-4 sm:h-4 border-b-2 border-l-2 border-amber-600"></div>
        <div class="absolute bottom-1 right-1 sm:bottom-2 sm:right-2 w-3 h-3 sm:w-4 sm:h-4 border-b-2 border-r-2 border-amber-600"></div>

        {{-- WATERMARK SEAL --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
            <svg class="w-44 h-44 sm:w-72 sm:h-72 md:w-96 md:h-96 text-[#0b1329]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>

        {{-- INSTITUTION & LOGO HEADER --}}
        <div class="cert-header-row flex flex-col sm:flex-row items-center sm:items-start justify-between gap-2.5 sm:gap-4 pb-3 sm:pb-4 border-b border-slate-200 relative z-10 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3.5 text-center sm:text-left">
                @if(file_exists(public_path('storage/logo.png')))
                    <img src="{{ asset('storage/logo.png') }}"
                         alt="{{ $test->agency_name ?: 'Logo' }}"
                         class="h-10 sm:h-12 md:h-14 w-auto max-w-[130px] object-contain shrink-0"
                         crossorigin="anonymous">
                @elseif($test->agency_logo && file_exists(public_path('storage/' . $test->agency_logo)))
                    <img src="{{ asset('storage/' . $test->agency_logo) }}"
                         alt="{{ $test->agency_name ?: 'Logo' }}"
                         class="h-10 sm:h-12 md:h-14 w-auto max-w-[130px] object-contain shrink-0"
                         crossorigin="anonymous">
                @else
                    <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-xl bg-[#0b1329] text-amber-400 flex items-center justify-center font-black text-lg sm:text-xl shadow-md shrink-0 border border-amber-400/40">
                        {{ strtoupper(substr($test->agency_name ?: 'B', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="text-sm sm:text-base md:text-lg font-black tracking-wider text-[#0b1329] uppercase leading-tight">
                        {{ $test->agency_name ?: 'REIAC TEST ASSESSMENT CENTER' }}
                    </div>
                    <div class="text-[9px] sm:text-[10px] md:text-[11px] font-bold tracking-widest text-slate-500 uppercase mt-0.5">
                        {{ $test->agency_name ? 'Authorized Examination & Assessment Portal' : 'Global Examination & Assessment Portal' }}
                    </div>
                </div>
            </div>

            <div class="text-center sm:text-right text-[10px] sm:text-[11px] font-medium text-slate-500 shrink-0 leading-relaxed border-t sm:border-t-0 pt-1.5 sm:pt-0 border-slate-100 w-full sm:w-auto">
                <div>Certificate Ref: <strong class="font-mono text-slate-900 font-bold">{{ $certNumber }}</strong></div>
                <div>Date of Issue: <strong class="text-slate-800 font-bold">{{ $issueDate }}</strong></div>
            </div>
        </div>

        {{-- CERTIFICATE TITLE & CANDIDATE DEDICATION --}}
        <div class="text-center my-3.5 sm:my-6 relative z-10">
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-amber-100/70 border border-amber-300 text-amber-900 text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1.5 sm:mb-2">
                ★ Official Academic Credential ★
            </div>

            <h1 class="text-xl sm:text-2xl md:text-3xl font-black uppercase tracking-wider text-[#0b1329] leading-tight">
                Certificate of Achievement
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium italic mt-1">
                This is to officially certify that
            </p>
    
            @php
                $showPhoto = !empty($test->require_camera_photo) || !empty($attempt->candidate_photo);
                $candidatePhotoTimestamp = @filemtime(public_path('storage/' . $attempt->candidate_photo)) ?: time();
            @endphp

            {{-- CANDIDATE NAME & PHOTO ROW --}}
            @if($showPhoto)
                <div class="cert-cand-row my-3 sm:my-4 flex flex-row items-center justify-center gap-3 sm:gap-5 text-left">
                    {{-- CANDIDATE VERIFIED PHOTO --}}
                    <div class="flex flex-col items-center shrink-0">
                        @if($attempt->candidate_photo)
                            <img src="{{ asset('storage/' . $attempt->candidate_photo) }}?v={{ $candidatePhotoTimestamp }}"
                                 alt="{{ $attempt->user->name ?? 'Candidate' }}"
                                 class="w-14 h-18 sm:w-20 sm:h-24 rounded-lg object-cover border-2 border-slate-300 shadow-sm bg-slate-100"
                                 style="image-orientation: from-image;"
                                 crossorigin="anonymous">
                        @elseif($attempt->user && $attempt->user->avatar)
                            <img src="{{ str_starts_with($attempt->user->avatar, 'http') ? $attempt->user->avatar : (asset('storage/' . $attempt->user->avatar) . '?v=' . (@filemtime(public_path('storage/' . $attempt->user->avatar)) ?: time())) }}"
                                 alt="{{ $attempt->user->name ?? 'Candidate' }}"
                                 class="w-14 h-18 sm:w-20 sm:h-24 rounded-lg object-cover border-2 border-slate-300 shadow-sm bg-slate-100"
                                 style="image-orientation: from-image;"
                                 crossorigin="anonymous">
                        @else
                            <div class="w-14 h-18 sm:w-20 sm:h-24 rounded-lg bg-slate-50 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-slate-300 mb-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                <span class="text-[7px] sm:text-[8px] font-bold uppercase tracking-wider">Photo</span>
                            </div>
                        @endif
                        <span class="text-[7px] sm:text-[8px] font-bold text-slate-400 uppercase tracking-wider mt-0.5 sm:mt-1">Candidate</span>
                    </div>

                    {{-- CANDIDATE NAME & ID (BESIDE PHOTO) --}}
                    <div class="text-left min-w-0">
                        <div class="inline-block border-b-2 border-[#0b1329] pb-0.5 sm:pb-1 pr-3 sm:pr-8 max-w-full">
                            <span class="text-lg sm:text-2xl md:text-3xl font-black text-[#0b1329] tracking-wide uppercase break-words leading-tight">
                                {{ $attempt->user->name ?? 'Candidate' }}
                            </span>
                        </div>
                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-500 mt-0.5 sm:mt-1">
                            Candidate ID: <span class="font-mono text-slate-800 font-bold">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</span>
                        </div>
                    </div>
                </div>
            @else
                {{-- CANDIDATE NAME & ID (CENTERED WHEN NO PHOTO) --}}
                <div class="my-3 sm:my-4 text-center">
                    <div class="inline-block border-b-2 border-[#0b1329] pb-1 px-4 sm:px-10 max-w-full">
                        <span class="text-xl sm:text-2xl md:text-3xl font-black text-[#0b1329] tracking-wide uppercase break-words leading-tight">
                            {{ $attempt->user->name ?? 'Candidate' }}
                        </span>
                    </div>
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-500 mt-1">
                        Candidate ID / Username: <span class="font-mono text-slate-800 font-bold">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</span>
                    </div>
                </div>
            @endif

            <p class="text-[10px] sm:text-xs text-slate-600 font-medium max-w-xl mx-auto leading-relaxed mt-1.5 sm:mt-2">
                has successfully appeared and completed the official examination for
            </p>

            {{-- TEST TITLE & LEVEL --}}
            <div class="mt-1 sm:mt-1.5">
                <div class="text-sm sm:text-lg md:text-xl font-black text-[#0b1329] tracking-tight leading-snug px-1">
                    {{ $test->title }}
                </div>
                <div class="inline-block px-2.5 sm:px-3 py-0.5 mt-1 rounded-md bg-slate-100 border border-slate-200 text-[9px] sm:text-[11px] font-bold text-slate-700">
                    {{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}
                </div>
            </div>
        </div>

        {{-- SCORE & PERFORMANCE CREDENTIALS --}}
        <div class="cert-stats-grid grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 my-3.5 sm:my-6 relative z-10">
            <div class="bg-white p-2 sm:p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                <div class="text-[8px] sm:text-[9px] font-bold uppercase tracking-wider text-slate-400">Score Obtained</div>
                <div class="text-base sm:text-2xl font-black text-[#0b1329] mt-0.5">
                    {{ $attempt->score_obtained }} <span class="text-[10px] sm:text-xs font-bold text-slate-400">/ {{ $test->total_marks }}</span>
                </div>
                <div class="text-[9px] sm:text-[10px] text-slate-500 font-semibold mt-0.5">Total Marks</div>
            </div>

            <div class="bg-white p-2 sm:p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                <div class="text-[8px] sm:text-[9px] font-bold uppercase tracking-wider text-slate-400">Percentage</div>
                <div class="text-base sm:text-2xl font-black {{ $isPass ? 'text-emerald-700' : 'text-slate-800' }} mt-0.5">
                    {{ $attempt->percentage }}%
                </div>
                <div class="text-[9px] sm:text-[10px] text-slate-500 font-semibold mt-0.5">Cutoff: {{ $passingPercentage }}%</div>
            </div>

            <div class="bg-white p-2 sm:p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                <div class="text-[8px] sm:text-[9px] font-bold uppercase tracking-wider text-slate-400">Accuracy</div>
                <div class="text-base sm:text-2xl font-black text-amber-600 mt-0.5">
                    {{ $accuracy }}%
                </div>
                <div class="text-[9px] sm:text-[10px] text-slate-500 font-semibold mt-0.5 truncate">{{ $attempt->correct_count }} of {{ $attempt->answered_count }} Correct</div>
            </div>

            <div class="bg-white p-2 sm:p-3 rounded-xl border {{ $isPass ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200' }} text-center shadow-2xs">
                <div class="text-[8px] sm:text-[9px] font-bold uppercase tracking-wider text-slate-400">Result Standing</div>
                <div class="text-sm sm:text-lg font-black {{ $isPass ? 'text-emerald-800' : 'text-rose-700' }} mt-0.5 truncate">
                    @if($matchedSlab)
                        {{ $matchedSlab->name }}
                    @elseif($isPass)
                        QUALIFIED (PASS)
                    @else
                        COMPLETED
                    @endif
                </div>
                <div class="text-[9px] sm:text-[10px] text-slate-500 font-semibold mt-0.5 truncate">
                    {{ $isPass ? 'Qualifying Met' : 'Completed' }}
                </div>
            </div>
        </div>

        {{-- SIGNATURES & OFFICIAL SECURITY SEAL --}}
        <div class="cert-sigs-row pt-3.5 sm:pt-6 border-t border-slate-200 relative z-10 flex items-end justify-between gap-1.5 sm:gap-4">
            {{-- CONTROLLER --}}
            <div class="text-left flex-1 min-w-0">
                <div class="font-serif italic text-xs sm:text-base md:text-lg font-bold text-slate-800 tracking-wider truncate">
                    {{ $test->controller_name ?: 'Kang Min-Seok' }}
                </div>
                <div class="w-20 sm:w-36 md:w-40 border-b border-slate-400 my-0.5 sm:my-1"></div>
                <div class="text-[8px] sm:text-[10px] md:text-[11px] font-extrabold text-slate-900 uppercase tracking-wide truncate">
                    Controller of Examinations
                </div>
                <div class="text-[7px] sm:text-[9px] text-slate-500 font-medium truncate">
                    {{ $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Examination Board' }}
                </div>
            </div>

            {{-- OFFICIAL GOLD SECURITY SEAL --}}
            <div class="flex flex-col items-center justify-center shrink-0 px-1">
                <div class="w-13 h-13 sm:w-18 sm:h-18 md:w-20 md:h-20 rounded-full border-2 sm:border-4 border-amber-400 bg-gradient-to-br from-amber-300 via-amber-400 to-amber-500 text-[#0b1329] p-0.5 sm:p-1 flex flex-col items-center justify-center shadow-md text-center select-none" style="width: 3.5rem; height: 3.5rem;">
                    <div class="w-full h-full rounded-full border border-amber-600/40 flex flex-col items-center justify-center p-0.5">
                        <span class="text-[6.5px] sm:text-[8px] font-black uppercase tracking-widest leading-none">{{ $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC TE' }}</span>
                        <span class="text-[9px] sm:text-xs my-0.5">★</span>
                        <span class="text-[5.5px] sm:text-[7px] font-black uppercase tracking-wider leading-none">VERIFIED</span>
                        <span class="text-[4.5px] sm:text-[6px] font-bold tracking-tighter opacity-80 mt-0.5">CERTIFICATE</span>
                    </div>
                </div>
                <span class="text-[7px] sm:text-[9px] font-bold font-mono text-slate-500 mt-0.5 sm:mt-1 truncate max-w-[80px] sm:max-w-none">{{ $certNumber }}</span>
            </div>

            {{-- ACADEMIC DIRECTOR --}}
            <div class="text-right flex-1 min-w-0">
                <div class="font-serif italic text-xs sm:text-base md:text-lg font-bold text-slate-800 tracking-wider truncate">
                    {{ $test->director_name ?: 'Dr. Rajesh Sharma' }}
                </div>
                <div class="w-20 sm:w-36 md:w-40 border-b border-slate-400 my-0.5 sm:my-1 ml-auto"></div>
                <div class="text-[8px] sm:text-[10px] md:text-[11px] font-extrabold text-slate-900 uppercase tracking-wide truncate">
                    Academic Director
                </div>
                <div class="text-[7px] sm:text-[9px] text-slate-500 font-medium truncate">
                    {{ $test->agency_name ? ($test->agency_name . ' Directorate') : 'REIAC Directorate' }}
                </div>
            </div>
        </div>

        {{-- VERIFICATION FOOTER NOTICE --}}
        <div class="mt-3.5 sm:mt-5 pt-2 sm:pt-3 border-t border-slate-200 text-center text-[7px] sm:text-[10px] text-slate-600 font-semibold leading-relaxed">
            This certificate is issued by {{ $test->agency_name ?: 'REIAC Test Assessment Center' }} for internal testing purposes only and is not an official score or certificate.
        </div>

    </div>
</div>
</div>
