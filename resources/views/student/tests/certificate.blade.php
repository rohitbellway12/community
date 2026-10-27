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
        $pdfDownloadUrl = route('tests.student.certificate.download', ['test' => $test->id, 'attempt' => $attempt->id]);
    @endphp

    {{-- TOP ACTION BAR (HIDDEN IN PRINT) --}}
    <div class="no-print max-w-[950px] w-full flex flex-col sm:flex-row items-center justify-between gap-3 mb-4 bg-white/95 backdrop-blur-md p-3 sm:px-5 sm:py-3.5 rounded-2xl border border-slate-200/90 shadow-sm">
        <div class="flex items-center justify-between w-full sm:w-auto gap-3">
            <a href="{{ route('tests.student.result', ['test' => $test->id, 'attempt' => $attempt->id]) }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Scorecard</span>
            </a>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-xs font-bold text-slate-800">Verified Certificate</span>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto justify-center sm:justify-end">
            @if(!empty($attempt->candidate_photo) && auth()->check() && (auth()->id() === $attempt->user_id || (auth()->user()->is_admin ?? false)))
                <form method="POST" action="{{ route('tests.student.certificate.rotate-photo', ['test' => $test->id, 'attempt' => $attempt->id]) }}" class="inline">
                    @csrf
                    <button type="submit"
                            title="Click if candidate photo is sideways or upside down to rotate it 90 degrees"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-300 shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Rotate Photo ↻</span>
                    </button>
                </form>
            @endif

            <a href="{{ $pdfDownloadUrl }}"
               id="btn-direct-download"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-[#0b1329] font-black text-xs transition shadow-md hover:shadow-lg">
                <svg class="w-3.5 h-3.5 text-[#0b1329]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Download PDF</span>
            </a>
        </div>
    </div>

    <div class="max-w-[950px] w-full flex justify-center">
        @include('student.tests.partials.certificate-card')
    </div>

    @if(request()->has('autoprint'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => window.print(), 700);
            });
        </script>
    @endif

    @if(request()->has('autodownload') || request()->has('download'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.location.href = @json($pdfDownloadUrl);
            });
        </script>
    @endif
</body>
</html>
