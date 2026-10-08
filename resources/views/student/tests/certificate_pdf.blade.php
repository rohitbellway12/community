<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate - {{ $attempt->user->name ?? 'Candidate' }}</title>
    <style>
        @page {
            margin: 8mm;
            size: A4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #0b1329;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .outer-border {
            border: 4px solid #0b1329;
            padding: 10px;
        }
        .inner-border {
            border: 2px solid #d97706;
            padding: 24px;
            background-color: #fffdfa;
        }
        .header-table, .signature-table, .metrics-table {
            width: 100%;
            border-collapse: collapse;
        }
        .title {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .badge {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #78350f;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 10px;
            text-transform: uppercase;
            display: inline-block;
        }
        .cert-heading {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0b1329;
            margin: 6px 0;
            letter-spacing: 1px;
        }
        .candidate-name {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0b1329;
            border-bottom: 2px solid #0b1329;
            display: inline-block;
            padding-bottom: 4px;
            margin: 8px 0;
        }
        .test-title {
            font-size: 16px;
            font-weight: bold;
            color: #0b1329;
            margin: 4px 0;
        }
        .metric-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
        }
        .metric-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .metric-val {
            font-size: 16px;
            font-weight: bold;
            color: #0b1329;
            margin: 3px 0;
        }
        .seal-circle {
            width: 65px;
            height: 65px;
            border: 3px solid #f59e0b;
            border-radius: 50%;
            background: #fbbf24;
            color: #0b1329;
            text-align: center;
            margin: 0 auto;
            padding-top: 10px;
            box-sizing: border-box;
            font-size: 8px;
            font-weight: bold;
        }
        .footer-note {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
            margin-top: 20px;
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

        {{-- HEADER TABLE --}}
        <table class="header-table">
            <tr>
                <td style="width: 15%; vertical-align: middle;">
                    @if($attempt->candidate_photo && file_exists(public_path('storage/' . $attempt->candidate_photo)))
                        <img src="{{ public_path('storage/' . $attempt->candidate_photo) }}" style="width: 60px; height: 60px; border-radius: 8px; border: 2px solid #f59e0b; object-fit: cover;">
                    @else
                        <div style="width: 55px; height: 55px; background: #0b1329; color: #f59e0b; font-weight: bold; font-size: 22px; text-align: center; line-height: 55px; border-radius: 8px;">
                            {{ strtoupper(substr($attempt->user->name ?? 'C', 0, 1)) }}
                        </div>
                    @endif
                </td>
                <td style="width: 55%; vertical-align: middle; padding-left: 10px;">
                    <div style="font-size: 15px; font-weight: bold; text-transform: uppercase; color: #0b1329;">
                        {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}
                    </div>
                    <div style="font-size: 9px; color: #64748b; text-transform: uppercase;">
                        Authorized Examination & Assessment Portal
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: middle; font-size: 9px; color: #64748b;">
                    <div>Ref: <strong style="color: #0b1329;">{{ $certNumber }}</strong></div>
                    <div>Issued: <strong style="color: #0b1329;">{{ $issueDate }}</strong></div>
                </td>
            </tr>
        </table>

        {{-- MAIN TITLE & DEDICATION --}}
        <div class="title">
            <span class="badge">★ Official Academic Credential ★</span>
            <div class="cert-heading">Certificate of Achievement</div>
            <div style="font-size: 10px; color: #64748b; font-style: italic;">This is to officially certify that</div>

            <div>
                <span class="candidate-name">{{ $attempt->user->name ?? 'Candidate' }}</span>
            </div>
            <div style="font-size: 9px; color: #64748b;">
                Candidate ID: <strong>{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</strong>
            </div>

            <div style="font-size: 10px; color: #475569; margin-top: 10px;">
                has successfully appeared and completed the official examination for
            </div>
            <div class="test-title">{{ $test->title }}</div>
            <div style="font-size: 9px; color: #64748b;">
                {{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}
            </div>
        </div>

        {{-- METRICS TABLE --}}
        <table class="metrics-table" style="margin-top: 15px; margin-bottom: 20px;">
            <tr>
                <td style="width: 25%; padding: 4px;">
                    <div class="metric-box">
                        <div class="metric-label">Score Obtained</div>
                        <div class="metric-val">{{ $attempt->score_obtained }} / {{ $test->total_marks }}</div>
                        <div style="font-size: 8px; color: #94a3b8;">Total Marks</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 4px;">
                    <div class="metric-box">
                        <div class="metric-label">Percentage</div>
                        <div class="metric-val" style="color: {{ $isPass ? '#059669' : '#0b1329' }};">{{ $attempt->percentage }}%</div>
                        <div style="font-size: 8px; color: #94a3b8;">Cutoff: {{ $passingPercentage }}%</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 4px;">
                    <div class="metric-box">
                        <div class="metric-label">Accuracy</div>
                        <div class="metric-val" style="color: #d97706;">{{ $accuracy }}%</div>
                        <div style="font-size: 8px; color: #94a3b8;">{{ $attempt->correct_count }} / {{ $attempt->answered_count }} Correct</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 4px;">
                    <div class="metric-box">
                        <div class="metric-label">Result Standing</div>
                        <div class="metric-val" style="font-size: 13px; color: {{ $isPass ? '#059669' : '#b91c1c' }};">
                            @if($matchedSlab)
                                {{ $matchedSlab->name }}
                            @elseif($isPass)
                                QUALIFIED (PASS)
                            @else
                                COMPLETED
                            @endif
                        </div>
                        <div style="font-size: 8px; color: #94a3b8;">{{ $isPass ? 'Standard Met' : 'Completed' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- SIGNATURE TABLE --}}
        <table class="signature-table" style="margin-top: 25px;">
            <tr>
                <td style="width: 35%; text-align: left; vertical-align: bottom;">
                    <div style="font-size: 13px; font-weight: bold; font-style: italic; color: #1e293b;">
                        {{ $test->controller_name ?: 'Kang Min-Seok' }}
                    </div>
                    <div style="border-bottom: 1px solid #94a3b8; width: 140px; margin: 3px 0;"></div>
                    <div style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Controller of Examinations</div>
                    <div style="font-size: 8px; color: #64748b;">{{ $test->agency_name ? ($test->agency_name . ' Board') : 'REIAC Council' }}</div>
                </td>

                <td style="width: 30%; text-align: center; vertical-align: middle;">
                    <div class="seal-circle">
                        <div style="font-size: 7px; text-transform: uppercase;">{{ $test->agency_name ? strtoupper(substr($test->agency_name, 0, 8)) : 'REIAC' }}</div>
                        <div style="font-size: 11px;">★</div>
                        <div style="font-size: 6px;">VERIFIED</div>
                    </div>
                    <div style="font-size: 7px; color: #64748b; margin-top: 2px;">{{ $certNumber }}</div>
                </td>

                <td style="width: 35%; text-align: right; vertical-align: bottom;">
                    <div style="font-size: 13px; font-weight: bold; font-style: italic; color: #1e293b;">
                        {{ $test->director_name ?: 'Dr. Rajesh Sharma' }}
                    </div>
                    <div style="border-bottom: 1px solid #94a3b8; width: 140px; margin: 3px 0; margin-left: auto;"></div>
                    <div style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Academic Director</div>
                    <div style="font-size: 8px; color: #64748b;">Global Education Board</div>
                </td>
            </tr>
        </table>

        {{-- FOOTER NOTE --}}
        <div class="footer-note">
            This is an official system-verified electronic certificate issued by {{ $test->agency_name ?: 'REIAC Test Assessment Center' }}.<br>
            Authenticity can be verified at: {{ route('tests.student.certificate', ['test' => $test->id, 'attempt' => $attempt->id]) }}
        </div>

    </div>
</div>

</body>
</html>
