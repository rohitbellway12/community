<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $campaign->campaign_name ?? 'Opening App...' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #fff;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 32px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .icon-wrap {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
        }
        .icon-wrap svg {
            width: 38px;
            height: 38px;
            fill: none;
            stroke: #fff;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        h1 {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        p {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .spinner {
            width: 32px;
            height: 32px;
            margin: 0 auto 20px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top-color: #10b981;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: #10b981;
            color: #ffffff;
            margin-bottom: 10px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-primary:active {
            transform: scale(0.98);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg viewBox="0 0 24 24">
                <rect x="5" y="2" width="14" height="20" rx="3" ry="3"></rect>
                <line x1="12" y1="18" x2="12.01" y2="18"></line>
            </svg>
        </div>

        <span class="badge">Campaign: {{ $campaign->code }}</span>
        <h1>Opening App...</h1>
        <p>Redirecting you to the app or store...</p>

        <div class="spinner" id="spinner"></div>

        <div style="margin-top: 10px;">
            <a href="{{ $openAppUrl }}" id="btnOpenApp" class="btn btn-primary">Open in App</a>
            <a href="{{ $fallbackStoreUrl }}" id="btnStore" class="btn btn-secondary">Download on Store</a>
        </div>
    </div>

    <script>
        var openAppUrl = @json($openAppUrl);
        var fallbackStoreUrl = @json($fallbackStoreUrl);

        // Attempt automatic deep link launch
        try {
            window.location.href = openAppUrl;
        } catch(e) {
            console.error('Auto redirect failed', e);
        }

        // Fallback timer: if app is not installed or window remains active after 2s, redirect to store
        var timer = setTimeout(function() {
            if (!document.hidden) {
                window.location.href = fallbackStoreUrl;
            }
        }, 2000);

        // Cancel store fallback if app successfully takes over foreground
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearTimeout(timer);
            }
        });
    </script>
</body>
</html>
