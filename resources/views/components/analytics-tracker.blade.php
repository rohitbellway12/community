@php
    $analytics = \App\Models\AnalyticsSetting::config();
@endphp

@if($analytics && $analytics->is_enabled && !empty($analytics->google_analytics_id))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analytics->google_analytics_id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{ $analytics->google_analytics_id }}', {
            'send_page_view': true,
            @auth
            'user_id': '{{ auth()->id() }}',
            @endauth
        });

        // Dynamic Event Tracking for WhatsApp and Document Downloads
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (e) {
                var target = e.target.closest('a');
                if (!target || !target.href) return;

                var href = target.href.toLowerCase();

                // 1. Android / iOS Store Clicks
                if (href.indexOf('play.google.com') !== -1) {
                    gtag('event', 'app_download_android', { 'event_category': 'App Download', 'link_url': target.href });
                } else if (href.indexOf('apps.apple.com') !== -1) {
                    gtag('event', 'app_download_ios', { 'event_category': 'App Download', 'link_url': target.href });
                }

                // 3. Document / Resource Download Tracking
                else if (href.indexOf('/documents/') !== -1 && href.indexOf('download') !== -1) {
                    var title = target.innerText.trim() || 'Document';
                    gtag('event', 'file_download', {
                        'event_category': 'Downloads',
                        'event_label': title,
                        'file_url': target.href
                    });
                }

                // 4. WhatsApp Consultation Lead Tracking
                else if (href.indexOf('wa.me') !== -1 || href.indexOf('api.whatsapp.com') !== -1) {
                    gtag('event', 'whatsapp_click', {
                        'event_category': 'Lead Generation',
                        'event_label': target.innerText.trim() || 'WhatsApp Consultation',
                        'link_url': target.href
                    });
                }
            }, true);
        });
    </script>
@endif

@if($analytics && !empty($analytics->custom_head_scripts))
    {!! $analytics->custom_head_scripts !!}
@endif

@if($analytics && !empty($analytics->custom_body_scripts))
    {!! $analytics->custom_body_scripts !!}
@endif
