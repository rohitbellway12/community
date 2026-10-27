<!DOCTYPE html>
<html lang="{{ ($resume->language ?? 'en') === 'ko' ? 'ko' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $resume->title }} - {{ $resume->personal_info['full_name'] ?? 'Candidate' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Batang', 'AppleMyungjo', 'Nanum Myeongjo', 'Georgia', 'Garamond', 'Times New Roman', serif;
            color: #111827;
            background: #ffffff;
            font-size: 10.5pt;
            line-height: 1.45;
        }
        .page {
            position: relative;
            padding: 34px 44px;
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 42%;
            left: 5%;
            width: 90%;
            text-align: center;
            font-size: 38pt;
            font-weight: 900;
            color: rgba(160, 160, 160, 0.22);
            text-transform: uppercase;
            letter-spacing: 5px;
            transform: rotate(-30deg);
            z-index: 99999;
            pointer-events: none;
            font-family: Arial, sans-serif;
        }
        .watermark-sub {
            font-size: 14pt;
            display: block;
            margin-top: 8px;
            font-weight: 700;
            letter-spacing: 2px;
            color: rgba(160, 160, 160, 0.28);
        }

        .header {
            text-align: center;
            border-bottom: 1.5px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .name {
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #111827;
        }
        .title {
            font-size: 11.5pt;
            font-style: italic;
            color: #4b5563;
            margin-top: 2px;
        }
        .contact {
            margin-top: 6px;
            font-size: 9pt;
            color: #4b5563;
            font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;
        }

        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            border-bottom: 1.5px solid #111827;
            padding-bottom: 2px;
            margin-bottom: 8px;
            color: #111827;
        }

        .entry {
            margin-bottom: 10px;
        }
        .row {
            width: 100%;
        }
        .left {
            float: left;
            font-weight: bold;
            font-size: 10.5pt;
            color: #111827;
        }
        .right {
            float: right;
            font-style: italic;
            font-size: 9pt;
            color: #4b5563;
        }
        .clear {
            clear: both;
        }
        .sub-left {
            font-style: italic;
            font-size: 9.5pt;
            color: #374151;
            margin-bottom: 3px;
        }

        ul {
            margin-left: 20px;
            margin-top: 2px;
        }
        ul li {
            font-size: 9.5pt;
            margin-bottom: 3px;
            color: #1f2937;
            text-align: justify;
        }

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
                background: #111827;
                color: #fff;
                padding: 12px 20px;
                border-radius: 12px;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                font-size: 13px;
                font-family: Arial, sans-serif;
                box-shadow: 0 4px 12px rgba(17, 24, 39, 0.15);
            }
            .web-preview-bar a {
                background: #f59e0b;
                color: #000;
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
            .web-preview-bar { display: none !important; }
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
                padding: 24px 30px !important;
                max-width: 100% !important;
                width: 100% !important;
                border: none !important;
                margin: 0 !important;
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

    @if(!$forPdf && empty($isEmbed))
        <div class="web-preview-bar">
            <div>
                @if(!empty($isSample))
                    <strong>👀 Sample Preview:</strong> Executive Classic ATS Template
                    <span style="background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 3px 10px; border-radius: 9999px; font-size: 11px; margin-left: 8px; font-weight: 800;">Demo Candidate Data</span>
                @else
                    <strong>📄 Classic ATS Template:</strong> {{ $resume->title }}
                    @if(!$isClean)
                        <span style="background: #ef4444; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px; font-weight: 800;">Watermarked</span>
                    @else
                        <span style="background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px; font-weight: 800;">✓ Clean Unlocked</span>
                    @endif
                @endif
            </div>
            <div>
                @if(!empty($isSample))
                    <a href="{{ route('resume-builder.create', ['template' => $resume->template]) }}" style="background: #f59e0b; color: #0f172a; font-weight: 900; box-shadow: 0 4px 12px rgba(245,158,11,0.3);">✨ Use This Template Now →</a>
                    <a href="{{ route('resume-builder.index') }}" style="background: #334155; color: #fff;">← Back to Templates</a>
                @else
                    <a href="{{ route('resume-builder.edit', $resume->id) }}" style="background: #334155; color: #fff;">✏️ Edit CV</a>
                    <button type="button" onclick="window.print()" style="background: #f59e0b; color: #0f172a; padding: 7px 16px; border-radius: 8px; font-weight: 800; border: none; cursor: pointer; margin-left: 8px;">📥 Download / Save PDF</button>
                @endif
            </div>
        </div>
    @endif

    @php $isKo = ($resume->language ?? 'en') === 'ko'; @endphp

    <div class="page">
        {{-- WATERMARK --}}
        @if(!$isClean)
            <div class="watermark">
                {{ $setting->watermark_text ?: 'REIAC COMMUNITY' }}
                <span class="watermark-sub">{{ $isKo ? '워터마크 제거를 위해 공유하세요 • reiac.org' : 'Share to Remove Watermark • reiac.org' }}</span>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="header">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <tr>
                    <td style="vertical-align: middle; text-align: {{ !empty($resume->personal_info['photo_url']) ? 'left' : 'center' }};">
                        <h1 class="name">{{ $resume->personal_info['full_name'] ?? ($isKo ? '성명 미입력' : 'Your Name') }}</h1>
                        @if(!empty($resume->personal_info['job_title']))
                            <div class="title">{{ $resume->personal_info['job_title'] }}</div>
                        @endif
                        <div class="contact">
                            @if(!empty($resume->personal_info['location'])) {{ $resume->personal_info['location'] }} • @endif
                            @if(!empty($resume->personal_info['email'])) {{ $resume->personal_info['email'] }} • @endif
                            @if(!empty($resume->personal_info['phone'])) {{ $resume->personal_info['phone'] }} • @endif
                            @if(!empty($resume->personal_info['linkedin'])) {{ $resume->personal_info['linkedin'] }} @endif
                        </div>
                    </td>
                    @if(!empty($resume->personal_info['photo_url']))
                        <td style="width: 85px; vertical-align: middle; text-align: right; padding-left: 14px;">
                            <img src="{{ $resume->personal_info['photo_url'] }}" style="width: 78px; height: 98px; object-fit: cover; border: 1px solid #111;" alt="Candidate Photo">
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        {{-- OBJECTIVE / SUMMARY --}}
        @if(!empty($resume->summary))
            <div class="section">
                <div class="section-title">{{ $isKo ? '전문 요약 (Professional Summary)' : 'Professional Summary' }}</div>
                <p style="font-size: 10pt; text-align: justify;">{{ $resume->summary }}</p>
            </div>
        @endif

        {{-- EXPERIENCE --}}
        @if(!empty($resume->experience) && count($resume->experience) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '경력 사항 (Work Experience)' : 'Professional Experience' }}</div>
                @foreach($resume->experience as $exp)
                    <div class="entry">
                        <div class="row">
                            <div class="left">{{ $exp['title'] ?? ($exp['position'] ?? '') }}, {{ $exp['company'] ?? '' }}</div>
                            <div class="right">{{ $exp['start_date'] ?? '' }} – {{ !empty($exp['is_current']) ? ($isKo ? '현재 재직중' : 'Present') : ($exp['end_date'] ?? '') }}</div>
                            <div class="clear"></div>
                        </div>
                        @if(!empty($exp['location']))
                            <div class="sub-left">{{ $exp['location'] }}</div>
                        @endif

                        @if(!empty($exp['points']) && is_array($exp['points']) && count(array_filter($exp['points'], 'strlen')) > 0)
                            <ul>
                                @foreach($exp['points'] as $pt)
                                    @if(!empty(trim($pt)))
                                        <li>{{ trim($pt) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @elseif(!empty($exp['rawNotes']))
                            <p style="font-size: 9.5pt; text-align: justify; margin-top: 2px; white-space: pre-line;">{{ $exp['rawNotes'] }}</p>
                        @elseif(!empty($exp['description']))
                            <p style="font-size: 9.5pt; text-align: justify; margin-top: 2px;">{{ $exp['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- EDUCATION --}}
        @if(!empty($resume->education) && count($resume->education) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '학력 사항 (Education)' : 'Education' }}</div>
                @foreach($resume->education as $edu)
                    <div class="entry">
                        <div class="row">
                            <div class="left">{{ $edu['institution'] ?? '' }}</div>
                            <div class="right">{{ $edu['start_date'] ?? '' }} – {{ !empty($edu['is_current']) ? ($isKo ? '재학중' : 'Present') : ($edu['end_date'] ?? '') }}</div>
                            <div class="clear"></div>
                        </div>
                        <div class="sub-left">{{ $edu['degree'] ?? '' }} {{ !empty($edu['field']) ? ($isKo ? ' (' . $edu['field'] . ')' : 'in ' . $edu['field']) : '' }} {{ !empty($edu['grade']) ? ($isKo ? '(학점: ' . $edu['grade'] . ')' : '(GPA: ' . $edu['grade'] . ')') : '' }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- SKILLS --}}
        @if(!empty($resume->skills) && count($resume->skills) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '보유 기술 및 핵심 역량 (Skills & Core Competencies)' : 'Skills & Core Competencies' }}</div>
                <p style="font-size: 10pt;">
                    <strong>{{ $isKo ? '보유 기술 및 전문 지식:' : 'Technical & Professional Skills:' }}</strong>
                    {{ implode(', ', array_filter($resume->skills)) }}
                </p>
            </div>
        @endif

        {{-- PROJECTS --}}
        @if(!empty($resume->projects) && count($resume->projects) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '주요 프로젝트 (Key Projects)' : 'Key Projects' }}</div>
                @foreach($resume->projects as $proj)
                    <div class="entry">
                        <div class="row">
                            <div class="left">{{ $proj['title'] ?? '' }}</div>
                            @if(!empty($proj['url']))
                                <div class="right">{{ $proj['url'] }}</div>
                            @endif
                            <div class="clear"></div>
                        </div>
                        @if(!empty($proj['tech']))
                            <div class="sub-left">{{ $isKo ? '사용 기술:' : 'Tools:' }} {{ $proj['tech'] }}</div>
                        @endif
                        @if(!empty($proj['description']))
                            <p style="font-size: 9.5pt; text-align: justify;">{{ $proj['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CERTIFICATIONS & LICENSES --}}
        @if(!empty($resume->certifications) && count($resume->certifications) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '자격증 및 면허 (Certifications & Licenses)' : 'Certifications & Licenses' }}</div>
                @foreach($resume->certifications as $cert)
                    <div class="entry">
                        <div class="row">
                            <div class="left">{{ $cert['name'] ?? '' }}</div>
                            @if(!empty($cert['date']) || !empty($cert['year']))
                                <div class="right">{{ $cert['date'] ?? $cert['year'] }}</div>
                            @endif
                            <div class="clear"></div>
                        </div>
                        @if(!empty($cert['issuer']))
                            <div class="sub-left">{{ $cert['issuer'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- FORMAL DECLARATION, DATE & SIGNATURE BLOCK --}}
        <div class="declaration-block" style="margin-top: 18px; padding-top: 10px; border-top: 1px solid #cbd5e1; font-size: 8.5pt; color: #334155; page-break-inside: avoid;">
            <div style="font-style: italic; margin-bottom: 8px; color: #475569;">
                {{ $isKo ? '위 기재사항은 사실과 틀림없음을 확인합니다.' : 'I hereby declare that all the information furnished above is true and complete to the best of my knowledge.' }}
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-top: 4px;">
                <tr>
                    <td style="text-align: left; vertical-align: bottom; font-size: 8.5pt; color: #475569;">
                        <strong>{{ $isKo ? '작성일자 :' : 'Date :' }}</strong>
                        <span style="margin-left: 4px;">{{ $resume->personal_info['declaration_date'] ?? date($isKo ? 'Y년 m월 d일' : 'F d, Y') }}</span>
                    </td>
                    <td style="text-align: right; vertical-align: bottom; font-size: 8.5pt;">
                        <strong>{{ $isKo ? '지원자(작성자) :' : 'Applicant Signature :' }}</strong>
                        <span style="display: inline-block; border-bottom: 1px solid #334155; min-width: 120px; text-align: center; font-weight: 700; color: #0f172a; padding: 0 8px; margin: 0 4px;">
                            {{ $resume->personal_info['signature_name'] ?? ($resume->personal_info['full_name'] ?? ($isKo ? '성명' : 'Candidate Name')) }}
                        </span>
                        <span style="font-size: 7.5pt; color: #64748b;">{{ $isKo ? '(인 / 서명)' : '(Signed)' }}</span>
                    </td>
                </tr>
            </table>
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
