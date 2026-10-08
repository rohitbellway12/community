<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentWebController extends Controller
{
    /**
     * Download or redirect to a document for community users.
     */
    public function download(Document $document): BinaryFileResponse|StreamedResponse|RedirectResponse
    {
        if (! $document->is_active) {
            abort(404, 'Document not found or inactive.');
        }

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            if ($document->link_url) {
                $document->increment('download_count');
                return redirect()->away($document->link_url);
            }
            return back()->with('error', 'File not found in storage.');
        }

        $document->increment('download_count');

        $downloadFilename = $document->file_name ?: ('document_' . $document->id . '.' . ($document->file_type ?: 'dat'));

        return Storage::disk('public')->download($document->file_path, $downloadFilename);
    }

    /**
     * Download a specific attached file of a document.
     */
    public function downloadFile(\App\Models\DocumentFile $file): BinaryFileResponse|StreamedResponse|RedirectResponse
    {
        $document = $file->document;
        if ($document && ! $document->is_active) {
            abort(404, 'Document not found or inactive.');
        }

        if (! $file->file_path || ! Storage::disk('public')->exists($file->file_path)) {
            return back()->with('error', 'File not found in storage.');
        }

        $file->increment('download_count');
        $document?->increment('download_count');

        $downloadFilename = $file->file_name ?: ('file_' . $file->id . '.' . ($file->file_type ?: 'dat'));

        return Storage::disk('public')->download($file->file_path, $downloadFilename);
    }
}
