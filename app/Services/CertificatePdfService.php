<?php

namespace App\Services;

use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Shared-hosting safe certificate PDF (DomPDF only).
 * Used by website download + mobile app download/base64 — same file everywhere.
 */
class CertificatePdfService
{
    public function getPdfBinary(Test $test, TestAttempt $attempt): ?string
    {
        $cachedPath = $this->getPdfPathIfExists($test, $attempt);
        if ($cachedPath && file_exists($cachedPath) && filesize($cachedPath) > 1000) {
            $content = @file_get_contents($cachedPath);
            if ($content !== false && strlen($content) > 1000) {
                return $content;
            }
        }

        $path = $this->getOrGeneratePdf($test, $attempt);
        if ($path && file_exists($path) && filesize($path) > 1000) {
            $content = @file_get_contents($path);
            if ($content !== false && strlen($content) > 1000) {
                return $content;
            }
        }

        return $this->generateDomPdfBinary($test, $attempt);
    }

    public function isPdfCacheValid(?string $pdfPath, ?TestAttempt $attempt = null): bool
    {
        if (!$pdfPath || !file_exists($pdfPath) || filesize($pdfPath) < 1000) {
            return false;
        }

        if (request()->hasAny(['refresh', 'force', 'regenerate'])) {
            @unlink($pdfPath);
            return false;
        }

        $template = resource_path('views/student/tests/certificate-pdf.blade.php');
        if (file_exists($template) && filemtime($pdfPath) < filemtime($template)) {
            @unlink($pdfPath);
            return false;
        }

        // Invalidate if candidate photo or avatar was updated/rotated after this PDF was generated
        if ($attempt) {
            $photoFiles = array_filter([
                !empty($attempt->candidate_photo) ? public_path('storage/' . $attempt->candidate_photo) : null,
                !empty($attempt->candidate_photo) ? storage_path('app/public/' . $attempt->candidate_photo) : null,
                (!empty($attempt->user) && !empty($attempt->user->avatar)) ? public_path('storage/' . $attempt->user->avatar) : null,
                (!empty($attempt->user) && !empty($attempt->user->avatar)) ? storage_path('app/public/' . $attempt->user->avatar) : null,
            ]);

            foreach ($photoFiles as $photoFile) {
                if (file_exists($photoFile) && filemtime($photoFile) > filemtime($pdfPath)) {
                    @unlink($pdfPath);
                    return false;
                }
            }
        }

        return true;
    }

    public function clearCache(?Test $test = null, ?TestAttempt $attempt = null): void
    {
        $dir = storage_path('app/certificates');
        if ($test && $attempt) {
            $file = $dir . "/certificate_{$test->id}_{$attempt->id}.pdf";
            if (file_exists($file)) {
                @unlink($file);
            }
        } elseif (is_dir($dir)) {
            foreach (glob($dir . '/certificate_*.pdf') as $f) {
                @unlink($f);
            }
        }
    }

    public function getPdfPathIfExists(Test $test, TestAttempt $attempt): ?string
    {
        $dir = storage_path('app/certificates');
        $pdfPath = $dir . "/certificate_{$test->id}_{$attempt->id}.pdf";
        return $this->isPdfCacheValid($pdfPath, $attempt) ? $pdfPath : null;
    }

    public function getOrGeneratePdf(Test $test, TestAttempt $attempt): ?string
    {
        $dir = storage_path('app/certificates');
        try {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0777, true, true);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not create certificates directory: ' . $e->getMessage());
        }

        $pdfPath = $dir . "/certificate_{$test->id}_{$attempt->id}.pdf";

        if ($this->isPdfCacheValid($pdfPath, $attempt)) {
            return $pdfPath;
        }

        @unlink($pdfPath);
        $this->generatePdfFile($test, $attempt, $pdfPath);

        return (file_exists($pdfPath) && filesize($pdfPath) > 1000) ? $pdfPath : null;
    }

    public function getPdfBase64(Test $test, TestAttempt $attempt): ?string
    {
        try {
            $binary = $this->getPdfBinary($test, $attempt);
            if ($binary && strlen($binary) > 1000) {
                return base64_encode($binary);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to get certificate PDF base64: ' . $e->getMessage());
        }

        return null;
    }

    protected function generatePdfFile(Test $test, TestAttempt $attempt, string $outputPath): void
    {
        try {
            $binary = $this->generateDomPdfBinary($test, $attempt);
            if ($binary && strlen($binary) > 1000) {
                @file_put_contents($outputPath, $binary);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to write DomPDF certificate to disk: ' . $e->getMessage());
        }
    }

    public function generateDomPdfBinary(Test $test, TestAttempt $attempt): ?string
    {
        $test->loadMissing(['testLevel']);
        $attempt->loadMissing(['user.profile']);

        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            @mkdir($fontDir, 0775, true);
        }

        // Ensure Korean fonts are available in DomPDF font cache (local + live/public_html)
        $this->ensureFontsCopied($fontDir);

        $dompdfOptions = [
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'fontDir' => $fontDir,
            'fontCache' => $fontDir,
            'tempDir' => sys_get_temp_dir(),
            'chroot' => array_values(array_filter([
                base_path(),
                public_path(),
                storage_path(),
                $fontDir,
                base_path('public_html'),
            ], 'is_dir')),
        ];

        try {
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.tests.certificate-pdf', [
                    'test' => $test,
                    'attempt' => $attempt,
                ]);
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOptions($dompdfOptions);
                return $pdf->output();
            }

            if (app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper')->loadView('student.tests.certificate-pdf', [
                    'test' => $test,
                    'attempt' => $attempt,
                ]);
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOptions($dompdfOptions);
                return $pdf->output();
            }

            if (class_exists(\Dompdf\Dompdf::class)) {
                $html = view('student.tests.certificate-pdf', compact('test', 'attempt'))->render();
                $options = new \Dompdf\Options();
                foreach ($dompdfOptions as $key => $val) {
                    $options->set($key, $val);
                }
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                return $dompdf->output();
            }

            Log::error('No DomPDF engine available for certificate generation.');
        } catch (\Throwable $e) {
            Log::error('DomPDF generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    /**
     * Copy NanumGothic fonts into storage/fonts if missing (shared hosting safe).
     */
    protected function ensureFontsCopied(string $fontDir): void
    {
        $sources = [
            public_path('fonts'),
            base_path('public/fonts'),
            base_path('public_html/fonts'),
        ];

        $files = [
            'NanumGothic-Regular.ttf',
            'NanumGothic-Bold.ttf',
            'NanumGothic-ExtraBold.ttf',
        ];

        foreach ($files as $file) {
            $dest = $fontDir . DIRECTORY_SEPARATOR . $file;
            if (file_exists($dest) && filesize($dest) > 1000) {
                continue;
            }
            foreach ($sources as $dir) {
                $src = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $file;
                if (file_exists($src)) {
                    @copy($src, $dest);
                    break;
                }
            }
        }
    }
}
