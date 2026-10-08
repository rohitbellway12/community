<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    use ApiResponse;

    /**
     * List all published documents and resources.
     *
     * GET /api/v1/documents
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::where('is_active', true)->with('files')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', strtolower($request->input('file_type')));
        }

        $perPage = min(50, max(5, $request->integer('per_page', 20)));
        $documents = $query->paginate($perPage);

        $categories = Document::where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $data = [
            'documents'  => $documents->items(),
            'categories' => $categories,
            'pagination' => [
                'current_page' => $documents->currentPage(),
                'last_page'    => $documents->lastPage(),
                'per_page'     => $documents->perPage(),
                'total'        => $documents->total(),
                'has_more'     => $documents->hasMorePages(),
            ],
        ];

        return $this->successResponse($data, 'Documents retrieved successfully.');
    }

    /**
     * Get specific document details.
     *
     * GET /api/v1/documents/{document}
     */
    public function show(Document $document): JsonResponse
    {
        if (!$document->is_active) {
            return $this->errorResponse('Document not found or inactive.', 404);
        }

        $document->load('files');

        return $this->successResponse($document, 'Document details retrieved successfully.');
    }

    /**
     * Download document file or increment download counter.
     *
     * GET /api/v1/documents/{document}/download
     */
    public function download(Document $document)
    {
        if (!$document->is_active) {
            return $this->errorResponse('Document not found or inactive.', 404);
        }

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            if ($document->link_url) {
                return redirect()->away($document->link_url);
            }
            return $this->errorResponse('File not available for download.', 404);
        }

        $document->increment('download_count');

        $downloadFilename = $document->file_name ?: ('document_' . $document->id . '.' . ($document->file_type ?: 'dat'));

        return Storage::disk('public')->download($document->file_path, $downloadFilename);
    }
}
