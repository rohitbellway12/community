<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Print - {{ $attempt->user->name ?? 'Candidate' }}</title>
    {{-- Same visual engine as website page --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            color: #0b1329;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        #certificate-export-wrapper {
            padding: 0 !important;
            margin: 0 !important;
            max-width: none !important;
            width: 100% !important;
        }
        #certificate-print-card {
            max-width: none !important;
            width: 100% !important;
            margin: 0 !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body>
    @php
        $isPass = $attempt->result === 'pass';
        $passingPercentage = round(($test->passing_marks / max(1, $test->total_marks)) * 100);
        $accuracy = $attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0;
        $matchedSlab = \App\Models\ResultSlab::getSlabForScore((float) $attempt->score_obtained, $attempt->test_id);
        $certNumber = 'REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT);
        $issueDate = $attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y');
    @endphp

    {{-- EXACT same markup as website certificate --}}
    @include('student.tests.partials.certificate-card')
</body>
</html>
