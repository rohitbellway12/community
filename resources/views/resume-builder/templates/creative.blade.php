<!DOCTYPE html>
<html lang="{{ ($resume->language ?? 'en') === 'ko' ? 'ko' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $resume->title }} - {{ $resume->personal_info['full_name'] ?? 'Candidate' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Malgun Gothic', 'Apple SD Gothic Neo', 'Noto Sans KR', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 10.5pt;
            line-height: 1.4;
        }
        .page {
            position: relative;
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
            color: rgba(130, 100, 220, 0.22);
            text-transform: uppercase;
            letter-spacing: 4px;
            transform: rotate(-30deg);
            z-index: 99999;
            pointer-events: none;
            line-height: 1.2;
        }
        .watermark-sub {
            font-size: 14pt;
            display: block;
            margin-top: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            color: rgba(130, 100, 220, 0.28);
        }

        /* Header Banner */
        .top-banner {
            background: #0f172a;
            color: #ffffff;
            padding: 24px 30px;
        }
        .name {
            font-size: 22pt;
            font-weight: 800;
            color: #f59e0b;
            letter-spacing: 0.5px;
        }
        .title {
            font-size: 12pt;
            font-weight: 600;
            color: #94a3b8;
            margin-top: 2px;
        }
        .contact {
            margin-top: 8px;
            font-size: 9pt;
            color: #cbd5e1;
        }
        .contact span {
            display: inline-block;
            margin-right: 14px;
        }

        .content {
            padding: 22px 30px;
        }
        .section {
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 3px;
            margin-bottom: 9px;
            display: inline-block;
        }

        .item {
            margin-bottom: 12px;
        }
        .item-row {
            width: 100%;
        }
        .item-title {
            float: left;
            font-size: 10.5pt;
            font-weight: 700;
            color: #0f172a;
        }
        .item-dates {
            float: right;
            font-size: 9pt;
            font-weight: 700;
            color: #64748b;
        }
        .clear { clear: both; }
        .item-company {
            font-size: 9.5pt;
            font-weight: 600;
            color: #6366f1;
            margin-bottom: 3px;
        }

        ul {
            margin-left: 18px;
            margin-top: 3px;
        }
        ul li {
            font-size: 9.5pt;
            color: #334155;
            margin-bottom: 2.5px;
        }

        .pill {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 8.5pt;
            font-weight: 700;
            margin-right: 4px;
            margin-bottom: 5px;
        }

        @media screen {
            body {
                background: {{ !empty($isEmbed) ? '#0F172A' : '#f1f5f9' }};
                padding: {{ !empty($isEmbed) ? '28px 16px' : '24px 12px' }};
            }
            .page {
                box-shadow: {{ !empty($isEmbed) ? '0 25px 50px -12px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.08)' : '0 10px 30px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0,0,0,0.06)' }};
                border-radius: 8px;
                overflow: hidden;
                background: #ffffff;
            }
            .web-preview-bar {
                max-width: 820px;
                margin: 0 auto 16px auto;
                background: #0f172a;
                color: #fff;
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
                color: #000;
                padding: 7px 16px;
                border-radius: 8px;
                font-weight: 800;
                text-decoration: none;
                margin-left: 8px;
            }
        }
        @media print {
            .web-preview-bar { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; }
            .page { box-shadow: none !important; }
        }
    </style>
</head>
<body>

    @if(!$forPdf && empty($isEmbed))
        <div class="web-preview-bar">
            <div>
                @if(!empty($isSample))
                    <strong>👀 Sample Preview:</strong> Creative Impact Template
                    <span style="background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 3px 10px; border-radius: 9999px; font-size: 11px; margin-left: 8px; font-weight: 800;">Demo Candidate Data</span>
                @else
                    <strong>📄 Creative Impact Template:</strong> {{ $resume->title }}
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

        {{-- TOP BANNER --}}
        <div class="top-banner">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <tr>
                    <td style="vertical-align: middle;">
                        <h1 class="name">{{ $resume->personal_info['full_name'] ?? ($isKo ? '성명 미입력' : 'Your Full Name') }}</h1>
                        @if(!empty($resume->personal_info['job_title']))
                            <div class="title">{{ $resume->personal_info['job_title'] }}</div>
                        @endif
                        <div class="contact">
                            @if(!empty($resume->personal_info['email'])) <span>✉ {{ $resume->personal_info['email'] }}</span> @endif
                            @if(!empty($resume->personal_info['phone'])) <span>📞 {{ $resume->personal_info['phone'] }}</span> @endif
                            @if(!empty($resume->personal_info['location'])) <span>📍 {{ $resume->personal_info['location'] }}</span> @endif
                            @if(!empty($resume->personal_info['linkedin'])) <span>🔗 {{ $resume->personal_info['linkedin'] }}</span> @endif
                        </div>
                    </td>
                    @if(!empty($resume->personal_info['photo_url']))
                        <td style="width: 85px; vertical-align: middle; text-align: right; padding-left: 14px;">
                            <img src="{{ $resume->personal_info['photo_url'] }}" style="width: 78px; height: 98px; object-fit: cover; border-radius: 8px; border: 2px solid rgba(255,255,255,0.4);" alt="Candidate Photo">
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        <div class="content">

            {{-- SUMMARY --}}
            @if(!empty($resume->summary))
                <div class="section">
                    <div class="section-title">{{ $isKo ? '자기소개 및 요약 (About Me)' : 'About Me' }}</div>
                    <p style="font-size: 9.5pt; color: #334155; line-height: 1.45; text-align: justify;">{{ $resume->summary }}</p>
                </div>
            @endif

            {{-- WORK EXPERIENCE --}}
            @if(!empty($resume->experience) && count($resume->experience) > 0)
                <div class="section">
                    <div class="section-title">{{ $isKo ? '경력 사항 (Experience)' : 'Experience Timeline' }}</div>
                    @foreach($resume->experience as $exp)
                        <div class="item">
                            <div class="item-row">
                                <div class="item-title">{{ $exp['title'] ?? ($exp['position'] ?? '') }}</div>
                                <div class="item-dates">{{ $exp['start_date'] ?? '' }} — {{ !empty($exp['is_current']) ? ($isKo ? '현재 재직중' : 'Present') : ($exp['end_date'] ?? '') }}</div>
                                <div class="clear"></div>
                            </div>
                            <div class="item-company">{{ $exp['company'] ?? '' }} {{ !empty($exp['location']) ? '• ' . $exp['location'] : '' }}</div>

                            @if(!empty($exp['points']) && is_array($exp['points']))
                                <ul>
                                    @foreach($exp['points'] as $pt)
                                        @if(!empty(trim($pt)))
                                            <li>{{ trim($pt) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @elseif(!empty($exp['description']))
                                <p style="font-size: 9.5pt; color: #334155;">{{ $exp['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- SKILLS --}}
            @if(!empty($resume->skills) && count($resume->skills) > 0)
                <div class="section">
                    <div class="section-title">{{ $isKo ? '보유 기술 및 역량 (Skills)' : 'Skills & Technologies' }}</div>
                    <div style="margin-top: 4px;">
                        @foreach($resume->skills as $s)
                            @if(!empty(trim($s)))
                                <span class="pill">{{ trim($s) }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- EDUCATION --}}
            @if(!empty($resume->education) && count($resume->education) > 0)
                <div class="section">
                    <div class="section-title">{{ $isKo ? '학력 사항 (Education)' : 'Education' }}</div>
                    @foreach($resume->education as $edu)
                        <div class="item">
                            <div class="item-row">
                                <div class="item-title">{{ $edu['degree'] ?? '' }} {{ !empty($edu['field']) ? ($isKo ? ' (' . $edu['field'] . ')' : 'in ' . $edu['field']) : '' }}</div>
                                <div class="item-dates">{{ $edu['start_date'] ?? '' }} — {{ !empty($edu['is_current']) ? ($isKo ? '재학중' : 'Present') : ($edu['end_date'] ?? '') }}</div>
                                <div class="clear"></div>
                            </div>
                            <div class="item-company" style="color: #475569;">{{ $edu['institution'] ?? '' }} {{ !empty($edu['grade']) ? ($isKo ? '• 학점: ' . $edu['grade'] : '• Grade: ' . $edu['grade']) : '' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- PROJECTS --}}
            @if(!empty($resume->projects) && count($resume->projects) > 0)
                <div class="section">
                    <div class="section-title">{{ $isKo ? '주요 프로젝트 (Featured Projects)' : 'Featured Projects' }}</div>
                    @foreach($resume->projects as $p)
                        <div class="item">
                            <div class="item-row">
                                <div class="item-title">{{ $p['title'] ?? '' }}</div>
                                @if(!empty($p['url']))
                                    <div class="item-dates">{{ $p['url'] }}</div>
                                @endif
                                <div class="clear"></div>
                            </div>
                            @if(!empty($p['tech']))
                                <div class="item-company" style="color: #64748b;">{{ $isKo ? '기술:' : 'Tech:' }} {{ $p['tech'] }}</div>
                            @endif
                            @if(!empty($p['description']))
                                <p style="font-size: 9pt; color: #334155;">{{ $p['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</body>
</html>
