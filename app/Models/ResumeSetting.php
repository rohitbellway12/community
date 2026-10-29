<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeSetting extends Model
{
    protected $fillable = [
        'openai_api_key',
        'openai_model',
        'watermark_text',
        'watermark_enabled',
        'verification_mode',
        'language_mode',
        'reshare_guidelines',
        'reshare_content',
        'reshare_target_url',
    ];

    protected $casts = [
        'watermark_enabled' => 'boolean',
    ];

    public static function config(): self
    {
        return cache()->rememberForever('resume_settings_config', function () {
            return static::firstOrCreate([], [
                'openai_api_key'     => env('OPENAI_API_KEY'),
                'openai_model'       => 'gpt-6-luna',
                'watermark_text'     => 'REIAC COMMUNITY • reiac.org',
                'watermark_enabled'  => true,
                'verification_mode'  => 'both',
                'language_mode'      => 'both',
                'reshare_guidelines' => "1. Share our official launch post or platform link on your LinkedIn / WhatsApp Status / Instagram Story.\n2. Add hashtag #REIACCommunity.\n3. Keep the post visible for at least 24 hours.",
                'reshare_content'    => "Building my professional CV with AI on REIAC Community! 🚀 Try it out: https://community.reiac.org/resume-builder #REIACCommunity #CareerGrowth",
                'reshare_target_url' => 'https://community.reiac.org',
            ]);
        });
    }

    public static function clearCache(): void
    {
        cache()->forget('resume_settings_config');
    }
}
