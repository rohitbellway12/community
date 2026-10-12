@php
    $isPass = isset($isPass) ? $isPass : ($attempt->result === 'pass');
    $passingPercentage = isset($passingPercentage) ? $passingPercentage : round(($test->passing_marks / max(1, $test->total_marks)) * 100);
    $accuracy = isset($accuracy) ? $accuracy : ($attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0);
    $matchedSlab = isset($matchedSlab) ? $matchedSlab : \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
    $certNumber = isset($certNumber) ? $certNumber : ('REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT));
    $issueDate = isset($issueDate) ? $issueDate : ($attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y'));

    $agencyName = $test->agency_name ?: 'REIAC TEST ASSESSMENT CENTER';
    $agencySubtitle = $test->agency_name ? 'Authorized Examination & Assessment Portal' : 'Global Examination & Assessment Portal';
    $sealOrg = $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC TE';
    $controllerOrg = $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Test Assessment Center Board';
    $directorOrg = $test->agency_name ? ($test->agency_name . ' Directorate') : 'REIAC Test Assessment Center Directorate';

    $findFont = function($filename) {
        $paths = [
            public_path('fonts/' . $filename),
            base_path('public/fonts/' . $filename),
            base_path('public_html/fonts/' . $filename),
            storage_path('fonts/' . $filename),
        ];
        foreach ($paths as $p) {
            if (file_exists($p)) {
                return str_replace('\\', '/', $p);
            }
        }
        return str_replace('\\', '/', public_path('fonts/' . $filename));
    };

    $fontRegular = $findFont('NanumGothic-Regular.ttf');
    $fontBold = $findFont('NanumGothic-Bold.ttf');
    $fontExtraBold = $findFont('NanumGothic-ExtraBold.ttf');

    $hasLogo = file_exists(public_path('storage/logo.png'));
    $logoPath = $hasLogo ? str_replace('\\', '/', public_path('storage/logo.png')) : null;

    $hasCandidatePhoto = $attempt->candidate_photo && file_exists(public_path('storage/' . $attempt->candidate_photo));
    $candidatePhotoPath = $hasCandidatePhoto ? str_replace('\\', '/', public_path('storage/' . $attempt->candidate_photo)) : null;

    if (!$candidatePhotoPath && $attempt->user && $attempt->user->avatar && file_exists(public_path('storage/' . $attempt->user->avatar))) {
        $candidatePhotoPath = str_replace('\\', '/', public_path('storage/' . $attempt->user->avatar));
        $hasCandidatePhoto = true;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate - {{ $attempt->user->name ?? 'Candidate' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
        }

        @font-face {
            font-family: 'KoreanFont';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $fontRegular }}') format('truetype');
        }
        @font-face {
            font-family: 'KoreanFont';
            font-style: normal;
            font-weight: 700;
            src: url('{{ $fontBold }}') format('truetype');
        }
        @font-face {
            font-family: 'KoreanFont';
            font-style: normal;
            font-weight: 900;
            src: url('{{ $fontExtraBold }}') format('truetype');
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'KoreanFont', 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #0b1329;
            font-size: 11px;
            line-height: 1.3;
        }

        .outer-frame {
            border: 4px solid #0b1329;
            padding: 6px;
            background-color: #ffffff;
            position: relative;
        }

        .inner-frame {
            border: 2px solid rgba(245, 158, 11, 0.7);
            padding: 22px 26px 18px 26px;
            background-color: #ffffff;
            position: relative;
        }

        /* WATERMARK STAR */
        .watermark-star {
            position: absolute;
            top: 140px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 260px;
            color: #0b1329;
            opacity: 0.03;
            line-height: 1;
            z-index: 0;
            pointer-events: none;
        }

        /* CORNER L-ORNAMENTS */
        .corner-tl {
            position: absolute;
            top: 7px;
            left: 7px;
            width: 16px;
            height: 16px;
            border-top: 2px solid #d97706;
            border-left: 2px solid #d97706;
        }
        .corner-tr {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 16px;
            height: 16px;
            border-top: 2px solid #d97706;
            border-right: 2px solid #d97706;
        }
        .corner-bl {
            position: absolute;
            bottom: 7px;
            left: 7px;
            width: 16px;
            height: 16px;
            border-bottom: 2px solid #d97706;
            border-left: 2px solid #d97706;
        }
        .corner-br {
            position: absolute;
            bottom: 7px;
            right: 7px;
            width: 16px;
            height: 16px;
            border-bottom: 2px solid #d97706;
            border-right: 2px solid #d97706;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
            position: relative;
            z-index: 10;
        }

        .agency-name {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.05em;
            color: #0b1329;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .agency-sub {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .ref-box {
            text-align: right;
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
            font-weight: 500;
        }

        /* CREDENTIAL PILL & TITLE */
        .badge-pill {
            display: inline-block;
            padding: 2px 14px;
            border-radius: 9999px;
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            color: #78350f;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 6px;
        }

        .cert-title {
            font-size: 26px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0b1329;
            margin: 0;
            line-height: 1.2;
        }

        .cert-subtitle {
            font-size: 10.5px;
            color: #64748b;
            font-style: italic;
            margin-top: 3px;
            margin-bottom: 10px;
        }

        /* CANDIDATE NAME & PHOTO TABLE */
        .candidate-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            position: relative;
            z-index: 10;
        }

        .candidate-name-container {
            display: inline-block;
            border-bottom: 2px solid #0b1329;
            padding-bottom: 4px;
            padding-left: 28px;
            padding-right: 28px;
        }

        .candidate-name {
            font-size: 26px;
            font-weight: 900;
            color: #0b1329;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .candidate-id {
            font-size: 10.5px;
            font-weight: 700;
            color: #64748b;
            margin-top: 4px;
        }

        /* EXAM TITLE & BADGE */
        .appeared-text {
            font-size: 11px;
            color: #475569;
            margin-bottom: 4px;
        }

        .test-title {
            font-size: 19px;
            font-weight: 900;
            color: #0b1329;
            letter-spacing: -0.025em;
            margin-top: 2px;
        }

        .level-pill {
            display: inline-block;
            padding: 2.5px 14px;
            margin-top: 4px;
            border-radius: 6px;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
        }

        /* 4 STATS BOXES ROW */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-top: 14px;
            margin-bottom: 16px;
            position: relative;
            z-index: 10;
        }

        .stat-card {
            width: 25%;
            background-color: #ffffff;
            padding: 10px 6px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .stat-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }

        .stat-val {
            font-size: 20px;
            font-weight: 900;
            color: #0b1329;
            margin-top: 2px;
            line-height: 1.1;
        }

        .stat-sub {
            font-size: 9px;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        /* SIGNATURES & OFFICIAL SEAL */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            margin-top: 14px;
            position: relative;
            z-index: 10;
        }

        .sig-name {
            font-family: Georgia, serif;
            font-style: italic;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.05em;
        }

        .sig-line {
            width: 135px;
            border-bottom: 1px solid #94a3b8;
            margin: 4px 0;
        }

        .sig-title {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .sig-org {
            font-size: 8.5px;
            color: #64748b;
            font-weight: 500;
        }

        /* GOLD SEAL */
        .seal-circle {
            width: 68px;
            height: 68px;
            border-radius: 34px;
            border: 3px solid #fbbf24;
            background-color: #fbbf24;
            margin: 0 auto;
            text-align: center;
            padding-top: 8px;
            box-sizing: border-box;
        }

        .seal-org {
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1;
            color: #0b1329;
        }

        .seal-star {
            font-size: 10px;
            line-height: 1;
            margin: 2px 0;
            color: #0b1329;
        }

        .seal-verified {
            font-size: 6.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1;
            color: #0b1329;
        }

        .seal-cert {
            font-size: 5.5px;
            font-weight: 700;
            opacity: 0.85;
            line-height: 1;
            color: #0b1329;
            margin-top: 1px;
        }

        .seal-ref {
            font-size: 8.5px;
            font-weight: 700;
            font-family: monospace;
            color: #64748b;
            margin-top: 3px;
        }

        /* FOOTER NOTICE */
        .footer-notice {
            border-top: 1px solid #f1f5f9;
            margin-top: 14px;
            padding-top: 8px;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            font-weight: 500;
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>

<div class="outer-frame">
    <div class="inner-frame">

        {{-- WATERMARK STAR --}}
        <div class="watermark-star">&#9733;</div>

        {{-- CORNER ORNAMENTS --}}
        <div class="corner-tl"></div>
        <div class="corner-tr"></div>
        <div class="corner-bl"></div>
        <div class="corner-br"></div>

        {{-- INSTITUTION & LOGO HEADER TABLE --}}
        <table class="header-table">
            <tr>
                <td style="width: 120px; vertical-align: middle;">
                    @if($hasLogo)
                        <img src="{{ $logoPath }}" style="max-height: 48px; max-width: 120px; object-fit: contain;">
                    @elseif($test->agency_logo && file_exists(public_path('storage/' . $test->agency_logo)))
                        <img src="{{ str_replace('\\', '/', public_path('storage/' . $test->agency_logo)) }}" style="max-height: 48px; max-width: 120px; object-fit: contain;">
                    @else
                        <div style="width: 48px; height: 36px; padding-top: 12px; background-color: #0b1329; border: 1px solid rgba(251, 191, 36, 0.4); border-radius: 10px; text-align: center; color: #fbbf24; font-size: 20px; font-weight: 900; line-height: 1;">
                            {{ strtoupper(substr($agencyName, 0, 1)) }}
                        </div>
                    @endif
                </td>
                <td style="vertical-align: middle; padding-left: 8px;">
                    <div class="agency-name">{{ $agencyName }}</div>
                    <div class="agency-sub">{{ $agencySubtitle }}</div>
                </td>
                <td class="ref-box" style="width: 220px; vertical-align: middle;">
                    <div>Certificate Ref: <strong style="font-family: monospace; color: #0f172a;">{{ $certNumber }}</strong></div>
                    <div>Date of Issue: <strong style="color: #1e293b;">{{ $issueDate }}</strong></div>
                </td>
            </tr>
        </table>

        {{-- CENTER BODY: CERTIFICATE DEDICATION --}}
        <div style="text-align: center; margin-top: 10px;">
            <div class="badge-pill">&#9733; Official Academic Credential &#9733;</div>
            <h1 class="cert-title">Certificate of Achievement</h1>
            <div class="cert-subtitle">This is to officially certify that</div>

            @php
                $showPhoto = !empty($test->require_camera_photo) || !empty($attempt->candidate_photo);
            @endphp

            @if($showPhoto)
                {{-- CANDIDATE NAME & PHOTO ROW TABLE --}}
                <table class="candidate-table">
                    <tr>
                        <td style="width: 85px; vertical-align: middle; text-align: left;">
                            @if($hasCandidatePhoto)
                                <img src="{{ $candidatePhotoPath }}" style="width: 76px; height: 95px; border-radius: 8px; border: 2px solid #cbd5e1; object-fit: cover;">
                            @else
                                <div style="width: 76px; height: 95px; border-radius: 8px; background-color: #f8fafc; border: 2px dashed #cbd5e1; text-align: center; padding-top: 20px; box-sizing: border-box;">
                                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='%23cbd5e1'><path d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/></svg>" width="28" height="28" style="display: block; margin: 0 auto;"/>
                                    <div style="font-size: 8px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 6px;">Photo</div>
                                </div>
                            @endif
                            <div style="font-size: 7px; font-weight: 700; color: #94a3b8; letter-spacing: 0.05em; text-transform: uppercase; text-align: center; width: 76px; margin-top: 3px;">Candidate Photo</div>
                        </td>
                        <td style="text-align: center; vertical-align: middle; padding-right: 85px;">
                            <div class="candidate-name-container">
                                <span class="candidate-name">{{ $attempt->user->name ?? 'Candidate' }}</span>
                            </div>
                            <div class="candidate-id">
                                Candidate ID / Username: <strong style="font-family: monospace; color: #1e293b;">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            @else
                <div style="text-align: center; margin: 12px 0;">
                    <div class="candidate-name-container">
                        <span class="candidate-name">{{ $attempt->user->name ?? 'Candidate' }}</span>
                    </div>
                    <div class="candidate-id">
                        Candidate ID / Username: <strong style="font-family: monospace; color: #1e293b;">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</strong>
                    </div>
                </div>
            @endif

            {{-- EXAMINATION DETAILS --}}
            <div style="margin-top: 6px;">
                <div class="appeared-text">has successfully appeared and completed the official examination for</div>
                <div class="test-title">{{ $test->title }}</div>
                <div class="level-pill">
                    {{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}
                </div>
            </div>
        </div>

        {{-- 4 METRIC CARDS ROW --}}
        <table class="stats-table">
            <tr>
                <td class="stat-card">
                    <div class="stat-label">Score Obtained</div>
                    <div class="stat-val">{{ $attempt->score_obtained }} <span style="font-size: 10px; font-weight: 700; color: #94a3b8;">/ {{ $test->total_marks }}</span></div>
                    <div class="stat-sub">Total Marks</div>
                </td>
                <td class="stat-card">
                    <div class="stat-label">Percentage</div>
                    <div class="stat-val" style="color: {{ $isPass ? '#047857' : '#1e293b' }};">{{ $attempt->percentage }}%</div>
                    <div class="stat-sub">Cutoff: {{ $passingPercentage }}%</div>
                </td>
                <td class="stat-card">
                    <div class="stat-label">Accuracy</div>
                    <div class="stat-val" style="color: #d97706;">{{ $accuracy }}%</div>
                    <div class="stat-sub">{{ $attempt->correct_count }} of {{ $attempt->answered_count }} Correct</div>
                </td>
                <td class="stat-card" style="{{ $isPass ? 'border-color: #86efac; background-color: #f0fdf4;' : '' }}">
                    <div class="stat-label">Result Standing</div>
                    <div class="stat-val" style="font-size: 14px; color: {{ $isPass ? '#065f46' : '#be123c' }}; margin-top: 4px;">
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

        {{-- SIGNATURES & SECURITY SEAL TABLE --}}
        <table class="sig-table">
            <tr>
                <td style="width: 36%; vertical-align: top; text-align: left;">
                    <div class="sig-name">{{ $test->controller_name ?: 'Kang Min-Seok' }}</div>
                    <div class="sig-line"></div>
                    <div class="sig-title">Controller of Examinations</div>
                    <div class="sig-org">{{ $controllerOrg }}</div>
                </td>
                <td style="width: 28%; vertical-align: middle; text-align: center;">
                    <div class="seal-circle">
                        <div class="seal-org">{{ $sealOrg }}</div>
                        <div class="seal-star">&#9733;</div>
                        <div class="seal-verified">VERIFIED</div>
                        <div class="seal-cert">CERTIFICATE</div>
                    </div>
                    <div class="seal-ref">{{ $certNumber }}</div>
                </td>
                <td style="width: 36%; vertical-align: top; text-align: right;">
                    <div class="sig-name">{{ $test->director_name ?: 'Dr. Rajesh Sharma' }}</div>
                    <div class="sig-line" style="margin-left: auto;"></div>
                    <div class="sig-title">Academic Director</div>
                    <div class="sig-org">{{ $directorOrg }}</div>
                </td>
            </tr>
        </table>

        {{-- VERIFICATION FOOTER NOTICE --}}
        <div class="footer-notice">
            This is an official system-verified electronic certificate issued by {{ $agencyName }}.
        </div>

    </div>
</div>

</body>
</html>
