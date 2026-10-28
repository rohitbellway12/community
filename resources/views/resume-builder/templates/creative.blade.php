<!DOCTYPE html>
<html lang="{{ ($resume->language ?? 'en') === 'ko' ? 'ko' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resume->title }} - {{ $resume->personal_info['full_name'] ?? 'Candidate' }}</title>

    @if(empty($forPdf))
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
    @endif

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans KR', 'DejaVu Sans', 'Malgun Gothic', 'Apple SD Gothic Neo', 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 10pt;
            line-height: 1.45;
            -webkit-font-smoothing: antialiased;
        }

        .page {
            position: relative;
            width: 800px;
            min-width: 800px;
            max-width: 800px;
            min-height: 1130px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            box-sizing: border-box;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .a4-canvas-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: {{ !empty($isEmbed) ? '10px 4px 24px 4px' : '16px 8px 40px 8px' }};
            box-sizing: border-box;
            transition: height 0.2s ease;
        }
        .a4-zoom-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 12px;
            width: 100%;
            max-width: 800px;
        }
        .a4-zoom-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0F172A;
            color: #FFFFFF;
            border: 1px solid #334155;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.15s ease;
        }
        .a4-zoom-pill:hover {
            background: #1E293B;
            border-color: #F59E0B;
            color: #F59E0B;
        }
        .a4-page-badge {
            font-size: 10.5px;
            font-weight: 700;
            color: #64748B;
            background: #E2E8F0;
            padding: 3px 9px;
            border-radius: 9999px;
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

        /* High-Impact Creative Header Banner */
        .top-banner {
            background-color: #0F1C3F;
            background: linear-gradient(135deg, #091124 0%, #0F1C3F 65%, #182C5E 100%);
            color: #ffffff;
            padding: 26px 32px 22px 32px;
            border-bottom: 3.5px solid #F59E0B;
            overflow: hidden;
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
            max-width: 100%;
        }
        .contact-item {
            display: inline-block;
            margin-right: 14px;
            margin-bottom: 2px;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: bottom;
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
            display: block;
            margin-left: auto;
            box-shadow: 0 8px 18px rgba(0,0,0,0.35);
        }

        /* 2-Column Body Layout via Table */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .col-sidebar {
            width: 35%;
            vertical-align: top;
            background: #F8FAFC;
            border-right: 1.5px solid #E2E8F0;
            padding: 20px 14px;
            box-sizing: border-box;
            word-break: break-word;
            overflow-wrap: anywhere;
        }
        .col-main {
            width: 65%;
            vertical-align: top;
            background: #FFFFFF;
            padding: 20px 22px;
            box-sizing: border-box;
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
        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: flex-start;
            width: 100%;
            box-sizing: border-box;
        }
        .skill-pill {
            display: inline-block;
            background: #0F172A;
            color: #F8FAFC;
            border: 1px solid #1E293B;
            padding: 2.5px 7px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            line-height: 1.35;
            max-width: 100%;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            box-sizing: border-box;
            vertical-align: middle;
        }
        .side-item {
            margin-bottom: 10px;
        }
        .side-item-title {
            font-size: 9pt;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.25;
            word-break: break-word;
        }
        .side-item-sub {
            font-size: 8pt;
            color: #475569;
            margin-top: 1px;
            word-break: break-all;
            overflow-wrap: anywhere;
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

        @media screen {
            body {
                background: #f1f5f9;
                padding: {{ !empty($isEmbed) ? '20px 10px' : '24px 16px 40px 16px' }};
            }
            .page {
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
                border: 1px solid #e2e8f0;
                border-radius: 8px;
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

        /* Mobile & Tablet Responsive View - Keeps A4 Proportion through Canvas Auto-Scaling */
        @media screen and (max-width: 768px) {
            body {
                padding: 6px 2px !important;
                background: #f1f5f9 !important;
            }
            .web-preview-bar {
                padding: 10px 12px !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }
            .preview-meta, .preview-actions {
                width: 100% !important;
                justify-content: center !important;
            }
            .web-preview-bar a, .web-preview-bar button {
                display: block !important;
                width: 100% !important;
                margin: 4px 0 !important;
                text-align: center !important;
                box-sizing: border-box !important;
            }
            .watermark {
                font-size: 24pt !important;
            }
            .watermark-sub {
                font-size: 10pt !important;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            .web-preview-bar, .a4-zoom-bar, .no-print { display: none !important; }
            .a4-canvas-container { padding: 0 !important; height: auto !important; width: 100% !important; }
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
                border-radius: 0 !important;
                border: none !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
                transform: none !important;
            }
            .top-banner {
                padding: 26px 32px 22px 32px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background: linear-gradient(135deg, #091124 0%, #0F1C3F 65%, #182C5E 100%) !important;
                background-color: #0F1C3F !important;
            }
            .layout-table {
                width: 100% !important;
            }
            .col-sidebar {
                width: 35% !important;
                background: #F8FAFC !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 20px 14px !important;
                word-break: break-word !important;
                overflow-wrap: anywhere !important;
                box-sizing: border-box !important;
            }
            .col-main {
                width: 65% !important;
                background: #FFFFFF !important;
                padding: 20px 22px !important;
                box-sizing: border-box !important;
            }
            .skills-container {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 4px !important;
                width: 100% !important;
            }
            .side-item-sub {
                word-break: break-all !important;
                overflow-wrap: anywhere !important;
            }
            .summary-box, .project-card, .skill-pill, .role-badge, .side-badge, .date-pill, .watermark {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        @if(!empty($forPdf))
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
            border-radius: 0 !important;
        }
        .top-banner {
            background-color: #0F1C3F !important;
            background: #0F1C3F !important;
            color: #ffffff !important;
            padding: 18px 24px 14px 24px !important;
            border-bottom: 3.5px solid #F59E0B !important;
        }
        .col-sidebar {
            width: 35% !important;
            padding: 16px 12px !important;
        }
        .col-main {
            width: 65% !important;
            padding: 16px 18px !important;
        }
        .skills-container {
            display: block !important;
        }
        .name {
            color: #FFFFFF !important;
        }
        .role-badge {
            background: #182C5E !important;
            color: #FBBF24 !important;
            border: 1px solid #F59E0B !important;
        }
        .contact-strip {
            color: #E2E8F0 !important;
        }
        .contact-icon {
            color: #F59E0B !important;
        }
        .photo-frame {
            box-shadow: none !important;
            border: 2px solid #F59E0B !important;
            border-radius: 6px !important;
        }
        .sidebar-title {
            display: block !important;
            border-bottom: 2px solid #0F172A !important;
        }
        .sidebar-title .accent-dot {
            display: inline-block !important;
            vertical-align: middle !important;
            margin-right: 6px !important;
        }
        .main-title {
            border-bottom: 2px solid #F59E0B !important;
        }
        .main-title::after {
            display: none !important;
        }
        .experience-item {
            border-left: none !important;
            padding-left: 0 !important;
            margin-bottom: 12px !important;
        }
        .experience-item::before {
            display: none !important;
        }
        .points-list {
            list-style-type: disc !important;
            padding-left: 18px !important;
        }
        .points-list li {
            padding-left: 0 !important;
        }
        .points-list li::before {
            display: none !important;
        }
        .skill-pill {
            display: inline-block !important;
            background: #0F172A !important;
            color: #F8FAFC !important;
            border: 1px solid #1E293B !important;
            padding: 2px 6px !important;
            font-size: 7.5pt !important;
            margin-right: 3px !important;
            margin-bottom: 4px !important;
            max-width: 100% !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        .side-item-sub {
            word-break: break-all !important;
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
                $pdf->page_text($w * 0.10, $h * 0.44, $watermarkText, $font, 36, [0.85, 0.20, 0.20], 0, 0, -32);
                
                // Watermark Subtitle
                $subFont = $fontMetrics->get_font("helvetica", "bold");
                $subText = "{{ ($resume->language ?? 'en') === 'ko' ? '워터마크 제거를 위해 공유하세요 • reiac.org' : 'SHARE TO REMOVE WATERMARK • REIAC.ORG' }}";
                $pdf->page_text($w * 0.16, $h * 0.48, $subText, $subFont, 11, [0.85, 0.20, 0.20], 0, 0, -32);
                
                if (method_exists($pdf, "set_opacity")) {
                    $pdf->set_opacity(1.0, "Normal");
                }
            }
        </script>
    @endif

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
                    <button type="button" onclick="window.print()" class="btn-use-tpl" style="cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <span>📥</span>
                        <span>Download / Save PDF</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div class="a4-canvas-container" id="a4CanvasContainer">
        @if(!$forPdf)
        <div class="a4-zoom-bar no-print" id="a4ZoomBar">
            <div class="a4-zoom-group">
                <button type="button" id="a4FitBtn" class="a4-zoom-pill is-active" onclick="setA4ScaleMode('fit')">Fit</button>
                <button type="button" id="a4FullBtn" class="a4-zoom-pill" onclick="setA4ScaleMode('full')">100%</button>
            </div>
            <span class="a4-page-badge">A4 · 210×297mm</span>
        </div>
        @endif

        <div class="a4-sheet-wrap" id="a4SheetWrap">
        <div class="page" id="resumePage">
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
                        <td style="width: 104px; max-width: 104px; vertical-align: middle; text-align: right; padding-left: 12px;">
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
                            <div class="skills-container">
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

                                    @if(!empty($exp['points']) && is_array($exp['points']) && count(array_filter($exp['points'], 'strlen')) > 0)
                                        <ul class="points-list">
                                            @foreach($exp['points'] as $point)
                                                @if(!empty(trim($point)))
                                                    <li>{{ trim($point) }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @elseif(!empty($exp['rawNotes']))
                                        <p style="font-size: 9pt; color: #334155; margin-top: 5px; line-height: 1.45; white-space: pre-line;">{{ $exp['rawNotes'] }}</p>
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
                                        <div style="font-size: 8pt; color: #2563EB; margin-top: 4px; font-weight: 600;">Link: {{ $proj['url'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- FORMAL DECLARATION, DATE & SIGNATURE BLOCK --}}
                    @if(!empty($resume->personal_info['declaration_date']) || !empty($resume->personal_info['signature_name']) || ($resume->language ?? '') === 'ko')
                    <div class="declaration-block" style="margin-top: 14px; padding-top: 8px; border-top: 1px solid #cbd5e1; font-size: 8pt; color: #475569; page-break-inside: avoid;">
                        <div style="font-style: italic; margin-bottom: 6px; color: #64748b;">
                            {{ $isKo ? '위 기재사항은 사실과 틀림없음을 확인합니다.' : 'I hereby declare that all the information furnished above is true and complete to the best of my knowledge.' }}
                        </div>
                        <table style="width: 100%; border-collapse: collapse; margin-top: 4px;">
                            <tr>
                                <td style="text-align: left; vertical-align: bottom; font-size: 8pt; color: #64748b;">
                                    <strong>{{ $isKo ? '작성일자 :' : 'Date :' }}</strong>
                                    <span style="margin-left: 4px;">{{ $resume->personal_info['declaration_date'] ?? date($isKo ? 'Y년 m월 d일' : 'F d, Y') }}</span>
                                </td>
                                <td style="text-align: right; vertical-align: bottom; font-size: 8pt;">
                                    <strong>{{ $isKo ? '지원자(작성자) :' : 'Applicant Signature :' }}</strong>
                                    <span style="display: inline-block; border-bottom: 1px solid #334155; min-width: 110px; text-align: center; font-weight: 700; color: #0f172a; padding: 0 6px; margin: 0 4px;">
                                        {{ $resume->personal_info['signature_name'] ?? ($resume->personal_info['full_name'] ?? ($isKo ? '성명' : 'Candidate Name')) }}
                                    </span>
                                    <span style="font-size: 7pt; color: #64748b;">{{ $isKo ? '(인 / 서명)' : '(Signed)' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endif

                </td>
            </tr>
        </table>
        </div>
        </div>
    </div>

    @include('resume-builder.partials.a4-fit-script')

    @if(request()->boolean('print'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => { window.print(); }, 400);
            });
        </script>
    @endif
</body>
</html>
