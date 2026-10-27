<!DOCTYPE html>
<html lang="{{ ($resume->language ?? 'en') === 'ko' ? 'ko' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $resume->title }} - {{ $resume->personal_info['full_name'] ?? 'Candidate' }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Malgun Gothic', 'Apple SD Gothic Neo', 'Nanum Gothic', 'Noto Sans KR', 'Segoe UI', Arial, sans-serif;
            color: #111827;
            background: #ffffff;
            font-size: 9.5pt;
            line-height: 1.45;
        }
        .page {
            position: relative;
            padding: 28px 34px;
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 40%;
            left: 5%;
            width: 90%;
            text-align: center;
            font-size: 36pt;
            font-weight: 900;
            color: rgba(220, 38, 38, 0.20);
            text-transform: uppercase;
            letter-spacing: 4px;
            transform: rotate(-30deg);
            z-index: 99999;
            pointer-events: none;
            line-height: 1.2;
        }
        .watermark-sub {
            font-size: 13pt;
            display: block;
            margin-top: 8px;
            font-weight: 700;
            letter-spacing: 2px;
            color: rgba(220, 38, 38, 0.24);
        }

        /* Form Title */
        .doc-title-wrap {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            font-size: 22pt;
            font-weight: 900;
            letter-spacing: 6px;
            color: #0f172a;
            display: inline-block;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
        }
        .doc-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        /* Korean Table Grid Base */
        table.kr-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }
        table.kr-table th, table.kr-table td {
            border: 1px solid #94a3b8;
            padding: 5px 7px;
            vertical-align: middle;
        }
        table.kr-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.3px;
        }
        table.kr-table td {
            color: #1e293b;
        }
        table.kr-table td.center {
            text-align: center;
        }

        /* Section Header Bar */
        .section-header {
            font-size: 10pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 14px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 3.5px solid #0f172a;
            padding-left: 7px;
        }
        .section-header span.sec-num {
            color: #b45309;
            margin-right: 4px;
        }

        /* Photo Box */
        .photo-box {
            width: 95px;
            height: 120px;
            border: 1px dashed #94a3b8;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 8pt;
            color: #64748b;
            overflow: hidden;
            margin: 0 auto;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Self Introduction Blocks */
        .essay-block {
            border: 1px solid #94a3b8;
            margin-bottom: 10px;
        }
        .essay-title {
            background: #f1f5f9;
            padding: 5px 10px;
            font-weight: 700;
            font-size: 8.5pt;
            color: #0f172a;
            border-bottom: 1px solid #94a3b8;
        }
        .essay-content {
            padding: 8px 10px;
            font-size: 8.5pt;
            color: #334155;
            line-height: 1.55;
            min-height: 48px;
            white-space: pre-line;
            text-align: justify;
        }

        /* Final Declaration & Signature Box */
        .pledge-box {
            text-align: center;
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px dashed #cbd5e1;
        }
        .pledge-text {
            font-size: 10pt;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .pledge-date {
            font-size: 9pt;
            color: #475569;
            margin-bottom: 10px;
        }
        .pledge-signature {
            font-size: 10pt;
            font-weight: 700;
            color: #0f172a;
        }

        /* Web print preview bar */
        @media screen {
            body {
                background: #f1f5f9;
                padding: 24px 12px;
            }
            .page {
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                background: #ffffff;
            }
            .web-preview-bar {
                max-width: 820px;
                margin: 0 auto 16px auto;
                background: #0f172a;
                color: #ffffff;
                padding: 12px 20px;
                border-radius: 12px;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                font-size: 13px;
                box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            }
            .web-preview-bar a {
                background: #f59e0b;
                color: #0f172a;
                padding: 7px 16px;
                border-radius: 8px;
                font-weight: 800;
                text-decoration: none;
                margin-left: 8px;
            }
        }
        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            .web-preview-bar {
                display: none !important;
            }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .page {
                box-shadow: none !important;
                padding: 16px 24px !important;
                max-width: 100% !important;
                width: 100% !important;
                border: none !important;
                margin: 0 !important;
            }
            .watermark {
                display: block !important;
                position: fixed !important;
                top: 40% !important;
                left: 5% !important;
                width: 90% !important;
                transform: rotate(-30deg) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                z-index: 99999 !important;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }

        @if(!empty($forPdf))
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }
        .page {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
        .watermark {
            display: none !important;
        }
        @endif
    </style>
</head>
<body>

    {{-- DOMPDF NATIVE WATERMARK CANVAS SCRIPT (FOR PDF EXPORTS) --}}
    @if(!empty($forPdf) && !$isClean)
        <script type="text/php">
            if (isset($pdf)) {
                $w = $pdf->get_width();
                $h = $pdf->get_height();
                $font = $fontMetrics->get_font("helvetica", "bold");
                
                if (method_exists($pdf, "set_opacity")) {
                    $pdf->set_opacity(0.18, "Normal");
                }
                
                // Primary Diagonal Watermark
                $watermarkText = "{{ addslashes($setting->watermark_text ?: 'REIAC COMMUNITY') }}";
                $pdf->page_text($w * 0.12, $h * 0.56, $watermarkText, $font, 46, [0.85, 0.20, 0.20], 0, 0, -35);
                
                // Watermark Subtitle
                $subFont = $fontMetrics->get_font("helvetica", "bold");
                $subText = "{{ ($resume->language ?? 'en') === 'ko' ? '워터마크 제거를 위해 공유하세요 • reiac.org' : 'SHARE TO REMOVE WATERMARK • REIAC.ORG' }}";
                $pdf->page_text($w * 0.16, $h * 0.60, $subText, $subFont, 12, [0.85, 0.20, 0.20], 0, 0, -35);
                
                if (method_exists($pdf, "set_opacity")) {
                    $pdf->set_opacity(1.0, "Normal");
                }
            }
        </script>
    @endif

    @php
        $isKo = ($resume->language ?? 'en') === 'ko';
        $personal = $resume->personal_info ?? [];
        $education = $resume->education ?? [];
        $experience = $resume->experience ?? [];
        $certifications = $resume->certifications ?? [];
        $langTests = $resume->language_tests ?? [];
        $military = $resume->military_service ?? [];
        $compSkills = $resume->computer_skills ?? [];
        $selfIntro = $resume->self_introduction ?? [];
    @endphp

    @if(!$forPdf && empty($isEmbed))
        <div class="web-preview-bar">
            <div>
                @if(!empty($isSample))
                    <strong>👀 {{ $isKo ? '샘플 미리보기' : 'Sample Preview' }}:</strong> {{ $isKo ? '한국형 표준 이력서 / 입사지원서' : 'Korean Standard Job Application Form' }}
                    <span style="background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 3px 10px; border-radius: 9999px; font-size: 11px; margin-left: 8px; font-weight: 800;">
                        {{ $isKo ? '한국어 서식' : 'English Form' }}
                    </span>
                @else
                    <strong>📄 {{ $resume->title }}</strong>
                    @if(!$isClean)
                        <span style="background: #ef4444; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px; font-weight: 800;">Watermarked</span>
                    @else
                        <span style="background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px; font-weight: 800;">✓ Clean Unlocked</span>
                    @endif
                @endif
            </div>
            <div>
                @if(!empty($isSample))
                    <a href="{{ route('resume-builder.create', ['template' => 'korean_standard']) }}" style="background: #f59e0b; color: #0f172a; font-weight: 900; box-shadow: 0 4px 12px rgba(245,158,11,0.3);">
                        {{ $isKo ? '이 양식으로 작성하기 →' : 'Use This Template Now →' }}
                    </a>
                    <a href="{{ route('resume-builder.index') }}" style="background: #334155; color: #fff;">← Back</a>
                @else
                    <a href="{{ route('resume-builder.edit', $resume->id) }}" style="background: #334155; color: #fff;">✏️ Edit CV</a>
                    <button type="button" onclick="window.print()" style="background: #f59e0b; color: #0f172a; padding: 7px 16px; border-radius: 8px; font-weight: 800; border: none; cursor: pointer; margin-left: 8px;">📥 Download / Save PDF</button>
                @endif
            </div>
        </div>
    @endif

    <div class="page">

        {{-- WATERMARK --}}
        @if(!$isClean)
            <div class="watermark">
                {{ $setting->watermark_text ?: 'REIAC COMMUNITY' }}
                <span class="watermark-sub">{{ $isKo ? '공유 후 워터마크 제거 • reiac.org' : 'Share to Remove Watermark • reiac.org' }}</span>
            </div>
        @endif

        {{-- 1. TITLE --}}
        <div class="doc-title-wrap">
            <h1 class="doc-title">{{ $isKo ? '입 사 지 원 서' : 'APPLICATION FORM' }}</h1>
            <div class="doc-subtitle">{{ $isKo ? '(이 력 서 및 자 기 소 개 서)' : 'CURRICULUM VITAE & PERSONAL STATEMENT' }}</div>
        </div>

        {{-- 2. PERSONAL INFORMATION TABLE (WITH PHOTO BOX) --}}
        <div class="section-header">
            <div><span class="sec-num">1.</span> {{ $isKo ? '인적사항 (Personal Information)' : 'Personal Information' }}</div>
        </div>

        <table class="kr-table">
            <colgroup>
                <col style="width: 15%;">
                <col style="width: 28%;">
                <col style="width: 14%;">
                <col style="width: 25%;">
                <col style="width: 18%;">
            </colgroup>
            <tbody>
                <tr>
                    <th>{{ $isKo ? '성 명' : 'Name' }}</th>
                    <td>
                        <strong>{{ $personal['full_name'] ?? '' }}</strong>
                        @if(!empty($personal['english_name']))
                            <div style="font-size: 7.5pt; color: #64748b;">(Eng) {{ $personal['english_name'] }}</div>
                        @endif
                    </td>
                    <th>{{ $isKo ? '생년월일' : 'Date of Birth' }}</th>
                    <td>
                        {{ $personal['date_of_birth'] ?? ($personal['dob'] ?? '') }}
                        @if(!empty($personal['gender']))
                            <span style="font-size: 8pt; color: #64748b; margin-left: 6px;">({{ $personal['gender'] }})</span>
                        @endif
                    </td>
                    <td rowspan="4" style="padding: 4px; text-align: center; vertical-align: middle;">
                        <div class="photo-box">
                            @if(!empty($personal['photo_url']))
                                <img src="{{ $personal['photo_url'] }}" alt="Photo">
                            @else
                                <div>
                                    <span style="font-size: 16pt;">📷</span><br>
                                    <span>{{ $isKo ? '사진' : 'Photo' }}</span><br>
                                    <span style="font-size: 6.5pt; color: #94a3b8;">(3cm x 4cm)</span>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>{{ $isKo ? '지원분야' : 'Applied Field' }}</th>
                    <td><strong>{{ $personal['job_title'] ?? ($personal['applying_field'] ?? '') }}</strong></td>
                    <th>{{ $isKo ? '총 경력' : 'Total Exp' }}</th>
                    <td>{{ $personal['total_experience'] ?? ($isKo ? '신입/경력' : 'Entry/Experienced') }}</td>
                </tr>
                <tr>
                    <th>{{ $isKo ? '연락처 (휴대폰)' : 'Mobile Phone' }}</th>
                    <td>
                        {{ $personal['phone'] ?? '' }}
                        @if(!empty($personal['emergency_phone']))
                            <div style="font-size: 7.5pt; color: #64748b;">{{ $isKo ? '(비상) ' : '(Alt) ' }}{{ $personal['emergency_phone'] }}</div>
                        @endif
                    </td>
                    <th>{{ $isKo ? '이메일' : 'E-mail' }}</th>
                    <td>{{ $personal['email'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>{{ $isKo ? '주 소' : 'Address' }}</th>
                    <td colspan="3">
                        {{ $personal['location'] ?? ($personal['address'] ?? '') }}
                        @if(!empty($personal['linkedin']) || !empty($personal['github']))
                            <div style="font-size: 7.5pt; color: #475569; margin-top: 2px;">
                                @if(!empty($personal['linkedin']))
                                    <span style="margin-right: 10px;">🔗 {{ $personal['linkedin'] }}</span>
                                @endif
                                @if(!empty($personal['github']))
                                    <span>💻 {{ $personal['github'] }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- 3. EDUCATION TABLE --}}
        <div class="section-header">
            <div><span class="sec-num">2.</span> {{ $isKo ? '학력사항 (Education)' : 'Education History' }}</div>
            <span style="font-size: 7.5pt; color: #64748b; font-weight: normal;">{{ $isKo ? '※ 최신순 기재' : 'Reverse chronological order' }}</span>
        </div>

        <table class="kr-table">
            <colgroup>
                <col style="width: 25%;">
                <col style="width: 35%;">
                <col style="width: 25%;">
                <col style="width: 15%;">
            </colgroup>
            <thead>
                <tr>
                    <th>{{ $isKo ? '재학기간' : 'Period (From - To)' }}</th>
                    <th>{{ $isKo ? '학교명 및 전공' : 'Institution & Major' }}</th>
                    <th>{{ $isKo ? '학위 / 구분' : 'Degree / Status' }}</th>
                    <th>{{ $isKo ? '학점 (Grade)' : 'GPA / Grade' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($education as $edu)
                    <tr>
                        <td class="center">{{ $edu['start_date'] ?? '' }} ~ {{ !empty($edu['is_current']) ? ($isKo ? '재학중' : 'Present') : ($edu['end_date'] ?? '') }}</td>
                        <td>
                            <strong>{{ $edu['institution'] ?? '' }}</strong>
                            @if(!empty($edu['field']))
                                <span style="color: #475569;">({{ $edu['field'] }})</span>
                            @endif
                        </td>
                        <td class="center">{{ $edu['degree'] ?? '' }}</td>
                        <td class="center">{{ $edu['grade'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="center" style="color: #94a3b8; padding: 10px;">{{ $isKo ? '기재된 학력 정보가 없습니다.' : 'No education history recorded.' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- 4. WORK EXPERIENCE TABLE --}}
        <div class="section-header">
            <div><span class="sec-num">3.</span> {{ $isKo ? '경력사항 (Work Experience)' : 'Work Experience' }}</div>
            <span style="font-size: 7.5pt; color: #64748b; font-weight: normal;">{{ $isKo ? '※ 주요 직무 및 성과 요약' : 'Key roles & achievements' }}</span>
        </div>

        <table class="kr-table">
            <colgroup>
                <col style="width: 22%;">
                <col style="width: 26%;">
                <col style="width: 16%;">
                <col style="width: 36%;">
            </colgroup>
            <thead>
                <tr>
                    <th>{{ $isKo ? '근무기간' : 'Period' }}</th>
                    <th>{{ $isKo ? '회사명 / 부서' : 'Company & Dept' }}</th>
                    <th>{{ $isKo ? '직급 / 직책' : 'Position / Role' }}</th>
                    <th>{{ $isKo ? '담당업무 및 성과' : 'Key Responsibilities & Achievements' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($experience as $exp)
                    <tr>
                        <td class="center">{{ $exp['start_date'] ?? '' }} ~ {{ !empty($exp['is_current']) ? ($isKo ? '현재 재직중' : 'Present') : ($exp['end_date'] ?? '') }}</td>
                        <td>
                            <strong>{{ $exp['company'] ?? '' }}</strong>
                            @if(!empty($exp['department']))
                                <div style="font-size: 7.5pt; color: #64748b;">{{ $exp['department'] }}</div>
                            @elseif(!empty($exp['location']))
                                <div style="font-size: 7.5pt; color: #64748b;">{{ $exp['location'] }}</div>
                            @endif
                        </td>
                        <td class="center">{{ $exp['title'] ?? ($exp['position'] ?? '') }}</td>
                        <td>
                            @if(!empty($exp['points']) && is_array($exp['points']) && count($exp['points']) > 0)
                                <ul style="margin-left: 14px; font-size: 8pt; line-height: 1.35;">
                                    @foreach($exp['points'] as $pt)
                                        @if(!empty(trim($pt)))
                                            <li>{{ trim($pt) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @elseif(!empty($exp['rawNotes']))
                                <div style="font-size: 8pt; line-height: 1.4; white-space: pre-line;">{{ $exp['rawNotes'] }}</div>
                            @else
                                <div style="font-size: 8pt; line-height: 1.4; white-space: pre-line;">{{ $exp['description'] ?? '-' }}</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="center" style="color: #94a3b8; padding: 10px;">{{ $isKo ? '기재된 경력사항이 없습니다.' : 'No experience records added.' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- 4. LANGUAGE PROFICIENCY & CERTIFICATIONS --}}
        <div class="section-header">
            <div><span class="sec-num">4.</span> {{ $isKo ? '어학능력 및 자격사항 (Language Proficiency & Certifications)' : 'Language Proficiency & Certifications' }}</div>
        </div>

        <div style="display: table; width: 100%; border-spacing: 0;">
            <div style="display: table-cell; width: 49%; vertical-align: top; padding-right: 6px;">
                <div style="font-size: 8pt; font-weight: 700; color: #475569; margin-bottom: 3px; padding-left: 2px;">
                    ▪ {{ $isKo ? '공인어학성적 (Language Tests)' : 'Official Language Tests' }}
                </div>
                <table class="kr-table">
                    <thead>
                        <tr>
                            <th>{{ $isKo ? '외국어/시험명' : 'Language / Test' }}</th>
                            <th>{{ $isKo ? '점수 / 등급' : 'Score / Level' }}</th>
                            <th>{{ $isKo ? '취득일자' : 'Date' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($langTests as $lt)
                            <tr>
                                <td class="center">
                                    <strong>{{ $lt['test_name'] ?? ($lt['name'] ?? '') }}</strong>
                                    @if(!empty($lt['organization']))
                                        <div style="font-size: 7pt; color: #64748b;">{{ $lt['organization'] }}</div>
                                    @endif
                                </td>
                                <td class="center">{{ $lt['score'] ?? ($lt['level'] ?? '') }}</td>
                                <td class="center">{{ $lt['acquisition_date'] ?? ($lt['date'] ?? '') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="center" style="color: #94a3b8;">{{ $isKo ? '기재된 어학 성적이 없습니다.' : 'No language test scores recorded.' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 6px;">
                <div style="font-size: 8pt; font-weight: 700; color: #475569; margin-bottom: 3px; padding-left: 2px;">
                    ▪ {{ $isKo ? '자격증 및 면허 (Certificates & Licenses)' : 'Certificates & Licenses' }}
                </div>
                <table class="kr-table">
                    <thead>
                        <tr>
                            <th>{{ $isKo ? '자격 / 면허명' : 'Certificate Name' }}</th>
                            <th>{{ $isKo ? '발행기관' : 'Issuer' }}</th>
                            <th>{{ $isKo ? '취득년월' : 'Date' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certifications as $cert)
                            <tr>
                                <td><strong>{{ $cert['name'] ?? '' }}</strong></td>
                                <td class="center">{{ $cert['issuer'] ?? '-' }}</td>
                                <td class="center">{{ $cert['year'] ?? ($cert['date'] ?? '') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="center" style="color: #94a3b8;">{{ $isKo ? '취득 자격증 없음' : 'None' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. MILITARY SERVICE & COMPUTER / TECHNICAL SKILLS --}}
        <div class="section-header">
            <div><span class="sec-num">5.</span> {{ $isKo ? '병역사항 및 주요 보유기술 (Military Service & Technical Skills)' : 'Military Service & Technical Skills' }}</div>
        </div>

        <div style="display: table; width: 100%; border-spacing: 0;">
            <div style="display: table-cell; width: 49%; vertical-align: top; padding-right: 6px;">
                <div style="font-size: 8pt; font-weight: 700; color: #475569; margin-bottom: 3px; padding-left: 2px;">
                    ▪ {{ $isKo ? '병역사항 (Military Status)' : 'Military Service' }}
                </div>
                <table class="kr-table">
                    <thead>
                        <tr>
                            <th>{{ $isKo ? '병역구분' : 'Status' }}</th>
                            <th>{{ $isKo ? '군별 / 계급' : 'Branch / Rank' }}</th>
                            <th>{{ $isKo ? '복무기간' : 'Period' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="center">{{ $military['status'] ?? ($isKo ? '군필 / 해당없음' : 'Completed / N/A') }}</td>
                            <td class="center">
                                @if(!empty($military['branch']) && !empty($military['rank']))
                                    {{ $military['branch'] }} / {{ $military['rank'] }}
                                @else
                                    {{ $military['branch'] ?? ($military['rank'] ?? ($isKo ? '육군 / 병장' : 'Army / Sergeant')) }}
                                @endif
                            </td>
                            <td class="center">
                                {{ $military['period'] ?? ($isKo ? '만기제대' : 'Honorable Discharge') }}
                                @if(!empty($military['discharge_reason']))
                                    <div style="font-size: 7pt; color: #64748b;">({{ $military['discharge_reason'] }})</div>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 6px;">
                <div style="font-size: 8pt; font-weight: 700; color: #475569; margin-bottom: 3px; padding-left: 2px;">
                    ▪ {{ $isKo ? '주요 보유기술 및 역량 (Skills & Tools)' : 'Technical Skills & Tools' }}
                </div>
                <table class="kr-table">
                    <thead>
                        <tr>
                            <th>{{ $isKo ? '기술 / 툴' : 'Skill / Tool' }}</th>
                            <th>{{ $isKo ? '숙련도' : 'Level' }}</th>
                            <th>{{ $isKo ? '활용분야' : 'Application' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($compSkills) && count($compSkills) > 0)
                            @foreach(array_slice($compSkills, 0, 3) as $cSkill)
                                <tr>
                                    <td><strong>{{ $cSkill['tool_name'] ?? '' }}</strong></td>
                                    <td class="center">{{ $cSkill['proficiency'] ?? '-' }}</td>
                                    <td style="font-size: 7.5pt;">{{ $cSkill['description'] ?? ($cSkill['category'] ?? '-') }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(!empty($resume->skills) && is_array($resume->skills) && count($resume->skills) > 0)
                            <tr>
                                <td colspan="3" style="padding: 5px;">
                                    @foreach(array_slice($resume->skills, 0, 10) as $sk)
                                        <span style="display: inline-block; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 2px 6px; border-radius: 3px; font-size: 7.5pt; margin: 1px 2px; font-weight: bold;">
                                            {{ $sk }}
                                        </span>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                        @if((empty($compSkills) || count($compSkills) === 0) && (empty($resume->skills) || count($resume->skills) === 0))
                            <tr>
                                <td colspan="3" class="center" style="color: #94a3b8; padding: 8px;">{{ $isKo ? '등록된 보유기술이 없습니다.' : 'No skills recorded.' }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KEY PROJECTS (IF AVAILABLE) --}}
        @if(!empty($resume->projects) && count($resume->projects) > 0)
            <div style="margin-top: 10px;">
                <div class="section-header">
                    <div><span class="sec-num">★</span> {{ $isKo ? '주요 프로젝트 (Key Projects)' : 'Key Projects' }}</div>
                </div>
                <table class="kr-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">{{ $isKo ? '프로젝트명' : 'Project Title' }}</th>
                            <th style="width: 25%;">{{ $isKo ? '사용 기술' : 'Tech Stack' }}</th>
                            <th>{{ $isKo ? '프로젝트 내용 및 역할' : 'Description / Role' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resume->projects as $proj)
                            <tr>
                                <td><strong>{{ $proj['title'] ?? '' }}</strong></td>
                                <td class="center" style="color: #047857; font-weight: bold;">{{ $proj['tech'] ?? '-' }}</td>
                                <td style="font-size: 8pt; line-height: 1.35;">{{ $proj['description'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- 6. KOREAN SELF-INTRODUCTION / COVER LETTER (자기소개서) --}}
        <div style="page-break-before: always; margin-top: 18px;"></div>

        <div class="section-header" style="margin-top: 10px; margin-bottom: 12px; font-size: 10.5pt;">
            <div><span class="sec-num">6.</span> {{ $isKo ? '자 기 소 개 서 (Personal Statement)' : 'Personal Statement' }}</div>
            <span style="font-size: 8pt; color: #64748b; font-weight: normal;">{{ $isKo ? '지원자: ' . ($personal['full_name'] ?? '성명') : 'Candidate: ' . ($personal['full_name'] ?? 'Candidate') }}</span>
        </div>

        <div class="doc-title-wrap" style="margin-top: 8px; margin-bottom: 14px;">
            <h2 class="doc-title" style="font-size: 18pt;">{{ $isKo ? '자 기 소 개 서' : 'PERSONAL STATEMENT' }}</h2>
            <div class="doc-subtitle">{{ $isKo ? '(성장과정 • 성격의 장단점 • 직무역량 • 지원동기)' : '(Growth • Personality • Core Competencies • Motivation)' }}</div>
        </div>

        {{-- 1. Growth Process --}}
        <div class="essay-block">
            <div class="essay-title">{{ $isKo ? '1. 성장과정 (Growth Background & Values)' : '1. Background & Personal Values' }}</div>
            <div class="essay-content">{{ $selfIntro['growth'] ?? ($resume->summary ?? ($isKo ? '도전적인 자세와 성실함을 바탕으로 목표를 설정하고 끊임없이 성장해 왔습니다.' : 'Driven by curiosity and a commitment to excellence, I have consistently pursued growth and learning.')) }}</div>
        </div>

        {{-- 2. Personality Strengths --}}
        <div class="essay-block">
            <div class="essay-title">{{ $isKo ? '2. 성격의 장단점 및 생활신조 (Personality & Work Ethic)' : '2. Key Strengths & Work Ethic' }}</div>
            <div class="essay-content">{{ $selfIntro['personality'] ?? ($isKo ? '긍정적인 마인드와 철저한 실행력으로 팀 프로젝트에서 신뢰받는 조력자 역할을 해왔습니다.' : 'A proactive problem-solver and collaborative team player known for reliable execution and resilience under pressure.') }}</div>
        </div>

        {{-- 3. Key Projects & Job Competencies --}}
        <div class="essay-block">
            <div class="essay-title">{{ $isKo ? '3. 주요경력 및 직무수행 역량 (Key Projects & Job Competencies)' : '3. Core Competencies & Key Projects' }}</div>
            <div class="essay-content">
@if(!empty($selfIntro['strengths']))
{{ $selfIntro['strengths'] }}
@elseif(!empty($resume->projects) && is_array($resume->projects) && count($resume->projects) > 0)
@foreach($resume->projects as $p)
• {{ $p['title'] ?? '' }}{{ !empty($p['tech']) ? ' (' . $p['tech'] . ')' : '' }}: {{ $p['description'] ?? '' }}
@endforeach
@else
{{ $isKo ? '다양한 직무 프로젝트를 직접 주도하며 문제 해결 능력과 실무 감각을 체득했습니다.' : 'Led multiple technical initiatives demonstrating engineering rigor and cross-functional leadership.' }}
@endif
            </div>
        </div>

        {{-- 4. Reason for Applying & Future Aspirations --}}
        <div class="essay-block">
            <div class="essay-title">{{ $isKo ? '4. 지원동기 및 입사 후 포부 (Motivation for Applying & Future Goals)' : '4. Motivation for Applying & Future Aspirations' }}</div>
            <div class="essay-content">{{ !empty($selfIntro['motivation_and_aspirations']) ? $selfIntro['motivation_and_aspirations'] : ($selfIntro['motivation'] ?? ($isKo ? '귀사의 혁신적인 비전에 깊이 공감하며, 축적된 역량을 바탕으로 회사와 함께 지속적인 성과를 창출하겠습니다.' : 'Deeply aligned with the organizational vision, I aim to leverage my skills to deliver measurable business impact and long-term value.')) }}</div>
        </div>

        {{-- 7. PLEDGE & SIGNATURE BLOCK --}}
        <div class="section-header" style="margin-top: 18px; margin-bottom: 8px;">
            <div><span class="sec-num">7.</span> {{ $isKo ? '사실 확인 및 서약 (Declaration & Signature)' : 'Declaration & Signature' }}</div>
        </div>

        <div class="pledge-box" style="margin-top: 8px; border-top: none; padding-top: 4px;">
            <div class="pledge-text">
                {{ $isKo ? '위의 모든 기재사항은 사실과 다름없음을 확인합니다.' : 'I hereby certify that all information stated above is true and correct.' }}
            </div>
            <div class="pledge-date">
                {{ $personal['declaration_date'] ?? date($isKo ? 'Y년 m월 d일' : 'F d, Y') }}
            </div>
            <div class="pledge-signature">
                {{ $isKo ? '지원자(작성자) :' : 'Applicant Signature :' }}
                <span style="border-bottom: 1px solid #000; padding: 0 25px; margin-left: 6px; font-weight: 700;">
                    {{ $personal['signature_name'] ?? ($personal['full_name'] ?? ($isKo ? '성명' : 'Candidate Name')) }}
                </span>
                <span style="font-size: 8pt; color: #64748b; margin-left: 6px;">{{ $isKo ? '(인 / 서명)' : '(Signed)' }}</span>
            </div>
        </div>

    </div>

    @if(request()->boolean('print'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => { window.print(); }, 400);
            });
        </script>
    @endif
</body>
</html>
