<!DOCTYPE html>
<html lang="{{ ($resume->language ?? 'en') === 'ko' ? 'ko' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resume->title }} - {{ $resume->personal_info['full_name'] ?? 'Candidate' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans KR', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 10pt;
            line-height: 1.45;
            -webkit-font-smoothing: antialiased;
        }

        .page {
            position: relative;
            width: 100%;
            max-width: 840px;
            margin: 0 auto;
            background: #ffffff;
        }

        /* Watermark (for unverified user drafts) */
        .watermark {
            position: fixed;
            top: 40%;
            left: 5%;
            width: 90%;
            text-align: center;
            font-size: 38pt;
            font-weight: 900;
            color: rgba(130, 100, 220, 0.16);
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
            color: rgba(130, 100, 220, 0.22);
        }

        /* High-Impact Creative Header Banner */
        .top-banner {
            background: linear-gradient(135deg, #091124 0%, #0F1C3F 65%, #182C5E 100%);
            color: #ffffff;
            padding: 26px 32px 22px 32px;
            border-bottom: 3.5px solid #F59E0B;
        }
        .banner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .name {
            font-size: 24pt;
            font-weight: 900;
            color: #FFFFFF;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }
        .name span.highlight {
            color: #F59E0B;
        }
        .role-badge {
            display: inline-block;
            background: rgba(245, 158, 11, 0.16);
            color: #FBBF24;
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-top: 6px;
            text-transform: uppercase;
        }
        .contact-strip {
            margin-top: 12px;
            font-size: 8.5pt;
            color: #CBD5E1;
            line-height: 1.6;
        }
        .contact-item {
            display: inline-block;
            margin-right: 14px;
            white-space: nowrap;
        }
        .contact-icon {
            color: #F59E0B;
            font-weight: 800;
            margin-right: 4px;
        }
        .photo-frame {
            width: 88px;
            height: 106px;
            border-radius: 10px;
            border: 2.5px solid #F59E0B;
            object-fit: cover;
            box-shadow: 0 8px 18px rgba(0,0,0,0.35);
        }

        /* 2-Column Body Layout via Table */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .col-sidebar {
            width: 33%;
            vertical-align: top;
            background: #F8FAFC;
            border-right: 1.5px solid #E2E8F0;
            padding: 22px 18px;
        }
        .col-main {
            width: 67%;
            vertical-align: top;
            background: #FFFFFF;
            padding: 22px 26px;
        }

        /* Section Headings */
        .sidebar-title {
            font-size: 9.5pt;
            font-weight: 800;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 5px;
            border-bottom: 2px solid #0F172A;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        .sidebar-title .accent-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #F59E0B;
            border-radius: 50%;
            margin-right: 6px;
        }

        .main-title {
            font-size: 11pt;
            font-weight: 900;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 6px;
            border-bottom: 2px solid #E2E8F0;
            margin-bottom: 14px;
            position: relative;
        }
        .main-title::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 48px;
            height: 2px;
            background: #F59E0B;
        }

        /* Sidebar Elements */
        .side-block {
            margin-bottom: 18px;
        }
        .skill-pill {
            display: inline-block;
            background: #0F172A;
            color: #F8FAFC;
            border: 1px solid #1E293B;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 8pt;
            font-weight: 700;
            margin-right: 4px;
            margin-bottom: 5px;
        }
        .side-item {
            margin-bottom: 10px;
        }
        .side-item-title {
            font-size: 9pt;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.25;
        }
        .side-item-sub {
            font-size: 8pt;
            color: #475569;
            margin-top: 1px;
        }
        .side-item-date {
            font-size: 7.5pt;
            font-weight: 700;
            color: #64748B;
            margin-top: 2px;
        }
        .side-badge {
            display: inline-block;
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Main Elements */
        .summary-box {
            background: #F8FAFC;
            border-left: 3.5px solid #F59E0B;
            border-radius: 0 8px 8px 0;
            padding: 12px 14px;
            font-size: 9.5pt;
            color: #334155;
            line-height: 1.55;
            margin-bottom: 18px;
            text-align: justify;
        }

        .experience-item {
            position: relative;
            padding-left: 16px;
            margin-bottom: 16px;
            border-left: 2px solid #E2E8F0;
        }
        .experience-item::before {
            content: "";
            position: absolute;
            left: -5.5px;
            top: 4px;
            width: 9px;
            height: 9px;
            background: #F59E0B;
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            box-shadow: 0 0 0 1px #F59E0B;
        }
        .item-header {
            width: 100%;
            margin-bottom: 3px;
        }
        .item-role {
            font-size: 10.5pt;
            font-weight: 800;
            color: #0F172A;
        }
        .item-company {
            font-size: 9pt;
            font-weight: 700;
            color: #D97706;
            margin-top: 1px;
        }
        .date-pill {
            float: right;
            background: #F1F5F9;
            color: #475569;
            font-size: 8pt;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            border: 1px solid #E2E8F0;
        }
        .clear { clear: both; }

        .points-list {
            list-style: none;
            margin-top: 5px;
            padding-left: 0;
        }
        .points-list li {
            position: relative;
            padding-left: 14px;
            font-size: 9pt;
            color: #334155;
            line-height: 1.45;
            margin-bottom: 3.5px;
        }
        .points-list li::before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #F59E0B;
            font-weight: 900;
            font-size: 8pt;
        }

        .project-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }
        .project-title {
            font-size: 9.5pt;
            font-weight: 800;
            color: #0F172A;
        }
        .project-tech {
            display: inline-block;
            background: #E2E8F0;
            color: #334155;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            margin-left: 6px;
        }
        .project-desc {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 4px;
            line-height: 1.4;
        }

        /* Screen Styling & Standalone Header */
        @media screen {
            body {
                background: {{ !empty($isEmbed) ? '#0B1222' : '#070D1E' }};
                padding: {{ !empty($isEmbed) ? '20px 10px' : '24px 16px 40px 16px' }};
            }
            .page {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
                border-radius: 10px;
                overflow: hidden;
                background: #ffffff;
            }

            /* Responsive Standalone Preview Bar */
            .web-preview-bar {
                max-width: 840px;
                margin: 0 auto 16px auto;
                background: #0B142B;
                border: 1px solid #1E293B;
                color: #FFFFFF;
                padding: 12px 18px;
                border-radius: 14px;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                font-size: 13px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            }
            .preview-meta {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }
            .preview-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }
            .btn-use-tpl {
                background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
                color: #0B1222;
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 900;
                font-size: 12px;
                text-decoration: none;
                letter-spacing: 0.3px;
                box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
                transition: transform 0.15s ease;
                white-space: nowrap;
            }
            .btn-use-tpl:hover {
                transform: translateY(-1px);
            }
            .btn-back {
                background: #1E293B;
                color: #CBD5E1;
                padding: 8px 14px;
                border-radius: 8px;
                font-weight: 700;
                font-size: 12px;
                text-decoration: none;
                border: 1px solid #334155;
                transition: background 0.15s ease;
                white-space: nowrap;
            }
            .btn-back:hover {
                background: #334155;
                color: #FFFFFF;
            }
            .lang-pill-link {
                background: #1E293B;
                color: #94A3B8;
                padding: 5px 10px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 700;
                text-decoration: none;
                border: 1px solid #334155;
            }
            .lang-pill-link.active {
                background: #F59E0B;
                color: #0F172A;
                border-color: #F59E0B;
            }
        }

        @media print {
            .web-preview-bar { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; }
            .page { box-shadow: none !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body>

    @php
        $isKo = ($resume->language ?? 'en') === 'ko';
        $pInfo = $resume->personal_info ?? [];
    @endphp

    @if(!$forPdf && empty($isEmbed))
        <div class="web-preview-bar">
            <div class="preview-meta">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: rgba(245,158,11,0.2); color: #F59E0B; font-weight: 900;">🚀</span>
                <div>
                    <strong>{{ $isKo ? '크리에이티브 임팩트 템플릿' : 'Creative & Impact Template' }}</strong>
                    <span style="background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 2px 8px; border-radius: 9999px; font-size: 10px; margin-left: 6px; font-weight: 800;">
                        {{ !empty($isSample) ? 'Sample Preview' : 'Resume Preview' }}
                    </span>
                </div>
            </div>

            <div class="preview-actions">
                {{-- Language Toggle for Sample Preview --}}
                @if(!empty($isSample))
                    <div style="display: inline-flex; gap: 4px; background: #070D1E; padding: 3px; border-radius: 8px; border: 1px solid #1E293B;">
                        <a href="{{ route('resume-builder.sample', ['template' => 'creative', 'lang' => 'ko']) }}" class="lang-pill-link {{ $isKo ? 'active' : '' }}">🇰🇷 KR</a>
                        <a href="{{ route('resume-builder.sample', ['template' => 'creative', 'lang' => 'en']) }}" class="lang-pill-link {{ !$isKo ? 'active' : '' }}">🌐 EN</a>
                    </div>
                @endif

                @if(!empty($isSample))
                    <a href="{{ route('resume-builder.create', ['template' => 'creative', 'language' => $isKo ? 'ko' : 'en']) }}" class="btn-use-tpl">
                        ✨ {{ $isKo ? '이 템플릿으로 시작하기' : 'Use This Template' }} →
                    </a>
                    <a href="{{ route('resume-builder.index') }}" class="btn-back">
                        ← {{ $isKo ? '템플릿 목록' : 'Templates' }}
                    </a>
                @else
                    <a href="{{ route('resume-builder.edit', $resume->id) }}" class="btn-back">✏️ Edit CV</a>
                    <a href="{{ route('resume-builder.download', $resume->id) }}" class="btn-use-tpl">📥 Download PDF</a>
                @endif
            </div>
        </div>
    @endif

    <div class="page">
        {{-- WATERMARK (Shown only for unverified user drafts) --}}
        @if(!$isClean)
            <div class="watermark">
                {{ $setting->watermark_text ?: 'REIAC COMMUNITY' }}
                <span class="watermark-sub">{{ $isKo ? '워터마크 제거를 위해 공유하세요 • reiac.org' : 'Share to Remove Watermark • reiac.org' }}</span>
            </div>
        @endif

        {{-- EXECUTIVE TOP BANNER --}}
        <div class="top-banner">
            <table class="banner-table">
                <tr>
                    <td style="vertical-align: middle;">
                        <h1 class="name">
                            {{ $pInfo['full_name'] ?? ($isKo ? '지원자 성명' : 'Alex Johnson') }}
                        </h1>
                        @if(!empty($pInfo['job_title']))
                            <div>
                                <span class="role-badge">{{ $pInfo['job_title'] }}</span>
                            </div>
                        @endif

                        <div class="contact-strip">
                            @if(!empty($pInfo['email']))
                                <span class="contact-item"><span class="contact-icon">✉</span> {{ $pInfo['email'] }}</span>
                            @endif
                            @if(!empty($pInfo['phone']))
                                <span class="contact-item"><span class="contact-icon">📞</span> {{ $pInfo['phone'] }}</span>
                            @endif
                            @if(!empty($pInfo['location']))
                                <span class="contact-item"><span class="contact-icon">📍</span> {{ $pInfo['location'] }}</span>
                            @endif
                            @if(!empty($pInfo['linkedin']))
                                <span class="contact-item"><span class="contact-icon">🔗</span> {{ $pInfo['linkedin'] }}</span>
                            @endif
                        </div>
                    </td>
                    @if(!empty($pInfo['photo_url']))
                        <td style="width: 95px; vertical-align: middle; text-align: right; padding-left: 16px;">
                            <img src="{{ $pInfo['photo_url'] }}" class="photo-frame" alt="Candidate Photo">
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        {{-- 2-COLUMN STRUCTURE --}}
        <table class="layout-table">
            <tr>
                {{-- LEFT SIDEBAR (33%): SKILLS, EDUCATION, CERTIFICATIONS, LANGUAGES --}}
                <td class="col-sidebar">

                    {{-- ONLINE PROFILES (GITHUB / PORTFOLIO) --}}
                    @if(!empty($pInfo['github']) || !empty($pInfo['portfolio']))
                        <div class="side-block">
                            <div class="sidebar-title"><span class="accent-dot"></span> {{ $isKo ? '온라인 프로필' : 'Links & Portfolio' }}</div>
                            @if(!empty($pInfo['portfolio']))
                                <div class="side-item">
                                    <div class="side-item-title">🌐 Portfolio</div>
                                    <div class="side-item-sub">{{ $pInfo['portfolio'] }}</div>
                                </div>
                            @endif
                            @if(!empty($pInfo['github']))
                                <div class="side-item">
                                    <div class="side-item-title">💻 GitHub</div>
                                    <div class="side-item-sub">{{ $pInfo['github'] }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- SKILLS & TECHNOLOGIES --}}
                    @if(!empty($resume->skills) && count($resume->skills) > 0)
                        <div class="side-block">
                            <div class="sidebar-title"><span class="accent-dot"></span> {{ $isKo ? '보유 기술 역량' : 'Skills & Stack' }}</div>
                            <div>
                                @foreach($resume->skills as $skill)
                                    @if(!empty(trim($skill)))
                                        <span class="skill-pill">{{ trim($skill) }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- EDUCATION --}}
                    @if(!empty($resume->education) && count($resume->education) > 0)
                        <div class="side-block">
                            <div class="sidebar-title"><span class="accent-dot"></span> {{ $isKo ? '학력 사항' : 'Education' }}</div>
                            @foreach($resume->education as $edu)
                                <div class="side-item">
                                    <div class="side-item-title">{{ $edu['degree'] ?? '' }} {{ !empty($edu['field']) ? ($isKo ? ' (' . $edu['field'] . ')' : 'in ' . $edu['field']) : '' }}</div>
                                    <div class="side-item-sub">{{ $edu['institution'] ?? '' }}</div>
                                    <div class="side-item-date">{{ $edu['start_date'] ?? '' }} — {{ !empty($edu['is_current']) ? ($isKo ? '재학중' : 'Present') : ($edu['end_date'] ?? '') }}</div>
                                    @if(!empty($edu['grade']))
                                        <div class="side-badge">{{ $edu['grade'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- CERTIFICATIONS --}}
                    @if(!empty($resume->certifications) && count($resume->certifications) > 0)
                        <div class="side-block">
                            <div class="sidebar-title"><span class="accent-dot"></span> {{ $isKo ? '자격 및 면허' : 'Certifications' }}</div>
                            @foreach($resume->certifications as $cert)
                                <div class="side-item">
                                    <div class="side-item-title">{{ $cert['name'] ?? '' }}</div>
                                    <div class="side-item-sub">{{ $cert['issuer'] ?? '' }}</div>
                                    @if(!empty($cert['date']) || !empty($cert['year']))
                                        <div class="side-item-date">{{ $cert['date'] ?? $cert['year'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- LANGUAGES --}}
                    @if(!empty($resume->languages) && count($resume->languages) > 0)
                        <div class="side-block">
                            <div class="sidebar-title"><span class="accent-dot"></span> {{ $isKo ? '어학 능력' : 'Languages' }}</div>
                            @foreach($resume->languages as $lang)
                                <div class="side-item">
                                    <div class="side-item-title">{{ is_array($lang) ? ($lang['language'] ?? '') : $lang }}</div>
                                    @if(is_array($lang) && !empty($lang['proficiency']))
                                        <div class="side-badge">{{ $lang['proficiency'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </td>

                {{-- RIGHT MAIN COLUMN (67%): SUMMARY, EXPERIENCE, PROJECTS --}}
                <td class="col-main">

                    {{-- PROFESSIONAL SUMMARY --}}
                    @if(!empty($resume->summary))
                        <div style="margin-bottom: 20px;">
                            <div class="main-title">{{ $isKo ? '전문 분야 및 역량 요약' : 'Executive Profile' }}</div>
                            <div class="summary-box">
                                {{ $resume->summary }}
                            </div>
                        </div>
                    @endif

                    {{-- WORK EXPERIENCE --}}
                    @if(!empty($resume->experience) && count($resume->experience) > 0)
                        <div style="margin-bottom: 20px;">
                            <div class="main-title">{{ $isKo ? '주요 경력 사항' : 'Work Experience' }}</div>
                            @foreach($resume->experience as $exp)
                                <div class="experience-item">
                                    <div class="item-header">
                                        <span class="date-pill">{{ $exp['start_date'] ?? '' }} — {{ !empty($exp['is_current']) ? ($isKo ? '현재 재직중' : 'Present') : ($exp['end_date'] ?? '') }}</span>
                                        <div class="item-role">{{ $exp['title'] ?? ($exp['position'] ?? ($exp['role'] ?? '')) }}</div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="item-company">
                                        {{ $exp['company'] ?? '' }}
                                        @if(!empty($exp['location']))
                                            <span style="color: #64748B; font-weight: 500;">• {{ $exp['location'] }}</span>
                                        @endif
                                    </div>

                                    @if(!empty($exp['points']) && is_array($exp['points']))
                                        <ul class="points-list">
                                            @foreach($exp['points'] as $point)
                                                @if(!empty(trim($point)))
                                                    <li>{{ trim($point) }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @elseif(!empty($exp['responsibilities']))
                                        <p style="font-size: 9pt; color: #334155; margin-top: 5px; line-height: 1.45;">{{ $exp['responsibilities'] }}</p>
                                    @elseif(!empty($exp['description']))
                                        <p style="font-size: 9pt; color: #334155; margin-top: 5px; line-height: 1.45;">{{ $exp['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- FEATURED PROJECTS --}}
                    @if(!empty($resume->projects) && count($resume->projects) > 0)
                        <div style="margin-bottom: 14px;">
                            <div class="main-title">{{ $isKo ? '주요 프로젝트 및 성과' : 'Featured Projects' }}</div>
                            @foreach($resume->projects as $proj)
                                <div class="project-card">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <span class="project-title">{{ $proj['title'] ?? '' }}</span>
                                        @if(!empty($proj['tech']))
                                            <span class="project-tech">{{ $proj['tech'] }}</span>
                                        @endif
                                    </div>
                                    @if(!empty($proj['description']))
                                        <div class="project-desc">{{ $proj['description'] }}</div>
                                    @endif
                                    @if(!empty($proj['url']))
                                        <div style="font-size: 8pt; color: #2563EB; margin-top: 4px; font-weight: 600;">🔗 {{ $proj['url'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </td>
            </tr>
        </table>
    </div>

</body>
</html>
