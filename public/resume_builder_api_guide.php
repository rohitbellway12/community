<?php
/**
 * ==============================================================================================
 * REIAC RESUME BUILDER — MOBILE APP API INTEGRATION & WORKFLOW GUIDE
 * ==============================================================================================
 * File: resume_builder_api_guide.php
 * Path: c:\xampp\htdocs\community\resume_builder_api_guide.php
 * Direct URL: http://localhost/community/resume_builder_api_guide.php
 *
 * Yeh file aap kisi bhi text editor (VS Code, Notepad, PHPStorm) me khol kar padh sakte hain,
 * YA seedha browser me (http://localhost/community/resume_builder_api_guide.php) khol kar
 * interactive documentation ki tarah padh sakte hain.
 * ==============================================================================================
 */

// If requested from command line or included, don't execute HTML template unless in web browser
if (php_sapi_name() === 'cli') {
    echo "========================================================================\n";
    echo "REIAC RESUME BUILDER — API INTEGRATION SUMMARY\n";
    echo "========================================================================\n";
    echo "1. Base URL: http://localhost/community/api/v1\n";
    echo "2. Public Endpoints:\n";
    echo "   - GET  /resume/templates\n";
    echo "   - GET  /resume/templates/{template}/sample?lang=ko\n";
    echo "   - GET  /resume/config\n";
    echo "3. Authenticated Endpoints (Bearer Token Required):\n";
    echo "   - GET  /resumes/initial-data\n";
    echo "   - GET  /resumes\n";
    echo "   - POST /resumes\n";
    echo "   - GET  /resumes/{id}\n";
    echo "   - DELETE /resumes/{id}\n";
    echo "   - POST /resume/upload-photo\n";
    echo "   - POST /resume/ai/polish-summary\n";
    echo "   - POST /resume/ai/enhance-bullets\n";
    echo "   - POST /resume/ai/suggest-skills\n";
    echo "   - POST /resumes/{id}/submit-share\n";
    echo "========================================================================\n";
    echo "Open in browser for complete UI documentation: http://localhost/community/resume_builder_api_guide.php\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REIAC Resume Builder — Mobile App API Integration Guide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0b132b;
            --surface: #111e42;
            --surface-card: #182855;
            --border: #243a73;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --emerald: #10b981;
            --rose: #ef4444;
            --cyan: #06b6d4;
            --purple: #a855f7;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            padding: 30px 16px 80px 16px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #10214a 0%, #1e3a8a 100%);
            border: 1px solid var(--border);
            padding: 32px 28px;
            border-radius: 18px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        .badge {
            display: inline-block;
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        h1 { font-size: 28px; font-weight: 900; margin-bottom: 8px; color: #ffffff; }
        p.subtitle { color: var(--text-muted); font-size: 14px; max-width: 800px; }
        
        .section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }
        .method-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
        }
        .get { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }
        .post { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
        .delete { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px;
        }
        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        th {
            background: var(--surface-card);
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        tr:hover { background: rgba(255,255,255,0.02); }
        code {
            font-family: 'JetBrains Mono', monospace;
            background: #080e22;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            color: #38bdf8;
            border: 1px solid #1e293b;
        }
        pre {
            background: #080e22;
            padding: 16px;
            border-radius: 12px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            border: 1px solid var(--border);
            color: #e2e8f0;
            margin: 12px 0;
            line-height: 1.5;
        }
        .flow-step {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .step-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: #0f172a;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .step-content { flex: 1; }
        .step-title { font-weight: 800; font-size: 14px; color: #ffffff; }
        .step-desc { font-size: 12.5px; color: var(--text-muted); margin-top: 4px; }
        .step-api { margin-top: 6px; }
        .links-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 16px;
        }
        .btn-link {
            background: var(--primary);
            color: #0f172a;
            font-weight: 800;
            font-size: 12px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-link-sec {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER BLOCK -->
    <div class="header">
        <span class="badge">🚀 Mobile App Integration Reference</span>
        <h1>REIAC Resume Builder API Guide</h1>
        <p class="subtitle">
            Yeh guide Flutter developer ke liye banayi gayi hai. Isme bataya gaya hai ki 
            <strong>App ki kis screen par, user ke kis action par konsi API call karni hai</strong> aur data flow kaise handle hoga.
        </p>
        <div class="links-row">
            <a href="REIAC_Resume_Builder_API.postman_collection.json" download class="btn-link">
                📥 Download Postman Collection JSON
            </a>
            <a href="http://localhost/community/resume-builder" target="_blank" class="btn-link-sec">
                🌐 Open Live Web Resume Builder
            </a>
        </div>
    </div>

    <!-- WORKFLOW FLOWCHART -->
    <div class="section">
        <div class="section-title">
            <span>🗺️</span>
            <span>Mobile App Complete User Journey & Condition Flow</span>
        </div>

        <div class="flow-step">
            <div class="step-num">1</div>
            <div class="step-content">
                <div class="step-title">Home Screen / Templates Gallery</div>
                <div class="step-desc">Jab user "Resume Builder" tab open kare ya App launch ho.</div>
                <div class="step-api">
                    <span class="method-badge get">GET</span> <code>/api/v1/resume/templates</code> — 4 templates ki list aur badges fetch karta hai.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">2</div>
            <div class="step-content">
                <div class="step-title">Sample Live Preview (Modal)</div>
                <div class="step-desc">User kisi card par "👁️ View Live Sample" tap kare.</div>
                <div class="step-api">
                    <span class="method-badge get">GET</span> <code>/api/v1/resume/templates/{template}/sample?lang=ko</code> — Sample dummy data deta hai.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">3</div>
            <div class="step-content">
                <div class="step-title">Build Start (Step 0 Initialization)</div>
                <div class="step-desc">User kisi template par "Build With [Template]" tap kare.</div>
                <div class="step-api">
                    <span class="method-badge get">GET</span> <code>/api/v1/resumes/initial-data?template={template}&language={ko}</code> — User profile se Name, Phone, Email auto-fill karta hai.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">4</div>
            <div class="step-content">
                <div class="step-title">Step 1: 3x4 Photo Upload</div>
                <div class="step-desc">User camera ya gallery se ID photo choose kare.</div>
                <div class="step-api">
                    <span class="method-badge post">POST</span> <code>/api/v1/resume/upload-photo</code> (multipart/form-data) — Returns <code>data.url</code> jise form state me save karein.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">5</div>
            <div class="step-content">
                <div class="step-title">Step 3: Work Experience AI Bullets</div>
                <div class="step-desc">User rough text likh kar "✨ AI Enhance Bullets" tap kare.</div>
                <div class="step-api">
                    <span class="method-badge post">POST</span> <code>/api/v1/resume/ai/enhance-bullets</code> — Action-verb bullet points generate karta hai.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">6</div>
            <div class="step-content">
                <div class="step-title">Step 5: AI Skills Suggestion</div>
                <div class="step-desc">User "✨ AI Suggest Skills" button tap kare.</div>
                <div class="step-api">
                    <span class="method-badge post">POST</span> <code>/api/v1/resume/ai/suggest-skills</code> — Target role ke keywords ka array deta hai.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">7</div>
            <div class="step-content">
                <div class="step-title">Step 6: Statement / Summary AI Polish</div>
                <div class="step-desc">Korean Statement (1-4 prompts) ya Summary box par "✨ AI Polish" tap kare.</div>
                <div class="step-api">
                    <span class="method-badge post">POST</span> <code>/api/v1/resume/ai/polish-summary</code> — Professional corporate tone me rewrite karta hai.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">8</div>
            <div class="step-content">
                <div class="step-title">Step 7: Final Save & Review</div>
                <div class="step-desc">User "Save Draft" dabaye ya Step 7 complete kare.</div>
                <div class="step-api">
                    <span class="method-badge post">POST</span> <code>/api/v1/resumes</code> — Pura 7-step JSON save karta hai. <code>resume_id</code> aur <code>preview_url</code> return hota hai.
                </div>
            </div>
        </div>

        <div class="flow-step">
            <div class="step-num">9</div>
            <div class="step-content">
                <div class="step-title">Live A4 Preview & Watermark Removal</div>
                <div class="step-desc">User "View Preview" dabaye ya watermark unlock karne ke liye link share kare.</div>
                <div class="step-api">
                    <span class="method-badge post">POST</span> <code>/api/v1/resumes/{id}/submit-share</code> (channel: link_copy) — Instant watermark unlock. Flutter InAppWebView me <code>preview_url</code> open karein.
                </div>
            </div>
        </div>
    </div>

    <!-- ALL 13 ENDPOINTS CHEATSHEET -->
    <div class="section">
        <div class="section-title">
            <span>⚡</span>
            <span>All 13 APIs Master Reference Table</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>API Endpoint</th>
                    <th>Screen / Condition</th>
                    <th>Auth</th>
                    <th>Payload / Query</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="method-badge get">GET</span></td>
                    <td><code>/resume/templates</code></td>
                    <td>Home Screen (Gallery)</td>
                    <td>Public</td>
                    <td>None</td>
                </tr>
                <tr>
                    <td><span class="method-badge get">GET</span></td>
                    <td><code>/resume/templates/{tpl}/sample</code></td>
                    <td>Sample Preview Modal</td>
                    <td>Public</td>
                    <td><code>?lang=ko</code></td>
                </tr>
                <tr>
                    <td><span class="method-badge get">GET</span></td>
                    <td><code>/resume/config</code></td>
                    <td>App Start (Settings)</td>
                    <td>Public</td>
                    <td>None</td>
                </tr>
                <tr>
                    <td><span class="method-badge get">GET</span></td>
                    <td><code>/resumes/initial-data</code></td>
                    <td>Click "Build With..."</td>
                    <td>Bearer</td>
                    <td><code>?template=korean_standard&language=ko</code></td>
                </tr>
                <tr>
                    <td><span class="method-badge post">POST</span></td>
                    <td><code>/resume/upload-photo</code></td>
                    <td>Step 1 (Choose 3x4 Photo)</td>
                    <td>Bearer</td>
                    <td><code>photo: File</code> (multipart)</td>
                </tr>
                <tr>
                    <td><span class="method-badge post">POST</span></td>
                    <td><code>/resume/ai/enhance-bullets</code></td>
                    <td>Step 3 (AI Bullets button)</td>
                    <td>Bearer</td>
                    <td><code>{"notes": "...", "role": "...", "company": "..."}</code></td>
                </tr>
                <tr>
                    <td><span class="method-badge post">POST</span></td>
                    <td><code>/resume/ai/suggest-skills</code></td>
                    <td>Step 5 (AI Skills button)</td>
                    <td>Bearer</td>
                    <td><code>{"target_role": "...", "existing_skills": []}</code></td>
                </tr>
                <tr>
                    <td><span class="method-badge post">POST</span></td>
                    <td><code>/resume/ai/polish-summary</code></td>
                    <td>Step 6 (AI Polish button)</td>
                    <td>Bearer</td>
                    <td><code>{"summary": "...", "target_role": "..."}</code></td>
                </tr>
                <tr>
                    <td><span class="method-badge post">POST</span></td>
                    <td><code>/resumes</code></td>
                    <td>Step 7 (Save / Update)</td>
                    <td>Bearer</td>
                    <td>Full 7-Step JSON (id: null ya id: 7)</td>
                </tr>
                <tr>
                    <td><span class="method-badge get">GET</span></td>
                    <td><code>/resumes</code></td>
                    <td>"My Resumes" Screen</td>
                    <td>Bearer</td>
                    <td><code>?page=1&per_page=15</code></td>
                </tr>
                <tr>
                    <td><span class="method-badge get">GET</span></td>
                    <td><code>/resumes/{id}</code></td>
                    <td>Edit Existing Resume</td>
                    <td>Bearer</td>
                    <td>None</td>
                </tr>
                <tr>
                    <td><span class="method-badge delete">DELETE</span></td>
                    <td><code>/resumes/{id}</code></td>
                    <td>Delete Resume action</td>
                    <td>Bearer</td>
                    <td>None</td>
                </tr>
                <tr>
                    <td><span class="method-badge post">POST</span></td>
                    <td><code>/resumes/{id}/submit-share</code></td>
                    <td>Remove Watermark / Share</td>
                    <td>Bearer</td>
                    <td><code>{"channel": "link_copy"}</code></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SAMPLE SAVE PAYLOAD -->
    <div class="section">
        <div class="section-title">
            <span>📦</span>
            <span>Save API Payload (Flutter State Data Model)</span>
        </div>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
            Jab Flutter app se <code>POST /api/v1/resumes</code> par save kiya jayega, tab exact yahi JSON bheja jayega:
        </p>
<pre>{
  "id": null, // Naye ke liye null, existing resume update karne ke liye numeric ID
  "title": "My Professional Resume",
  "template": "korean_standard", // "korean_standard" | "modern" | "classic" | "creative"
  "language": "ko", // "ko" | "en"

  "personal_info": {
    "full_name": "홍길동",
    "english_name": "GILDONG HONG",
    "date_of_birth": "1998.05.14",
    "gender": "남 (Male)",
    "job_title": "백엔드 개발자",
    "total_experience": "신입 / 2년",
    "email": "gildong@example.com",
    "phone": "010-1234-5678",
    "emergency_phone": "010-9876-5432",
    "location": "서울특별시 서초구",
    "photo_url": "http://localhost/community/storage/resumes/photos/example.jpg",
    "linkedin": "https://linkedin.com/in/gildong",
    "github": "https://github.com/gildong",
    "declaration_date": "2026년 10월 27일",
    "signature_name": "홍길동"
  },

  "education": [
    {
      "institution": "서울대학교",
      "field": "컴퓨터공학과",
      "degree": "학사",
      "grade": "3.8/4.5",
      "start_date": "2018.03",
      "end_date": "2022.02",
      "is_current": false
    }
  ],

  "experience": [
    {
      "title": "주임 연구원",
      "company": "네이버",
      "location": "플랫폼 개발실",
      "start_date": "2022.03",
      "end_date": "2024.01",
      "is_current": false,
      "rawNotes": "대용량 API 게이트웨이 설계 및 구축",
      "points": [
        "Spearheaded large-scale API gateway architecture serving 2M users."
      ]
    }
  ],

  "language_tests": [
    {
      "test_name": "TOEIC",
      "score": "950",
      "acquisition_date": "2023.09",
      "organization": "ETS"
    }
  ],

  "certifications": [
    {
      "name": "정보처리기사",
      "issuer": "한국산업인력공단",
      "year": "2022.06"
    }
  ],

  "military_service": {
    "status": "군필 (Completed)",
    "branch": "육군",
    "rank": "병장",
    "period": "2018.06 ~ 2020.02",
    "discharge_reason": "만기전역"
  },

  "computer_skills": [
    {
      "category": "프로그래밍",
      "tool_name": "Python / Java",
      "proficiency": "상 (Advanced)",
      "description": "분산 아키텍처 및 REST API 구축"
    }
  ],

  "skills": ["Flutter", "Dart", "Laravel", "MySQL", "Docker"],

  "self_introduction": {
    "growth": "1. 성장과정 내용...",
    "personality": "2. 성격의 장단점 및 생활신조...",
    "strengths": "3. 주요경력 및 직무수행 역량...",
    "motivation_and_aspirations": "4. 지원동기 및 입사 후 포부..."
  }
}</pre>
    </div>

    <!-- FLUTTER CODE SNIPPET -->
    <div class="section">
        <div class="section-title">
            <span>📱</span>
            <span>Flutter InAppWebView Live A4 Preview Snippet</span>
        </div>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
            Website jaisa exact pixel-perfect preview mobile par render karne ke liye Flutter me <code>InAppWebView</code> use karein:
        </p>
<pre>// Flutter Screen: resume_preview_screen.dart
InAppWebView(
  initialUrlRequest: URLRequest(
    url: WebUri("http://localhost/community/resume-builder/${widget.resumeId}/preview?embed=1"),
    headers: {
      "Authorization": "Bearer $userToken",
    },
  ),
  initialSettings: InAppWebViewSettings(
    useShouldOverrideUrlLoading: true,
    supportZoom: true,
    builtInZoomControls: false,
    displayZoomControls: false,
  ),
)</pre>
    </div>

</div>

</body>
</html>

