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
            font-family: 'Malgun Gothic', 'Apple SD Gothic Neo', 'Noto Sans KR', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 11pt;
            line-height: 1.45;
        }
        .page {
            position: relative;
            padding: 30px 36px;
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
            font-size: 38pt;
            font-weight: 900;
            color: rgba(180, 190, 205, 0.22);
            text-transform: uppercase;
            letter-spacing: 4px;
            transform: rotate(-32deg);
            z-index: 99999;
            pointer-events: none;
            line-height: 1.2;
        }
        .watermark-sub {
            font-size: 15pt;
            display: block;
            margin-top: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            color: rgba(180, 190, 205, 0.28);
        }

        /* Header */
        .header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .candidate-name {
            font-size: 24pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .candidate-title {
            font-size: 13pt;
            font-weight: 700;
            color: #b45309;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .contact-row {
            margin-top: 8px;
            font-size: 9pt;
            color: #475569;
        }
        .contact-item {
            display: inline-block;
            margin-right: 14px;
        }

        /* Section */
        .section {
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 9px;
        }

        /* Summary */
        .summary-text {
            font-size: 10pt;
            color: #334155;
            line-height: 1.5;
            text-align: justify;
        }

        /* Experience Item */
        .item {
            margin-bottom: 12px;
        }
        .item-header {
            width: 100%;
            margin-bottom: 3px;
        }
        .item-title {
            font-size: 10.5pt;
            font-weight: 700;
            color: #0f172a;
            float: left;
        }
        .item-dates {
            font-size: 9pt;
            font-weight: 700;
            color: #64748b;
            float: right;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .item-subtitle {
            font-size: 9.5pt;
            font-weight: 600;
            color: #b45309;
            margin-bottom: 4px;
        }

        /* Bullet Points */
        ul.bullet-list {
            margin-left: 18px;
            margin-top: 3px;
        }
        ul.bullet-list li {
            font-size: 9.5pt;
            color: #334155;
            margin-bottom: 3px;
            line-height: 1.4;
        }

        /* Skills badges */
        .skills-container {
            margin-top: 4px;
        }
        .skill-tag {
            display: inline-block;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 600;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* Web print preview bar */
        @media screen {
            body {
                background: {{ !empty($isEmbed) ? '#0F172A' : '#f1f5f9' }};
                padding: {{ !empty($isEmbed) ? '28px 16px' : '24px 12px' }};
            }
            .page {
                box-shadow: {{ !empty($isEmbed) ? '0 25px 50px -12px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.08)' : '0 10px 30px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0,0,0,0.06)' }};
                border-radius: 8px;
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
                align-items: center;
                justify-content: space-between;
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
        @media print {
            .web-preview-bar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .page {
                box-shadow: none !important;
                padding: 24px 30px !important;
            }
        }
    </style>
</head>
<body>

    @if(!$forPdf && empty($isEmbed))
        <div class="web-preview-bar">
            <div>
                @if(!empty($isSample))
                    <strong>👀 Sample Preview:</strong> Modern Professional Template
                    <span style="background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 3px 10px; border-radius: 9999px; font-size: 11px; margin-left: 8px; font-weight: 800;">Demo Candidate Data</span>
                @else
                    <strong>📄 Resume Live Preview:</strong> {{ $resume->title }}
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
                    <a href="{{ route('resume-builder.download', $resume->id) }}">📥 Download PDF</a>
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
                    <td style="vertical-align: middle;">
                        <h1 class="candidate-name">{{ $resume->personal_info['full_name'] ?? ($isKo ? '성명 미입력' : 'Your Full Name') }}</h1>
                        @if(!empty($resume->personal_info['job_title']))
                            <div class="candidate-title">{{ $resume->personal_info['job_title'] }}</div>
                        @endif

                        <div class="contact-row">
                            @if(!empty($resume->personal_info['email']))
                                <span class="contact-item">✉ {{ $resume->personal_info['email'] }}</span>
                            @endif
                            @if(!empty($resume->personal_info['phone']))
                                <span class="contact-item">📞 {{ $resume->personal_info['phone'] }}</span>
                            @endif
                            @if(!empty($resume->personal_info['location']))
                                <span class="contact-item">📍 {{ $resume->personal_info['location'] }}</span>
                            @endif
                            @if(!empty($resume->personal_info['linkedin']))
                                <span class="contact-item">🔗 {{ $resume->personal_info['linkedin'] }}</span>
                            @endif
                            @if(!empty($resume->personal_info['github']))
                                <span class="contact-item">💻 {{ $resume->personal_info['github'] }}</span>
                            @endif
                        </div>
                    </td>
                    @if(!empty($resume->personal_info['photo_url']))
                        <td style="width: 85px; vertical-align: middle; text-align: right; padding-left: 14px;">
                            <img src="{{ $resume->personal_info['photo_url'] }}" style="width: 78px; height: 98px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;" alt="Candidate Photo">
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        {{-- PROFESSIONAL SUMMARY --}}
        @if(!empty($resume->summary))
            <div class="section">
                <div class="section-title">{{ $isKo ? '전문 요약 (Professional Summary)' : 'Professional Summary' }}</div>
                <div class="summary-text">{{ $resume->summary }}</div>
            </div>
        @endif

        {{-- WORK EXPERIENCE --}}
        @if(!empty($resume->experience) && count($resume->experience) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '경력 사항 (Work Experience)' : 'Professional Experience' }}</div>
                @foreach($resume->experience as $exp)
                    <div class="item">
                        <div class="item-header">
                            <div class="item-title">{{ $exp['title'] ?? ($exp['position'] ?? ($isKo ? '담당 직무' : 'Role')) }}</div>
                            <div class="item-dates">
                                {{ $exp['start_date'] ?? '' }} — {{ !empty($exp['is_current']) ? ($isKo ? '현재 재직중' : 'Present') : ($exp['end_date'] ?? '') }}
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="item-subtitle">
                            {{ $exp['company'] ?? '' }} {{ !empty($exp['location']) ? '• ' . $exp['location'] : '' }}
                        </div>

                        @if(!empty($exp['points']) && is_array($exp['points']))
                            <ul class="bullet-list">
                                @foreach($exp['points'] as $pt)
                                    @if(!empty(trim($pt)))
                                        <li>{{ trim($pt) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @elseif(!empty($exp['description']))
                            <div class="summary-text">{{ $exp['description'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- EDUCATION --}}
        @if(!empty($resume->education) && count($resume->education) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '학력 사항 (Education)' : 'Education & Qualifications' }}</div>
                @foreach($resume->education as $edu)
                    <div class="item">
                        <div class="item-header">
                            <div class="item-title">{{ $edu['degree'] ?? ($isKo ? '학위' : 'Degree') }} {{ !empty($edu['field']) ? ($isKo ? ' (' . $edu['field'] . ')' : 'in ' . $edu['field']) : '' }}</div>
                            <div class="item-dates">
                                {{ $edu['start_date'] ?? '' }} — {{ !empty($edu['is_current']) ? ($isKo ? '재학중' : 'Present') : ($edu['end_date'] ?? '') }}
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="item-subtitle">
                            {{ $edu['institution'] ?? '' }} {{ !empty($edu['grade']) ? ($isKo ? '• 학점/성적: ' . $edu['grade'] : '• GPA/Score: ' . $edu['grade']) : '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- SKILLS --}}
        @if(!empty($resume->skills) && count($resume->skills) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '보유 기술 및 역량 (Skills & Competencies)' : 'Skills & Competencies' }}</div>
                <div class="skills-container">
                    @foreach($resume->skills as $skill)
                        @if(!empty(trim($skill)))
                            <span class="skill-tag">{{ trim($skill) }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PROJECTS --}}
        @if(!empty($resume->projects) && count($resume->projects) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '주요 프로젝트 (Key Projects)' : 'Key Projects' }}</div>
                @foreach($resume->projects as $proj)
                    <div class="item">
                        <div class="item-header">
                            <div class="item-title">{{ $proj['title'] ?? ($isKo ? '프로젝트' : 'Project') }}</div>
                            @if(!empty($proj['url']))
                                <div class="item-dates">{{ $proj['url'] }}</div>
                            @endif
                            <div class="clear"></div>
                        </div>
                        @if(!empty($proj['tech']))
                            <div class="item-subtitle">{{ $isKo ? '사용 기술:' : 'Technologies:' }} {{ $proj['tech'] }}</div>
                        @endif
                        @if(!empty($proj['description']))
                            <div class="summary-text">{{ $proj['description'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CERTIFICATIONS & LANGUAGES --}}
        @if(!empty($resume->certifications) && count($resume->certifications) > 0)
            <div class="section">
                <div class="section-title">{{ $isKo ? '자격증 및 면허 (Certifications)' : 'Certifications' }}</div>
                @foreach($resume->certifications as $cert)
                    <div class="item">
                        <div class="item-header">
                            <div class="item-title">{{ $cert['name'] ?? '' }}</div>
                            @if(!empty($cert['date']) || !empty($cert['year']))
                                <div class="item-dates">{{ $cert['date'] ?? $cert['year'] }}</div>
                            @endif
                            <div class="clear"></div>
                        </div>
                        @if(!empty($cert['issuer']))
                            <div class="item-subtitle">{{ $cert['issuer'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</body>
</html>
