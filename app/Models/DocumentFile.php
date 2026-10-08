<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'mime_type',
        'download_count',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    protected $appends = [
        'file_url',
        'formatted_size',
        'file_badge',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return asset('storage/' . $this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '—';
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileBadgeAttribute(): array
    {
        $ext = strtolower($this->file_type ?? '');

        if (in_array($ext, ['pdf'])) {
            return ['label' => 'PDF', 'bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'icon' => '📄'];
        }
        if (in_array($ext, ['doc', 'docx'])) {
            return ['label' => 'WORD', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => '📝'];
        }
        if (in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return ['label' => 'EXCEL', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => '📊'];
        }
        if (in_array($ext, ['ppt', 'pptx'])) {
            return ['label' => 'PPT', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'icon' => '📑'];
        }
        if (in_array($ext, ['hwp', 'hwpx'])) {
            return ['label' => 'HWP', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'icon' => '🇰🇷'];
        }
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'])) {
            return ['label' => strtoupper($ext), 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '🖼️'];
        }
        if (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
            return ['label' => 'VIDEO', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => '🎥'];
        }
        if (in_array($ext, ['mp3', 'wav', 'aac', 'ogg'])) {
            return ['label' => 'AUDIO', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200', 'icon' => '🎵'];
        }
        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return ['label' => 'ARCHIVE', 'bg' => 'bg-slate-200', 'text' => 'text-slate-700', 'border' => 'border-slate-300', 'icon' => '📦'];
        }

        return ['label' => strtoupper($ext ?: 'FILE'), 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'icon' => '📁'];
    }
}
