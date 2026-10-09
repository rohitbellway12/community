@php
    $isPass = isset($isPass) ? $isPass : ($attempt->result === 'pass');
    $passingPercentage = isset($passingPercentage) ? $passingPercentage : round(($test->passing_marks / max(1, $test->total_marks)) * 100);
    $accuracy = isset($accuracy) ? $accuracy : ($attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0);
    $matchedSlab = isset($matchedSlab) ? $matchedSlab : \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
    $certNumber = isset($certNumber) ? $certNumber : ('REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT));
    $issueDate = isset($issueDate) ? $issueDate : ($attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate - {{ $attempt->user->name ?? 'Candidate' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700;900&display=swap');

        @page {
            size: A4 portrait;
            margin: 6mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Noto Sans KR', 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #0b1329;
            font-size: 11px;
            line-height: 1.3;
        }
        .outer-border {
            border: 4px solid #0b1329;
            padding: 8px;
            background-color: #ffffff;
            position: relative;
        }
        .inner-border {
            border: 2px solid rgba(245, 158, 11, 0.7);
            padding: 20px 24px;
            background-color: #fffefb;
            position: relative;
        }
        .corner-tl {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 14px;
            height: 14px;
            border-top: 2px solid #d97706;
            border-left: 2px solid #d97706;
        }
        .corner-tr {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 14px;
            height: 14px;
            border-top: 2px solid #d97706;
            border-right: 2px solid #d97706;
        }
        .corner-bl {
            position: absolute;
            bottom: 6px;
            left: 6px;
            width: 14px;
            height: 14px;
            border-bottom: 2px solid #d97706;
            border-left: 2px solid #d97706;
        }
        .corner-br {
            position: absolute;
            bottom: 6px;
            right: 6px;
            width: 14px;
            height: 14px;
            border-bottom: 2px solid #d97706;
            border-right: 2px solid #d97706;
        }

        /* HEADER TABLE */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
        }
        .logo-box {
            width: 48px;
            height: 48px;
            background-color: #0b1329;
            color: #fbbf24;
            text-align: center;
            vertical-align: middle;
            font-size: 22px;
            font-weight: 900;
            border-radius: 8px;
            border: 1px solid #fbbf24;
            display: inline-block;
            line-height: 48px;
        }
        .agency-title {
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0b1329;
        }
        .agency-subtitle {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-top: 2px;
        }
        .ref-box {
            text-align: right;
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
        }

        /* CENTER CONTENT */
        .badge-pill {
            display: inline-block;
            padding: 3px 14px;
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 900;
            color: #78350f;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        .cert-title {
            font-size: 23px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0b1329;
            margin: 4px 0;
        }
        .cert-subtitle {
            font-size: 11px;
            font-style: italic;
            color: #64748b;
            margin: 3px 0;
        }
        .candidate-name-box {
            display: inline-block;
            border-bottom: 2px solid #0b1329;
            padding-bottom: 2px;
            padding-left: 25px;
            padding-right: 25px;
            margin-top: 6px;
            margin-bottom: 3px;
        }
        .candidate-name {
            font-size: 23px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0b1329;
        }
        .candidate-id {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 8px;
        }
        .appeared-for {
            font-size: 11px;
            color: #475569;
            margin: 4px 0;
        }
        .test-title {
            font-size: 17px;
            font-weight: 900;
            color: #0b1329;
            margin-top: 3px;
        }
        .level-badge {
            display: inline-block;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 2px 10px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
            margin-top: 4px;
            margin-bottom: 12px;
        }

        /* 4 STATS BOXES TABLE */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin: 10px 0;
        }
        .stat-box {
            width: 25%;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 6px;
            text-align: center;
        }
        .stat-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 900;
            color: #0b1329;
            margin: 2px 0;
        }
        .stat-sub {
            font-size: 9px;
            font-weight: 600;
            color: #64748b;
        }

        /* SIGNATURES TABLE */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #e2e8f0;
            margin-top: 12px;
            padding-top: 10px;
        }
        .sig-name {
            font-family: Georgia, serif;
            font-style: italic;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }
        .sig-line {
            width: 120px;
            border-bottom: 1px solid #94a3b8;
            margin: 3px 0;
        }
        .sig-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #0f172a;
        }
        .sig-org {
            font-size: 8px;
            color: #64748b;
            font-weight: 500;
        }

        /* SEAL BADGE */
        .seal-circle {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            border: 3px solid #fbbf24;
            background-color: #fbbf24;
            color: #0b1329;
            margin: 0 auto;
            text-align: center;
            padding-top: 8px;
        }
        .seal-org {
            font-size: 7px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .seal-star {
            font-size: 10px;
            margin: 1px 0;
            line-height: 1;
        }
        .seal-verified {
            font-size: 6px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .seal-cert {
            font-size: 5px;
            font-weight: 700;
            opacity: 0.85;
            line-height: 1;
        }
        .seal-ref {
            font-size: 8px;
            font-weight: 700;
            font-family: monospace;
            color: #64748b;
            margin-top: 2px;
            text-align: center;
        }

        .footer-notice {
            border-top: 1px solid #f1f5f9;
            margin-top: 10px;
            padding-top: 6px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="outer-border">
    <div class="inner-border">
        {{-- CORNER ORNAMENTS --}}
        <div class="corner-tl"></div>
        <div class="corner-tr"></div>
        <div class="corner-bl"></div>
        <div class="corner-br"></div>

        {{-- HEADER TABLE --}}
        <table class="header-table">
            <tr>
                <td style="width: 55px; vertical-align: middle;">
                    @if($attempt->candidate_photo && file_exists(public_path('storage/' . $attempt->candidate_photo)))
                        <img src="{{ public_path('storage/' . $attempt->candidate_photo) }}" style="width: 46px; height: 46px; border-radius: 8px; border: 2px solid #fbbf24; object-fit: cover;">
                    @else
                        <div class="logo-box">
                            {{ strtoupper(substr($attempt->user->name ?? ($test->agency_name ?? 'R'), 0, 1)) }}
                        </div>
                    @endif
                </td>
                <td style="vertical-align: middle; padding-left: 6px;">
                    <div class="agency-title">{{ $test->agency_name ?: 'REIAC Test Assessment Center' }}</div>
                    <div class="agency-subtitle">{{ $test->agency_name ? 'Authorized Examination & Assessment Portal' : 'Global Korean Language & Educational Proficiency' }}</div>
                </td>
                <td class="ref-box" style="vertical-align: middle;">
                    <div>Certificate Ref: <strong style="font-family: monospace; color: #0f172a;">{{ $certNumber }}</strong></div>
                    <div>Date of Issue: <strong style="color: #1e293b;">{{ $issueDate }}</strong></div>
                </td>
            </tr>
        </table>

        {{-- CENTER BODY --}}
        <div style="text-align: center; margin-top: 6px;">
            <div class="badge-pill">★ Official Academic Credential ★</div>
            <div class="cert-title">Certificate of Achievement</div>
            <div class="cert-subtitle">This is to officially certify that</div>

            <div class="candidate-name-box">
                <span class="candidate-name">{{ $attempt->user->name ?? 'Candidate' }}</span>
            </div>
            <div class="candidate-id">
                Candidate ID / Username: <span style="font-family: monospace; color: #1e293b;">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</span>
            </div>

            <div class="appeared-for">has successfully appeared and completed the official examination for</div>

            <div class="test-title">{{ $test->title }}</div>
            <div class="level-badge">
                {{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}
            </div>
        </div>

        {{-- 4 STATS BOXES TABLE (GUARANTEED 4 IN A ROW IN DOMPDF) --}}
        <table class="stats-table">
            <tr>
                <td class="stat-box">
                    <div class="stat-label">Score Obtained</div>
                    <div class="stat-value">{{ $attempt->score_obtained }} <span style="font-size: 10px; color: #94a3b8;">/ {{ $test->total_marks }}</span></div>
                    <div class="stat-sub">Total Marks</div>
                </td>
                <td class="stat-box">
                    <div class="stat-label">Percentage</div>
                    <div class="stat-value" style="color: {{ $isPass ? '#047857' : '#1e293b' }};">{{ $attempt->percentage }}%</div>
                    <div class="stat-sub">Cutoff: {{ $passingPercentage }}%</div>
                </td>
                <td class="stat-box">
                    <div class="stat-label">Accuracy</div>
                    <div class="stat-value" style="color: #d97706;">{{ $accuracy }}%</div>
                    <div class="stat-sub">{{ $attempt->correct_count }} of {{ $attempt->answered_count }} Correct</div>
                </td>
                <td class="stat-box" style="{{ $isPass ? 'border-color: #86efac; background-color: #f0fdf4;' : '' }}">
                    <div class="stat-label">Result Standing</div>
                    <div class="stat-value" style="font-size: 13px; color: {{ $isPass ? '#065f46' : '#be123c' }};">
                        @if($matchedSlab)
                            {{ $matchedSlab->name }}
                        @elseif($isPass)
                            QUALIFIED (PASS)
                        @else
                            COMPLETED
                        @endif
                    </div>
                    <div class="stat-sub">{{ $isPass ? 'Qualifying Standard Met' : 'Assessment Completed' }}</div>
                </td>
            </tr>
        </table>

        {{-- SIGNATURES & OFFICIAL SEAL TABLE --}}
        <table class="signatures-table">
            <tr>
                <td style="width: 35%; vertical-align: top; text-align: left;">
                    <div class="sig-name">{{ $test->controller_name ?: 'Kang Min-Seok' }}</div>
                    <div class="sig-line"></div>
                    <div class="sig-title">Controller of Examinations</div>
                    <div class="sig-org">{{ $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Assessment Council' }}</div>
                </td>
                <td style="width: 30%; vertical-align: middle; text-align: center;">
                    <div class="seal-circle">
                        <div class="seal-org">{{ $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC' }}</div>
                        <div class="seal-star">★</div>
                        <div class="seal-verified">VERIFIED</div>
                        <div class="seal-cert">CERTIFICATE</div>
                    </div>
                    <div class="seal-ref">{{ $certNumber }}</div>
                </td>
                <td style="width: 35%; vertical-align: top; text-align: right;">
                    <div class="sig-name">{{ $test->director_name ?: 'Dr. Rajesh Sharma' }}</div>
                    <div class="sig-line" style="margin-left: auto;"></div>
                    <div class="sig-title">Academic Director</div>
                    <div class="sig-org">{{ $test->agency_name ? ($test->agency_name . ' Directorate') : 'Global Education Board' }}</div>
                </td>
            </tr>
        </table>

        {{-- VERIFICATION NOTICE FOOTER --}}
        <div class="footer-notice">
            This is an official system-verified electronic certificate issued by {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}. Authenticity can be verified at: <span style="font-family: monospace; font-weight: 700; color: #475569;">{{ route('tests.student.certificate', ['test' => $test->id, 'attempt' => $attempt->id]) }}</span>
        </div>
    </div>
</div>

</body>
</html>
