<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $attempt->user->name ?? 'Candidate' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #0b1329;
        }
        .outer-border {
            border: 4px solid #0b1329;
            padding: 4px;
            background-color: #ffffff;
            height: 98%;
        }
        .inner-border {
            border: 2px solid #d97706;
            padding: 22px 26px;
            background-color: #fefdf9;
            position: relative;
            min-height: 96%;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 14px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .candidate-photo {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            border: 2px solid #d97706;
            object-fit: cover;
        }
        .photo-fallback {
            width: 60px;
            height: 60px;
            line-height: 60px;
            text-align: center;
            border-radius: 8px;
            background-color: #0b1329;
            color: #f59e0b;
            font-size: 26px;
            font-weight: bold;
            border: 2px solid #d97706;
        }
        .inst-title {
            font-size: 16px;
            font-weight: 900;
            color: #0b1329;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .inst-subtitle {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .ref-box {
            text-align: right;
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
        }
        .ref-box strong {
            color: #0b1329;
            font-family: monospace;
            font-size: 11px;
        }
        .badge-center {
            text-align: center;
            margin-top: 18px;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            font-size: 9px;
            font-weight: 800;
            padding: 3px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .cert-heading {
            text-align: center;
            font-size: 26px;
            font-weight: 900;
            color: #0b1329;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 6px 0 3px 0;
        }
        .cert-sub {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            font-style: italic;
            margin-bottom: 16px;
        }
        .candidate-box {
            text-align: center;
            margin: 12px 0 16px 0;
        }
        .candidate-name {
            display: inline-block;
            font-size: 26px;
            font-weight: 900;
            color: #0b1329;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #0b1329;
            padding: 0 30px 4px 30px;
        }
        .candidate-id {
            font-size: 11px;
            font-weight: bold;
            color: #64748b;
            margin-top: 6px;
        }
        .candidate-id span {
            color: #0b1329;
            font-family: monospace;
        }
        .reason-text {
            text-align: center;
            font-size: 11px;
            color: #475569;
            margin: 10px auto;
            max-width: 520px;
            line-height: 1.4;
        }
        .test-title-box {
            text-align: center;
            margin: 8px 0 16px 0;
        }
        .test-title {
            font-size: 18px;
            font-weight: 900;
            color: #0b1329;
            letter-spacing: 0.5px;
        }
        .test-level {
            display: inline-block;
            margin-top: 5px;
            font-size: 9px;
            font-weight: bold;
            color: #1e293b;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 2px 10px;
            border-radius: 12px;
            text-transform: uppercase;
        }
        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .score-table th {
            background-color: #0b1329;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #0b1329;
        }
        .score-table td {
            font-size: 11px;
            font-weight: bold;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #e2e8f0;
            color: #0b1329;
        }
        .badge-pass {
            background-color: #dcfce7;
            color: #15803d;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
        }
        .badge-fail {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 26px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }
        .signatures-table td {
            vertical-align: bottom;
        }
        .sign-title {
            font-size: 14px;
            font-style: italic;
            font-family: Georgia, serif;
            font-weight: bold;
            color: #1e293b;
        }
        .sign-line {
            width: 130px;
            border-bottom: 1px solid #64748b;
            margin: 4px 0;
        }
        .sign-line-right {
            width: 130px;
            border-bottom: 1px solid #64748b;
            margin: 4px 0 4px auto;
        }
        .sign-role {
            font-size: 10px;
            font-weight: 900;
            color: #0b1329;
            text-transform: uppercase;
        }
        .sign-dept {
            font-size: 8px;
            color: #64748b;
        }
        .seal-circle {
            width: 74px;
            height: 74px;
            margin: 0 auto;
            border-radius: 50%;
            border: 3px double #d97706;
            background-color: #fffbeb;
            text-align: center;
            padding-top: 10px;
        }
        .seal-agency {
            font-size: 7px;
            font-weight: 900;
            color: #0b1329;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .seal-star {
            font-size: 11px;
            color: #d97706;
            line-height: 1;
            margin: 1px 0;
        }
        .seal-verified {
            font-size: 6px;
            font-weight: 900;
            color: #b45309;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .seal-code {
            text-align: center;
            font-family: monospace;
            font-size: 7px;
            color: #64748b;
            margin-top: 3px;
        }
        .footer-note {
            margin-top: 20px;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            line-height: 1.4;
        }
        .footer-note span {
            color: #475569;
            font-family: monospace;
            font-weight: bold;
        }
    </style>
</head>
<body>

    @php
        $isPass = $attempt->result === 'pass';
        $passingPercentage = round(($test->passing_marks / max(1, $test->total_marks)) * 100);
        $accuracy = $attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0;
        $matchedSlab = \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
        $certNumber = 'REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT);
        $issueDate = $attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y');
    @endphp

    <div class="outer-border">
        <div class="inner-border">

            <!-- HEADER TABLE -->
            <table class="header-table">
                <tr>
                    <td style="width: 72px;">
                        @if(!empty($candidatePhotoBase64))
                            <img src="{{ $candidatePhotoBase64 }}" class="candidate-photo" alt="Candidate">
                        @else
                            <div class="photo-fallback">
                                {{ strtoupper(substr($attempt->user->name ?? 'C', 0, 1)) }}
                            </div>
                        @endif
                    </td>
                    <td style="padding-left: 12px;">
                        <div class="inst-title">{{ $test->agency_name ?: 'REIAC Test Assessment Center' }}</div>
                        <div class="inst-subtitle">{{ $test->agency_name ? 'Authorized Examination & Assessment Portal' : 'Global Educational Proficiency & Certification' }}</div>
                    </td>
                    <td class="ref-box" style="width: 220px;">
                        <div>Certificate Ref: <strong>{{ $certNumber }}</strong></div>
                        <div>Date of Issue: <strong>{{ $issueDate }}</strong></div>
                    </td>
                </tr>
            </table>

            <!-- BADGE -->
            <div class="badge-center">
                <span class="badge">&#9733; Official Academic Credential &#9733;</span>
            </div>

            <!-- TITLE -->
            <div class="cert-heading">Certificate of Achievement</div>
            <div class="cert-sub">This is to officially certify that</div>

            <!-- CANDIDATE NAME -->
            <div class="candidate-box">
                <div class="candidate-name">{{ $attempt->user->name ?? 'Candidate' }}</div>
                <div class="candidate-id">Candidate ID / Username: <span>{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</span></div>
            </div>

            <div class="reason-text">
                has successfully appeared and completed the official examination assessment for
            </div>

            <!-- TEST TITLE & LEVEL -->
            <div class="test-title-box">
                <div class="test-title">{{ $test->title }}</div>
                @if($test->testLevel)
                    <div><span class="test-level">{{ $test->testLevel->name }}</span></div>
                @endif
            </div>

            <!-- SCORE TABLE -->
            <table class="score-table">
                <thead>
                    <tr>
                        <th>Total Marks</th>
                        <th>Passing Marks</th>
                        <th>Score Obtained</th>
                        <th>Percentage</th>
                        <th>Accuracy</th>
                        <th>Evaluation Result</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $test->total_marks }}</td>
                        <td>{{ $test->passing_marks }} ({{ $passingPercentage }}%)</td>
                        <td style="color: #0b1329; font-size: 13px;">{{ $attempt->score_obtained }}</td>
                        <td>{{ number_format($attempt->percentage, 1) }}%</td>
                        <td>{{ $accuracy }}%</td>
                        <td>
                            @if($isPass)
                                <span class="badge-pass">PASSED</span>
                            @else
                                <span class="badge-fail">{{ strtoupper($attempt->result) }}</span>
                            @endif
                            @if($matchedSlab)
                                <div style="font-size: 8px; color: #64748b; margin-top: 2px;">{{ $matchedSlab->name }}</div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- SIGNATURES & SEAL -->
            <table class="signatures-table">
                <tr>
                    <td style="width: 33%; text-align: left;">
                        <div class="sign-title">{{ $test->controller_name ?: 'Kang Min-Seok' }}</div>
                        <div class="sign-line"></div>
                        <div class="sign-role">Controller of Examinations</div>
                        <div class="sign-dept">{{ $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Assessment Council' }}</div>
                    </td>

                    <td style="width: 34%; text-align: center;">
                        <div class="seal-circle">
                            <div class="seal-agency">{{ $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC' }}</div>
                            <div class="seal-star">&#9733;</div>
                            <div class="seal-verified">OFFICIAL SEAL</div>
                        </div>
                        <div class="seal-code">{{ $certNumber }}</div>
                    </td>

                    <td style="width: 33%; text-align: right;">
                        <div class="sign-title">{{ $test->director_name ?: 'Dr. Rajesh Sharma' }}</div>
                        <div class="sign-line-right"></div>
                        <div class="sign-role">Academic Director</div>
                        <div class="sign-dept">{{ $test->agency_name ? ($test->agency_name . ' Directorate') : 'Global Education Board' }}</div>
                    </td>
                </tr>
            </table>

            <!-- FOOTER NOTICE -->
            <div class="footer-note">
                This is an official system-verified electronic certificate issued by {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}.<br>
                Online Authenticity can be verified at: <span>{{ route('tests.student.certificate', ['test' => $test->id, 'attempt' => $attempt->id]) }}</span>
            </div>

        </div>
    </div>

</body>
</html>
