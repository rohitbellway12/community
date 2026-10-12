@php
    $isPass = isset($isPass) ? $isPass : ($attempt->result === 'pass');
    $passingPercentage = isset($passingPercentage) ? $passingPercentage : round(($test->passing_marks / max(1, $test->total_marks)) * 100);
    $accuracy = isset($accuracy) ? $accuracy : ($attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0);
    $matchedSlab = isset($matchedSlab) ? $matchedSlab : \App\Models\ResultSlab::getSlabForScore((float) $attempt->score_obtained, $attempt->test_id);
    $certNumber = isset($certNumber) ? $certNumber : ('REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT));
    $issueDate = isset($issueDate) ? $issueDate : ($attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y'));

    $agencyName = $test->agency_name ?: 'REIAC TEST ASSESSMENT CENTER';
    $agencySubtitle = 'Authorized Examination & Assessment Portal';
    $sealOrg = $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC TE';
    $controllerOrg = $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Test Assessment Center Board';
    $directorOrg = $test->agency_name ? ($test->agency_name . ' Directorate') : 'REIAC Test Assessment Center Directorate';

    $findFont = function ($filename) {
        foreach ([
            storage_path('fonts/' . $filename),
            public_path('fonts/' . $filename),
            base_path('public/fonts/' . $filename),
            base_path('public_html/fonts/' . $filename),
        ] as $p) {
            if (@file_exists($p)) {
                return str_replace('\\', '/', $p);
            }
        }
        return str_replace('\\', '/', storage_path('fonts/' . $filename));
    };

    $fontRegular = $findFont('NanumGothic-Regular.ttf');
    $fontBold = $findFont('NanumGothic-Bold.ttf');
    $fontBlack = $findFont('NanumGothic-ExtraBold.ttf');

    $hasLogo = file_exists(public_path('storage/logo.png'));
    $logoPath = $hasLogo ? str_replace('\\', '/', public_path('storage/logo.png')) : null;

    $hasCandidatePhoto = $attempt->candidate_photo && file_exists(public_path('storage/' . $attempt->candidate_photo));
    $candidatePhotoPath = $hasCandidatePhoto ? str_replace('\\', '/', public_path('storage/' . $attempt->candidate_photo)) : null;
    if (!$candidatePhotoPath && $attempt->user && $attempt->user->avatar && file_exists(public_path('storage/' . $attempt->user->avatar))) {
        $candidatePhotoPath = str_replace('\\', '/', public_path('storage/' . $attempt->user->avatar));
        $hasCandidatePhoto = true;
    }
    $showPhoto = !empty($test->require_camera_photo) || !empty($attempt->candidate_photo);

    $resultLabel = $matchedSlab ? $matchedSlab->name : ($isPass ? 'QUALIFIED (PASS)' : 'COMPLETED');
    $resultColor = $isPass ? '#065f46' : '#be123c';

    // DomPDF-safe L-corner as tiny nested tables (looks like website absolute corners)
    $corner = function ($tl, $tr, $bl, $br) {
        $t = $tl || $tr ? 'border-top:2.5px solid #d97706;' : '';
        $b = $bl || $br ? 'border-bottom:2.5px solid #d97706;' : '';
        $l = $tl || $bl ? 'border-left:2.5px solid #d97706;' : '';
        $r = $tr || $br ? 'border-right:2.5px solid #d97706;' : '';
        return '<div style="width:18px;height:18px;' . $t . $b . $l . $r . '"></div>';
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate - {{ $attempt->user->name ?? 'Candidate' }}</title>
    <style>
        /*
         * Equal page margins + table frames (not div width:100% + border + padding).
         * DomPDF adds border OUTSIDE width → right side was cutting off.
         */
        @page { size: A4 portrait; margin: 11mm; }

        @font-face {
            font-family: 'CertReg';
            font-weight: normal;
            src: url('{{ $fontRegular }}') format('truetype');
        }
        @font-face {
            font-family: 'CertMed';
            font-weight: normal;
            src: url('{{ $fontBold }}') format('truetype');
        }
        @font-face {
            font-family: 'CertBlack';
            font-weight: normal;
            src: url('{{ $fontBlack }}') format('truetype');
        }

        * { margin: 0; padding: 0; }
        html, body {
            font-family: 'CertReg', 'DejaVu Sans', sans-serif;
            background: #ffffff;
            color: #0b1329;
            font-size: 11px;
            line-height: 1.4;
            width: 100%;
        }

        .black { font-family: 'CertBlack', 'CertMed', 'DejaVu Sans', sans-serif; }
        .med { font-family: 'CertMed', 'CertReg', 'DejaVu Sans', sans-serif; }

        /* Outer navy — TABLE so border stays inside page width */
        .frame-navy {
            width: 100%;
            border-collapse: collapse;
            border: 3px solid #0b1329;
            background: #ffffff;
        }
        .frame-navy > tbody > tr > td {
            padding: 6px;
            background: #ffffff;
        }

        .frame-gold {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #d97706;
            background: #ffffff;
        }

        .c { width: 16px; height: 16px; vertical-align: top; }
        .c-bl { vertical-align: bottom; }
        .c-br { vertical-align: bottom; text-align: right; }
        .c-tr { text-align: right; }

        .pad {
            padding: 10px 14px 8px 14px;
            vertical-align: top;
            position: relative;
        }

        .wm {
            position: absolute;
            top: 145px; left: 0; width: 100%;
            text-align: center; font-size: 200px;
            color: #0b1329; opacity: 0.03; line-height: 1; z-index: 0;
        }
        .z { position: relative; z-index: 2; }

        .hdr { width: 100%; border-collapse: collapse; border-bottom: 1px solid #e2e8f0; }
        .hdr td { vertical-align: middle; padding-bottom: 12px; }
        .agency { font-size: 16px; letter-spacing: 0.05em; color: #0b1329; text-transform: uppercase; line-height: 1.25; }
        .agency-sub { font-size: 10px; letter-spacing: 0.1em; color: #64748b; text-transform: uppercase; margin-top: 2px; }
        .ref { text-align: right; font-size: 10px; color: #64748b; line-height: 1.55; }

        .center { text-align: center; padding-top: 16px; padding-bottom: 2px; }

        .badge {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 9999px;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            color: #78350f;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .title {
            font-size: 28px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #0b1329;
            line-height: 1.25;
        }

        .sub { font-size: 12px; color: #64748b; font-style: italic; margin: 6px 0 10px 0; }

        .name-line {
            display: inline-block;
            border-bottom: 2.5px solid #0b1329;
            padding: 0 36px 4px 36px;
        }
        .name { font-size: 28px; letter-spacing: 0.03em; text-transform: uppercase; color: #0b1329; }
        .cand-id { font-size: 11px; color: #64748b; margin-top: 5px; }

        .appeared { font-size: 12px; color: #475569; margin: 12px 0 5px 0; }
        .exam { font-size: 19px; color: #0b1329; margin-bottom: 6px; line-height: 1.3; }
        .pill {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 6px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            color: #334155;
        }

        .stats {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-top: 16px;
        }
        .stat {
            width: 25%;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 11px 5px;
            text-align: center;
            vertical-align: middle;
            background: #fff;
        }
        .stat-l { font-size: 9px; letter-spacing: 0.05em; text-transform: uppercase; color: #94a3b8; line-height: 1.2; }
        .stat-v { font-size: 22px; color: #0b1329; margin-top: 4px; line-height: 1.2; }
        .stat-s { font-size: 10px; color: #64748b; margin-top: 3px; line-height: 1.2; }

        .sigs {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #e2e8f0;
            margin-top: 16px;
        }
        .sigs td {
            vertical-align: middle;
            padding-top: 16px;
            width: 33.33%;
        }
        .sig-block { width: 100%; }
        .sig-n {
            font-family: Georgia, 'Times New Roman', serif;
            font-style: italic;
            font-size: 16px;
            color: #1e293b;
            letter-spacing: 0.04em;
            line-height: 1.3;
        }
        .sig-line { width: 130px; border-bottom: 1px solid #94a3b8; margin: 5px 0; height: 1px; }
        .sig-t { font-size: 10px; text-transform: uppercase; color: #0f172a; letter-spacing: 0.03em; line-height: 1.3; }
        .sig-o { font-size: 8px; color: #64748b; margin-top: 2px; line-height: 1.3; }

        .seal-wrap { text-align: center; width: 100%; }
        .seal-outer {
            width: 72px;
            height: 72px;
            border-radius: 36px;
            border: 3px solid #f59e0b;
            background: #fbbf24;
            margin: 0 auto;
            text-align: center;
        }
        .seal-inner {
            width: 60px;
            height: 60px;
            border-radius: 30px;
            border: 1px solid #d97706;
            margin: 3px auto 0 auto;
            padding-top: 7px;
            text-align: center;
        }
        .seal-org { font-size: 7.5px; letter-spacing: 0.06em; text-transform: uppercase; color: #0b1329; line-height: 1; }
        .seal-star { font-size: 12px; color: #0b1329; line-height: 1; margin: 2px 0; }
        .seal-v { font-size: 6.5px; letter-spacing: 0.05em; text-transform: uppercase; color: #0b1329; line-height: 1; }
        .seal-c { font-size: 5.5px; color: #0b1329; opacity: 0.85; margin-top: 1px; line-height: 1; }
        .seal-ref { font-size: 8px; font-family: monospace; color: #64748b; margin-top: 4px; }

        .foot {
            border-top: 1px solid #f1f5f9;
            margin-top: 14px;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<table class="frame-navy" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td>
    <table class="frame-gold" width="100%" cellpadding="0" cellspacing="0">
        {{-- TOP CORNERS (website-style L brackets) --}}
        <tr>
            <td class="c">{!! $corner(true, false, false, false) !!}</td>
            <td></td>
            <td class="c c-tr">{!! $corner(false, true, false, false) !!}</td>
        </tr>

        <tr>
            <td colspan="3" class="pad">
                <div class="wm">&#9733;</div>
                <div class="z">

                    <table class="hdr">
                        <tr>
                            <td style="width: 100px;">
                                @if($hasLogo)
                                    <img src="{{ $logoPath }}" style="max-height: 48px; max-width: 120px; display: block;">
                                @elseif($test->agency_logo && file_exists(public_path('storage/' . $test->agency_logo)))
                                    <img src="{{ str_replace('\\', '/', public_path('storage/' . $test->agency_logo)) }}" style="max-height: 48px; max-width: 120px; display: block;">
                                @else
                                    <div style="width: 48px; height: 36px; padding-top: 10px; background: #0b1329; border-radius: 10px; text-align: center; color: #fbbf24; font-size: 18px; font-family: 'CertBlack', sans-serif; line-height: 1;">
                                        {{ strtoupper(substr($agencyName, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding-left: 8px;">
                                <div class="agency black">{{ $agencyName }}</div>
                                <div class="agency-sub med">{{ $agencySubtitle }}</div>
                            </td>
                            <td class="ref" style="width: 200px;">
                                <div>Certificate Ref: <span class="med" style="font-family: monospace; color: #0f172a;">{{ $certNumber }}</span></div>
                                <div>Date of Issue: <span class="med" style="color: #1e293b;">{{ $issueDate }}</span></div>
                            </td>
                        </tr>
                    </table>

                    <div class="center">
                        <div class="badge black">&#9733; Official Academic Credential &#9733;</div>
                        <div class="title black">Certificate of Achievement</div>
                        <div class="sub">This is to officially certify that</div>

                        @if($showPhoto)
                            <table style="width:100%; margin: 8px 0;">
                                <tr>
                                    <td style="width:88px; vertical-align:middle;">
                                        @if($hasCandidatePhoto)
                                            <img src="{{ $candidatePhotoPath }}" style="width:78px;height:98px;border-radius:8px;border:2px solid #cbd5e1;">
                                        @else
                                            <div style="width:78px;height:98px;border-radius:8px;background:#f8fafc;border:2px dashed #cbd5e1;text-align:center;padding-top:36px;color:#94a3b8;font-size:9px;" class="med">PHOTO</div>
                                        @endif
                                    </td>
                                    <td style="text-align:center; vertical-align:middle; padding-right:88px;">
                                        <div class="name-line"><span class="name black">{{ $attempt->user->name ?? 'Candidate' }}</span></div>
                                        <div class="cand-id med">Candidate ID / Username: <span style="font-family:monospace;color:#1e293b;">{{ $attempt->user->username ?? ('ID-'.$attempt->user_id) }}</span></div>
                                    </td>
                                </tr>
                            </table>
                        @else
                            <div style="margin:10px 0 8px 0;">
                                <div class="name-line"><span class="name black">{{ $attempt->user->name ?? 'Candidate' }}</span></div>
                                <div class="cand-id med">Candidate ID / Username: <span style="font-family:monospace;color:#1e293b;">{{ $attempt->user->username ?? ('ID-'.$attempt->user_id) }}</span></div>
                            </div>
                        @endif

                        <div class="appeared">has successfully appeared and completed the official examination for</div>
                        <div class="exam black">{{ $test->title }}</div>
                        <div class="pill med">{{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}</div>
                    </div>

                    <table class="stats">
                        <tr>
                            <td class="stat">
                                <div class="stat-l med">Score Obtained</div>
                                <div class="stat-v black">{{ $attempt->score_obtained }} <span style="font-size:12px;color:#94a3b8;">/ {{ $test->total_marks }}</span></div>
                                <div class="stat-s">Total Marks</div>
                            </td>
                            <td class="stat">
                                <div class="stat-l med">Percentage</div>
                                <div class="stat-v black" style="color:{{ $isPass ? '#047857' : '#1e293b' }};">{{ $attempt->percentage }}%</div>
                                <div class="stat-s">Cutoff: {{ $passingPercentage }}%</div>
                            </td>
                            <td class="stat">
                                <div class="stat-l med">Accuracy</div>
                                <div class="stat-v black" style="color:#d97706;">{{ $accuracy }}%</div>
                                <div class="stat-s">{{ $attempt->correct_count }} of {{ $attempt->answered_count }} Correct</div>
                            </td>
                            <td class="stat">
                                <div class="stat-l med">Result Standing</div>
                                <div class="stat-v black" style="font-size:15px;color:{{ $resultColor }};">{{ $resultLabel }}</div>
                                <div class="stat-s">{{ $isPass ? 'Qualifying Standard Met' : 'Assessment Completed' }}</div>
                            </td>
                        </tr>
                    </table>

                    <table class="sigs">
                        <tr>
                            <td style="text-align:left;">
                                <div class="sig-block">
                                    <div class="sig-n">{{ $test->controller_name ?: 'Kang Min-Seok' }}</div>
                                    <div class="sig-line"></div>
                                    <div class="sig-t black">Controller of Examinations</div>
                                    <div class="sig-o">{{ $controllerOrg }}</div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <div class="seal-wrap">
                                    <div class="seal-outer">
                                        <div class="seal-inner">
                                            <div class="seal-org black">{{ $sealOrg }}</div>
                                            <div class="seal-star">&#9733;</div>
                                            <div class="seal-v black">VERIFIED</div>
                                            <div class="seal-c med">CERTIFICATE</div>
                                        </div>
                                    </div>
                                    <div class="seal-ref med">{{ $certNumber }}</div>
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <div class="sig-block">
                                    <div class="sig-n">{{ $test->director_name ?: 'Dr. Rajesh Sharma' }}</div>
                                    <div class="sig-line" style="margin-left:auto;"></div>
                                    <div class="sig-t black">Academic Director</div>
                                    <div class="sig-o">{{ $directorOrg }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="foot">This is an official system-verified electronic certificate issued by {{ $agencyName }}.</div>
                </div>
            </td>
        </tr>

        {{-- BOTTOM CORNERS --}}
        <tr>
            <td class="c c-bl">{!! $corner(false, false, true, false) !!}</td>
            <td></td>
            <td class="c c-br">{!! $corner(false, false, false, true) !!}</td>
        </tr>
    </table>
        </td>
    </tr>
</table>
</body>
</html>
