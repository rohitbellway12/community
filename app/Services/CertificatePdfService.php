<?php

namespace App\Services;

use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CertificatePdfService
{
    /**
     * Get the PDF binary content directly (either from disk cache or by generating).
     */
    public function getPdfBinary(Test $test, TestAttempt $attempt): ?string
    {
        // 1. Try from cached file if it exists
        $cachedPath = $this->getPdfPathIfExists($test, $attempt);
        if ($cachedPath && file_exists($cachedPath) && filesize($cachedPath) > 1000) {
            $content = @file_get_contents($cachedPath);
            if ($content !== false && strlen($content) > 1000) {
                return $content;
            }
        }

        // 2. Try to generate and save to disk
        $path = $this->getOrGeneratePdf($test, $attempt);
        if ($path && file_exists($path) && filesize($path) > 1000) {
            $content = @file_get_contents($path);
            if ($content !== false && strlen($content) > 1000) {
                return $content;
            }
        }

        // 3. In-memory fallback (no disk dependency)
        return $this->generateDomPdfBinary($test, $attempt);
    }

    /**
     * Get the absolute path to the certificate PDF if it exists on disk.
     */
    public function getPdfPathIfExists(Test $test, TestAttempt $attempt): ?string
    {
        $dir = storage_path('app/certificates');
        $pdfPath = $dir . "/certificate_{$test->id}_{$attempt->id}.pdf";
        return (file_exists($pdfPath) && filesize($pdfPath) > 1000) ? $pdfPath : null;
    }

    /**
     * Get or generate the PDF file on disk. Returns path or null if cannot write to disk.
     */
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

        if (file_exists($pdfPath) && filesize($pdfPath) > 1000) {
            return $pdfPath;
        }

        $this->generatePdfFile($test, $attempt, $pdfPath);

        return (file_exists($pdfPath) && filesize($pdfPath) > 1000) ? $pdfPath : null;
    }

    /**
     * Get the base64 encoded content of the certificate PDF.
     */
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

    /**
     * Generate the PDF using headless Chrome/Chromium/Edge or fallback to DomPDF.
     */
    protected function generatePdfFile(Test $test, TestAttempt $attempt, string $outputPath): void
    {
        $browserPath = $this->findBrowserBinary();
        if ($browserPath) {
            $targetUrl = route('tests.student.certificate', [
                'test' => $test->id,
                'attempt' => $attempt->id,
            ]);

            $cmd = sprintf(
                '"%s" --headless --disable-gpu --no-pdf-header-footer --print-to-pdf="%s" "%s"',
                $browserPath,
                $outputPath,
                $targetUrl
            );

            @exec($cmd, $output, $returnCode);

            if (file_exists($outputPath) && filesize($outputPath) > 1000) {
                return;
            }
        }

        // Fallback: Generate via DomPDF and write to disk
        try {
            $binary = $this->generateDomPdfBinary($test, $attempt);
            if ($binary && strlen($binary) > 1000) {
                @file_put_contents($outputPath, $binary);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to write DomPDF fallback to disk: ' . $e->getMessage());
        }
    }

    /**
     * Generate in-memory PDF binary using DomPDF.
     */
    public function generateDomPdfBinary(Test $test, TestAttempt $attempt): ?string
    {
        $test->loadMissing(['testLevel']);
        $attempt->loadMissing(['user.profile']);

        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            @mkdir($fontDir, 0775, true);
        }

        $dompdfOptions = [
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'fontDir' => $fontDir,
            'fontCache' => $fontDir,
            'tempDir' => $fontDir,
            'chroot' => [base_path(), $fontDir],
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

            if (class_exists('Barryvdh\DomPDF\Facade\PDF')) {
                $pdf = call_user_func(['Barryvdh\DomPDF\Facade\PDF', 'loadView'], 'student.tests.certificate-pdf', [
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
        } catch (\Throwable $e) {
            Log::error('DomPDF generation failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Find a suitable Chromium-based browser executable.
     */
    protected function findBrowserBinary(): ?string
    {
        if (!function_exists('exec')) {
            return null;
        }

        $disabled = explode(',', ini_get('disable_functions') ?: '');
        $disabled = array_map('trim', $disabled);
        if (in_array('exec', $disabled, true)) {
            return null;
        }

        $candidates = [
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ];

        foreach ($candidates as $bin) {
            if (@file_exists($bin)) {
                return $bin;
            }
        }

        return null;
    }
}
