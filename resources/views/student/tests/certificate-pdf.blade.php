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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@700;900&family=Noto+Sans+KR:wght@400;700&display=swap');

        @page {
            size: A4 portrait;
            margin: 6mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', 'Noto Sans KR', 'DejaVu Sans', sans-serif;
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
            padding: 22px 26px;
            background-color: #fffefb;
            position: relative;
        }
        .corner-tl {
            position: absolute;
            top: 7px;
            left: 7px;
            width: 14px;
            height: 14px;
            border-top: 2px solid #d97706;
            border-left: 2px solid #d97706;
        }
        .corner-tr {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 14px;
            height: 14px;
            border-top: 2px solid #d97706;
            border-right: 2px solid #d97706;
        }

        /* HEADER TABLE */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
        }
        .agency-title {
            font-size: 16px;
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
            padding: 2px 14px;
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 900;
            color: #78350f;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 8px;
            margin-bottom: 6px;
        }
        .cert-title {
            font-size: 24px;
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
            padding-bottom: 4px;
            padding-left: 38px;
            padding-right: 38px;
            margin-top: 6px;
            margin-bottom: 3px;
        }
        .candidate-name {
            font-size: 25px;
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
            font-size: 18px;
            font-weight: 900;
            color: #0b1329;
            margin-top: 3px;
        }
        .level-badge {
            display: inline-block;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 3px 12px;
            border-radius: 6px;
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
            border-spacing: 10px 0;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            width: 25%;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 8px;
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
            font-size: 19px;
            font-weight: 900;
            color: #0b1329;
            margin: 2px 0;
            line-height: 1.1;
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
            margin-top: 22px;
            padding-top: 16px;
        }
        .sig-name {
            font-family: Georgia, serif;
            font-style: italic;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }
        .sig-line {
            width: 140px;
            border-bottom: 1px solid #94a3b8;
            margin: 4px 0;
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
        .seal-table {
            width: 66px;
            height: 66px;
            border-radius: 33px;
            border: 3px solid #fbbf24;
            background-color: #fbbf24;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .seal-cell {
            text-align: center;
            vertical-align: middle;
            padding: 2px;
        }
        .seal-org {
            font-size: 7px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1;
            color: #0b1329;
        }
        .seal-star {
            font-size: 10px;
            margin: 1px 0;
            line-height: 1;
            color: #0b1329;
        }
        .seal-verified {
            font-size: 6px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1;
            color: #0b1329;
        }
        .seal-cert {
            font-size: 5px;
            font-weight: 700;
            opacity: 0.85;
            line-height: 1;
            color: #0b1329;
        }
        .seal-ref {
            font-size: 8px;
            font-weight: 700;
            font-family: monospace;
            color: #64748b;
            margin-top: 3px;
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
        {{-- WATERMARK STAR --}}
        <div style="position: absolute; top: 140px; left: 0; width: 100%; text-align: center; font-size: 240px; color: #0b1329; opacity: 0.025; line-height: 1; z-index: 0; pointer-events: none;">
            &#9733;
        </div>

        {{-- CORNER ORNAMENTS --}}
        <div class="corner-tl"></div>
        <div class="corner-tr"></div>

        {{-- HEADER TABLE --}}
        <table class="header-table">
            <tr>
                <td style="width: 55px; vertical-align: middle;">
                    @if($attempt->candidate_photo && file_exists(public_path('storage/' . $attempt->candidate_photo)))
                        <img src="{{ public_path('storage/' . $attempt->candidate_photo) }}" style="width: 46px; height: 46px; border-radius: 8px; border: 2px solid #fbbf24; object-fit: cover;">
                    @else
                        <div style="width: 46px; height: 35px; padding-top: 11px; background-color: #0b1329; border: 1px solid rgba(251, 191, 36, 0.4); border-radius: 10px; text-align: center; color: #fbbf24; font-size: 22px; font-weight: 900; line-height: 1;">
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
            <div class="badge-pill">&#9733; Official Academic Credential &#9733;</div>
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
        <table class="stats-table" cellspacing="8" cellpadding="0">
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
                    <div style="width: 66px; height: 66px; border-radius: 33px; border: 3px solid #fbbf24; background-color: #fbbf24; margin: 0 auto; text-align: center;">
                        <div style="font-size: 7px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1; color: #0b1329; padding-top: 10px;">{{ $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC' }}</div>
                        <div style="font-size: 10px; margin: 2px 0; line-height: 1; color: #0b1329;">&#9733;</div>
                        <div style="font-size: 6px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1; color: #0b1329;">VERIFIED</div>
                        <div style="font-size: 5px; font-weight: 700; opacity: 0.85; line-height: 1; color: #0b1329;">CERTIFICATE</div>
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
            This is an official system-verified electronic certificate issued by {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}.
        </div>

        {{-- BOTTOM CORNER ORNAMENTS --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: -15px;">
            <tr>
                <td style="text-align: left; vertical-align: bottom; width: 50%; padding: 0;">
                    <div style="width: 14px; height: 14px; border-bottom: 2px solid #d97706; border-left: 2px solid #d97706; margin-left: -19px;"></div>
                </td>
                <td style="text-align: right; vertical-align: bottom; width: 50%; padding: 0;">
                    <div style="width: 14px; height: 14px; border-bottom: 2px solid #d97706; border-right: 2px solid #d97706; margin-left: auto; margin-right: -19px;"></div>
                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
