import './bootstrap';

import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse';

window.formatPostContent = function(text) {
    if (!text) return '';
    const escaped = String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const urlPattern = /(https?:\/\/[^\s<]+[^<.,:;"')\]\s]|www\.[^\s<]+[^<.,:;"')\]\s])/gi;

    return escaped.replace(urlPattern, (matchedUrl) => {
        let href = matchedUrl;
        if (/^www\./i.test(href)) {
            href = 'https://' + href;
        }
        return `<a href="${href}" target="_blank" rel="noopener noreferrer" class="text-amber-600 hover:text-amber-700 underline font-semibold break-all inline cursor-pointer" onclick="event.stopPropagation()">${matchedUrl}</a>`;
    });
};

Alpine.plugin(Collapse);

window.Alpine = Alpine;

Alpine.start();
