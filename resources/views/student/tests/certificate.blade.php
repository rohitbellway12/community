<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $attempt->user->name ?? 'Candidate' }} - {{ $test->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm;
            }
            html, body {
                background: #ffffff !important;
                color: #0b1329 !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .cert-print-card {
                border: 3px double #0b1329 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 antialiased p-2 sm:p-6 lg:p-10 flex flex-col items-center justify-start">

    @php
        $isPass = $attempt->result === 'pass';
        $passingPercentage = round(($test->passing_marks / max(1, $test->total_marks)) * 100);
        $accuracy = $attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0;
        $matchedSlab = \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
        $certNumber = 'REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT);
        $issueDate = $attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y');
    @endphp

    {{-- TOP ACTION BAR (HIDDEN IN PRINT / PDF) --}}
    <div class="no-print max-w-[950px] w-full flex flex-col sm:flex-row items-center justify-between gap-3 mb-4 bg-white/95 backdrop-blur-md px-5 py-3.5 rounded-2xl border border-slate-200/90 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="text-xs font-bold text-slate-800">Official Verified Certificate (A4)</span>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('tests.student.certificate.download', ['test' => $test->id, 'attempt' => $attempt->id]) }}"
               download
               id="btn-direct-download"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Download Official PDF</span>
            </a>
            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-[#0b1329] font-black text-xs transition shadow-md hover:shadow-lg cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print / Save as PDF (Best Quality)</span>
            </button>
        </div>
    </div>

    {{-- CERTIFICATE CARD --}}
    <div id="certificate-card" class="max-w-[950px] w-full cert-print-card bg-white border-4 border-[#0b1329] p-3 sm:p-4 relative text-[#0b1329] shadow-xl rounded-xl">
        <div class="border-2 border-amber-500/70 p-6 sm:p-10 relative bg-gradient-to-b from-amber-50/25 via-white to-amber-50/15">

            {{-- CORNER ORNAMENTS --}}
            <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-amber-600"></div>
            <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-amber-600"></div>
            <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-amber-600"></div>
            <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-amber-600"></div>

            {{-- WATERMARK SEAL --}}
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                <svg class="w-96 h-96 text-[#0b1329]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>

            {{-- INSTITUTION & CANDIDATE IDENTITY HEADER --}}
            <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-200 relative z-10">
                <div class="flex items-center gap-3.5">
                    @if($attempt->candidate_photo)
                        <img src="{{ asset('storage/' . $attempt->candidate_photo) }}"
                             alt="{{ $attempt->user->name ?? 'Candidate' }}"
                             class="w-16 h-16 rounded-xl object-cover border-2 border-amber-400 shadow-sm shrink-0 bg-slate-100">
                    @elseif($attempt->user && $attempt->user->avatar)
                        <img src="{{ str_starts_with($attempt->user->avatar, 'http') ? $attempt->user->avatar : asset('storage/' . $attempt->user->avatar) }}"
                             alt="{{ $attempt->user->name ?? 'Candidate' }}"
                             class="w-16 h-16 rounded-xl object-cover border-2 border-amber-400 shadow-sm shrink-0 bg-slate-100">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-[#0b1329] text-amber-400 flex items-center justify-center font-black text-xl shadow-md shrink-0 border border-amber-400/40">
                            {{ strtoupper(substr($attempt->user->name ?? 'C', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="text-base sm:text-lg font-black tracking-wider text-[#0b1329] uppercase">
                            {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}
                        </div>
                        <div class="text-[10px] sm:text-[11px] font-bold tracking-widest text-slate-500 uppercase">
                            {{ $test->agency_name ? 'Authorized Examination & Assessment Portal' : 'Global Korean Language & Educational Proficiency' }}
                        </div>
                    </div>
                </div>

                <div class="text-right text-[10px] sm:text-[11px] font-medium text-slate-500 shrink-0">
                    <div>Certificate Ref: <strong class="font-mono text-slate-900 font-bold">{{ $certNumber }}</strong></div>
                    <div>Date of Issue: <strong class="text-slate-800 font-bold">{{ $issueDate }}</strong></div>
                </div>
            </div>

            {{-- CERTIFICATE TITLE & CANDIDATE DEDICATION --}}
            <div class="text-center my-6 relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-amber-100/70 border border-amber-300 text-amber-900 text-[10px] font-black uppercase tracking-widest mb-2">
                    ★ Official Academic Credential ★
                </div>

                <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wider text-[#0b1329]">
                    Certificate of Achievement
                </h1>
                <p class="text-xs text-slate-500 font-medium italic mt-1">
                    This is to officially certify that
                </p>

                {{-- CANDIDATE NAME --}}
                <div class="my-4">
                    <div class="inline-block border-b-2 border-[#0b1329] pb-1 px-8 sm:px-12">
                        <span class="text-2xl sm:text-3xl font-black text-[#0b1329] tracking-wide uppercase">
                            {{ $attempt->user->name ?? 'Candidate' }}
                        </span>
                    </div>
                    <div class="text-[11px] font-bold text-slate-500 mt-1">
                        Candidate ID / Username: <span class="font-mono text-slate-800">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 font-medium max-w-xl mx-auto leading-relaxed">
                    has successfully appeared and completed the official examination for
                </p>

                {{-- TEST TITLE & LEVEL --}}
                <div class="mt-1.5">
                    <div class="text-lg sm:text-xl font-black text-[#0b1329] tracking-tight">
                        {{ $test->title }}
                    </div>
                    <div class="inline-block px-3 py-0.5 mt-1 rounded-md bg-slate-100 border border-slate-200 text-[11px] font-bold text-slate-700">
                        {{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}
                    </div>
                </div>
            </div>

            {{-- SCORE & PERFORMANCE CREDENTIALS --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 my-6 relative z-10">
                <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Score Obtained</div>
                    <div class="text-xl sm:text-2xl font-black text-[#0b1329] mt-0.5">
                        {{ $attempt->score_obtained }} <span class="text-xs font-bold text-slate-400">/ {{ $test->total_marks }}</span>
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Total Marks</div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Percentage</div>
                    <div class="text-xl sm:text-2xl font-black {{ $isPass ? 'text-emerald-700' : 'text-slate-800' }} mt-0.5">
                        {{ $attempt->percentage }}%
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Cutoff: {{ $passingPercentage }}%</div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Accuracy</div>
                    <div class="text-xl sm:text-2xl font-black text-amber-600 mt-0.5">
                        {{ $accuracy }}%
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ $attempt->correct_count }} of {{ $attempt->answered_count }} Correct</div>
                </div>

                <div class="bg-white p-3 rounded-xl border {{ $isPass ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200' }} text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Result Standing</div>
                    <div class="text-base sm:text-lg font-black {{ $isPass ? 'text-emerald-800' : 'text-rose-700' }} mt-0.5 truncate">
                        @if($matchedSlab)
                            {{ $matchedSlab->name }}
                        @elseif($isPass)
                            QUALIFIED (PASS)
                        @else
                            COMPLETED
                        @endif
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">
                        {{ $isPass ? 'Qualifying Standard Met' : 'Assessment Completed' }}
                    </div>
                </div>
            </div>

            {{-- SIGNATURES & OFFICIAL SECURITY SEAL --}}
            <div class="pt-6 border-t border-slate-200 relative z-10 flex items-center justify-between gap-4">
                <div class="text-left">
                    <div class="font-serif italic text-base sm:text-lg font-bold text-slate-800 tracking-wider">
                        {{ $test->controller_name ?: 'Kang Min-Seok' }}
                    </div>
                    <div class="w-32 sm:w-40 border-b border-slate-400 my-1"></div>
                    <div class="text-[10px] sm:text-[11px] font-extrabold text-slate-900 uppercase tracking-wide">
                        Controller of Examinations
                    </div>
                    <div class="text-[9px] text-slate-500 font-medium">
                        {{ $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Assessment Council' }}
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-amber-400 bg-gradient-to-br from-amber-300 via-amber-400 to-amber-500 text-[#0b1329] p-1 flex flex-col items-center justify-center shadow-md text-center select-none">
                        <div class="w-full h-full rounded-full border border-amber-600/40 flex flex-col items-center justify-center p-1">
                            <span class="text-[7px] sm:text-[8px] font-black uppercase tracking-widest leading-none">{{ $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC' }}</span>
                            <span class="text-xs my-0.5">★</span>
                            <span class="text-[6px] sm:text-[7px] font-black uppercase tracking-wider leading-none">VERIFIED</span>
                            <span class="text-[5px] sm:text-[6px] font-bold tracking-tighter opacity-80 mt-0.5">CERTIFICATE</span>
                        </div>
                    </div>
                    <span class="text-[8px] sm:text-[9px] font-bold font-mono text-slate-500 mt-1">{{ $certNumber }}</span>
                </div>

                <div class="text-right">
                    <div class="font-serif italic text-base sm:text-lg font-bold text-slate-800 tracking-wider">
                        {{ $test->director_name ?: 'Dr. Rajesh Sharma' }}
                    </div>
                    <div class="w-32 sm:w-40 border-b border-slate-400 my-1 ml-auto"></div>
                    <div class="text-[10px] sm:text-[11px] font-extrabold text-slate-900 uppercase tracking-wide">
                        Academic Director
                    </div>
                    <div class="text-[9px] text-slate-500 font-medium">
                        {{ $test->agency_name ? ($test->agency_name . ' Directorate') : 'Global Education Board' }}
                    </div>
                </div>
            </div>

            {{-- VERIFICATION FOOTER NOTICE --}}
            <div class="mt-5 pt-3 border-t border-slate-100 text-center text-[9px] text-slate-400 font-medium">
                This is an official system-verified electronic certificate issued by {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}.
                Authenticity can be verified at: <span class="font-mono text-slate-600 font-bold">{{ route('tests.student.certificate', ['test' => $test->id, 'attempt' => $attempt->id]) }}</span>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadCertificatePdf() {
            if (typeof html2pdf !== 'undefined') {
                const btn = document.getElementById('btn-direct-download');
                const orig = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.innerHTML = '<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Generating PDF...</span>';
                    btn.disabled = true;
                }

                const element = document.getElementById('certificate-card');
                const opt = {
                    margin:       [4, 4, 4, 4],
                    filename:     'Certificate_{{ preg_replace("/[^A-Za-z0-9_\-]/", "_", $attempt->user->name ?? "Candidate") }}_{{ $attempt->id }}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true, logging: false },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(element).save().then(() => {
                    if (btn) {
                        btn.innerHTML = orig;
                        btn.disabled = false;
                    }
                }).catch(() => {
                    window.print();
                });
            } else {
                window.print();
            }
        }
    </script>

    @if(request()->has('autoprint'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => window.print(), 700);
            });
        </script>
    @endif
</body>
</html>
