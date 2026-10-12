<?php

namespace App\Helpers;

class LinkHelper
{
    /**
     * Converts raw text into safe HTML with clickable hyperlinks for URLs.
     *
     * @param string|null $text
     * @return string
     */
    public static function makeClickableLinks(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. Escape HTML special characters to prevent XSS vulnerabilities
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // 2. Regular expression to detect URLs starting with http://, https://, or www.
        // It avoids trailing punctuation like periods, commas, colons, or closing brackets
        $pattern = '/(https?:\/\/[^\s<]+[^<.,:;"\')\]\s]|www\.[^\s<]+[^<.,:;"\')\]\s])/i';

        return preg_replace_callback($pattern, function ($matches) {
            $url = $matches[1];
            $href = str_starts_with(strtolower($url), 'www.') ? 'https://' . $url : $url;

            return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer" class="text-amber-600 hover:text-amber-700 underline font-semibold break-all inline cursor-pointer" onclick="event.stopPropagation()">' . $url . '</a>';
        }, $escaped);
    }
}
