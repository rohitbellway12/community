{{-- Shared A4 fit-to-screen scaler for standalone & embedded /preview pages (not PDF). --}}
@if(empty($forPdf))
<style>
    html, body {
        overflow-x: hidden;
        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }
    .a4-canvas-container {
        overflow-x: hidden;
        width: 100%;
        max-width: 100vw;
        box-sizing: border-box;
    }
    .a4-sheet-wrap {
        position: relative;
        width: 800px;
        margin: 0 auto;
        flex-shrink: 0;
        transition: width 0.15s ease, height 0.15s ease;
    }
    #resumePage {
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.18), 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
        border: 1px solid #cbd5e1 !important;
        background: #ffffff !important;
    }
    .a4-zoom-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        padding: 4px 8px 10px 8px;
        box-sizing: border-box;
        width: 100%;
    }
    .a4-zoom-group {
        display: inline-flex;
        align-items: center;
        background: #e2e8f0;
        border: 1px solid #cbd5e1;
        border-radius: 9999px;
        padding: 2px;
        gap: 2px;
    }
    .a4-zoom-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: transparent;
        color: #475569;
        border: none;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .a4-zoom-pill:hover {
        color: #0f172a;
    }
    .a4-zoom-pill.is-active {
        background: #0f172a !important;
        color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }
    .a4-page-badge {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 9999px;
        letter-spacing: 0.02em;
    }
    @media print {
        .a4-sheet-wrap {
            width: 100% !important;
            height: auto !important;
        }
        #resumePage {
            transform: none !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
<script>
(function () {
    var mode = 'fit';

    function applyA4Scaling() {
        var page = document.getElementById('resumePage');
        var wrap = document.getElementById('a4SheetWrap');
        var container = document.getElementById('a4CanvasContainer');
        if (!page || !wrap || !container) return;

        var pageWidth = page.offsetWidth || 800;
        var pageHeight = Math.max(page.scrollHeight, page.offsetHeight, 1130);

        var paddingX = 16;
        try {
            var cs = window.getComputedStyle(container);
            paddingX = (parseFloat(cs.paddingLeft) || 0) + (parseFloat(cs.paddingRight) || 0);
        } catch (e) {}

        var avail = Math.max(160, container.clientWidth - Math.max(8, paddingX));
        var fitScale = Math.min(1, avail / pageWidth);
        var scale = mode === 'fit' ? Math.max(0.22, fitScale) : 1;

        page.style.transformOrigin = 'top left';
        page.style.transform = 'scale(' + scale + ')';
        page.style.margin = '0';

        wrap.style.width = Math.round(pageWidth * scale) + 'px';
        wrap.style.height = Math.round(pageHeight * scale) + 'px';

        document.documentElement.style.overflowX = mode === 'fit' ? 'hidden' : 'auto';
        document.body.style.overflowX = mode === 'fit' ? 'hidden' : 'auto';
        container.style.overflowX = mode === 'fit' ? 'hidden' : 'auto';

        var fitBtn = document.getElementById('a4FitBtn');
        var fullBtn = document.getElementById('a4FullBtn');
        if (fitBtn) fitBtn.classList.toggle('is-active', mode === 'fit');
        if (fullBtn) fullBtn.classList.toggle('is-active', mode === 'full');
    }

    window.setA4ScaleMode = function (next) {
        mode = next === 'full' ? 'full' : 'fit';
        applyA4Scaling();
    };
    window.toggleA4Scale = function () {
        window.setA4ScaleMode(mode === 'fit' ? 'full' : 'fit');
    };

    window.addEventListener('message', function (ev) {
        if (!ev || !ev.data) return;
        if (ev.data.type === 'SET_A4_MODE') {
            window.setA4ScaleMode(ev.data.mode);
        } else if (ev.data.type === 'RESCALE_A4') {
            applyA4Scaling();
        }
    });

    window.addEventListener('resize', applyA4Scaling);
    window.addEventListener('orientationchange', applyA4Scaling);
    window.addEventListener('DOMContentLoaded', applyA4Scaling);
    window.addEventListener('load', applyA4Scaling);
    setTimeout(applyA4Scaling, 100);
    setTimeout(applyA4Scaling, 300);
    setTimeout(applyA4Scaling, 700);
})();
</script>
@endif