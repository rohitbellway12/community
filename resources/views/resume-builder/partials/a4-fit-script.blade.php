{{-- Shared A4 fit-to-screen scaler for standalone /preview pages (not embed, not PDF). --}}
@if(empty($forPdf))
<style>
    html { overflow-x: hidden; }
    .a4-canvas-container {
        overflow-x: hidden;
        width: 100%;
        max-width: 100vw;
    }
    .a4-sheet-wrap {
        position: relative;
        width: 800px;
        margin: 0 auto;
        flex-shrink: 0;
    }
    .a4-zoom-bar {
        flex-wrap: wrap;
        padding: 0 8px;
        box-sizing: border-box;
    }
    .a4-zoom-group {
        display: inline-flex;
        align-items: center;
        background: #e2e8f0;
        border-radius: 9999px;
        padding: 3px;
        gap: 2px;
    }
    .a4-zoom-pill.is-active {
        background: #0f172a !important;
        color: #ffffff !important;
        border-color: #0f172a !important;
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
        var avail = Math.max(160, container.clientWidth);
        var fitScale = Math.min(1, avail / pageWidth);
        var scale = mode === 'fit' ? Math.max(0.22, fitScale) : 1;

        page.style.transformOrigin = 'top left';
        page.style.transform = 'scale(' + scale + ')';
        page.style.margin = '0';

        wrap.style.width = Math.round(pageWidth * scale) + 'px';
        wrap.style.height = Math.round(pageHeight * scale) + 'px';

        document.documentElement.style.overflowX = mode === 'fit' ? 'hidden' : 'auto';
        document.body.style.overflowX = mode === 'fit' ? 'hidden' : 'auto';

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

    window.addEventListener('resize', applyA4Scaling);
    window.addEventListener('orientationchange', applyA4Scaling);
    window.addEventListener('DOMContentLoaded', applyA4Scaling);
    window.addEventListener('load', applyA4Scaling);
    setTimeout(applyA4Scaling, 200);
    setTimeout(applyA4Scaling, 800);
})();
</script>
@endif
