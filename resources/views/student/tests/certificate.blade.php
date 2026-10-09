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
            <button type="button"
               id="btn-direct-download"
               onclick="downloadCertificatePdf(this)"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Download Official PDF</span>
            </button>
            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-[#0b1329] font-black text-xs transition shadow-md hover:shadow-lg cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print / Save as PDF (Best Quality)</span>
            </button>
        </div>
    </div>

    {{-- REUSABLE CERTIFICATE CARD --}}
    <div class="max-w-[950px] w-full flex justify-center">
        @include('student.tests.partials.certificate-card')
    </div>

    <script src="{{ asset('html2pdf.bundle.min.js') }}"></script>
    <script>
        if (typeof html2pdf === 'undefined') {
            const cdnScript = document.createElement('script');
            cdnScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            document.head.appendChild(cdnScript);
        }

        function downloadCertificatePdf(btn) {
            btn = btn || document.getElementById('btn-direct-download');
            const orig = btn ? btn.innerHTML : '';
            if (btn) {
                btn.innerHTML = '<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Generating PDF...</span>';
                btn.disabled = true;
            }

            const element = document.getElementById('certificate-export-wrapper') || document.getElementById('certificate-print-card') || document.getElementById('certificate-card');
            if (!element) {
                if (btn) { btn.innerHTML = orig; btn.disabled = false; }
                window.print();
                return;
            }

            const opt = {
                margin:       [4, 4, 4, 4],
                filename:     'Certificate_{{ preg_replace("/[^A-Za-z0-9_\-]/", "_", $attempt->user->name ?? "Candidate") }}_{{ $attempt->id }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    logging: false,
                    backgroundColor: '#ffffff',
                    scrollX: 0,
                    scrollY: 0
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            const executeExport = () => {
                html2pdf().set(opt).from(element).save().then(() => {
                    if (btn) {
                        btn.innerHTML = orig;
                        btn.disabled = false;
                    }
                }).catch((err) => {
                    console.error('PDF generation error, falling back to print:', err);
                    if (btn) {
                        btn.innerHTML = orig;
                        btn.disabled = false;
                    }
                    window.print();
                });
            };

            if (typeof html2pdf !== 'undefined') {
                executeExport();
            } else {
                setTimeout(() => {
                    if (typeof html2pdf !== 'undefined') {
                        executeExport();
                    } else {
                        if (btn) { btn.innerHTML = orig; btn.disabled = false; }
                        window.print();
                    }
                }, 500);
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

    @if(request()->has('autodownload') || request()->has('download'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    const btn = document.getElementById('btn-direct-download');
                    downloadCertificatePdf(btn);
                }, 400);
            });
        </script>
    @endif
</body>
</html>
