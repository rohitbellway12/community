<!DOCTYPE html>
<html lang="{{ ($resume->language ?? 'en') === 'ko' ? 'ko' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            width: 800px;
            min-width: 800px;
            max-width: 800px;
            min-height: 1130px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
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

        /* Watermark */
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
            transform: rotate(-32deg);
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
            color: rgba(220, 38, 38, 0.24);
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
                background: #f1f5f9;
                padding: {{ !empty($isEmbed) ? '10px 4px' : '24px 12px' }};
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
            .web-preview-bar div {
                text-align: center !important;
                width: 100% !important;
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
            .web-preview-bar, .a4-zoom-bar, .no-print {
                display: none !important;
            }
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
                padding: 24px 30px !important;
                max-width: 100% !important;
                min-width: 0 !important;
                width: 100% !important;
                border: none !important;
                margin: 0 !important;
                transform: none !important;
            }
            .watermark {
                display: block !important;
                position: fixed !important;
                top: 40% !important;
                left: 5% !important;
                width: 90% !important;
                transform: rotate(-32deg) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                z-index: 99999 !important;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }

        @if(!empty($forPdf))
        @page {
            size: A4 portrait;
            margin: 12mm 14mm 12mm 14mm;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            font-family: Helvetica, Arial, sans-serif !important;
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
                
                // Primary Diagonal Watermark (Centered across A4)
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
                    <button type="button" onclick="window.print()" style="background: #f59e0b; color: #0f172a; padding: 7px 16px; border-radius: 8px; font-weight: 800; border: none; cursor: pointer; margin-left: 8px;">📥 Download / Save PDF</button>
                @endif
            </div>
        </div>
    @endif

    @php $isKo = ($resume->language ?? 'en') === 'ko'; @endphp

    <div class="a4-canvas-container" id="a4CanvasContainer">
        @if(!$forPdf && empty($isEmbed))
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

                        @php
                            $contacts = array_filter([
                                !empty($resume->personal_info['email']) ? $resume->personal_info['email'] : null,
                                !empty($resume->personal_info['phone']) ? $resume->personal_info['phone'] : null,
                                !empty($resume->personal_info['location']) ? $resume->personal_info['location'] : null,
                                !empty($resume->personal_info['linkedin']) ? $resume->personal_info['linkedin'] : null,
                                !empty($resume->personal_info['github']) ? $resume->personal_info['github'] : null,
                            ]);
                        @endphp
                        @if(count($contacts) > 0)
                            <div class="contact-row" style="font-size: 9pt; color: #475569; margin-top: 6px; font-weight: 500;">
                                {{ implode('  •  ', $contacts) }}
                            </div>
                        @endif
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

                        @if(!empty($exp['points']) && is_array($exp['points']) && count(array_filter($exp['points'], 'strlen')) > 0)
                            <ul class="bullet-list">
                                @foreach($exp['points'] as $pt)
                                    @if(!empty(trim($pt)))
                                        <li>{{ trim($pt) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @elseif(!empty($exp['rawNotes']))
                            <div class="summary-text" style="white-space: pre-line;">{{ $exp['rawNotes'] }}</div>
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

        {{-- FORMAL DECLARATION, DATE & SIGNATURE BLOCK --}}
        @if(!empty($resume->personal_info['declaration_date']) || !empty($resume->personal_info['signature_name']) || ($resume->language ?? '') === 'ko')
        <div class="declaration-block" style="margin-top: 14px; padding-top: 8px; border-top: 1px solid #cbd5e1; font-size: 8pt; color: #334155; page-break-inside: avoid;">
            <div style="font-style: italic; margin-bottom: 6px; color: #475569;">
                {{ $isKo ? '위 기재사항은 사실과 틀림없음을 확인합니다.' : 'I hereby declare that all the information furnished above is true and complete to the best of my knowledge.' }}
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-top: 4px;">
                <tr>
                    <td style="text-align: left; vertical-align: bottom; font-size: 8pt; color: #475569;">
                        <strong>{{ $isKo ? '작성일자 :' : 'Date :' }}</strong>
                        <span style="margin-left: 4px;">{{ $resume->personal_info['declaration_date'] ?? date($isKo ? 'Y년 m월 d일' : 'F d, Y') }}</span>
                    </td>
                    <td style="text-align: right; vertical-align: bottom; font-size: 8pt;">
                        <strong>{{ $isKo ? '지원자(작성자) :' : 'Applicant Signature :' }}</strong>
                        <span style="display: inline-block; border-bottom: 1px solid #334155; min-width: 120px; text-align: center; font-weight: 700; color: #0f172a; padding: 0 8px; margin: 0 4px;">
                            {{ $resume->personal_info['signature_name'] ?? ($resume->personal_info['full_name'] ?? ($isKo ? '성명' : 'Candidate Name')) }}
                        </span>
                        <span style="font-size: 7.5pt; color: #64748b;">{{ $isKo ? '(인 / 서명)' : '(Signed)' }}</span>
                    </td>
                </tr>
            </table>
        </div>
        @endif

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
