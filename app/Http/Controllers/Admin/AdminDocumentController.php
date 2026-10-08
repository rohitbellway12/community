<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::with('uploader')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('type')) {
            if ($request->input('type') === 'file') {
                $query->whereNotNull('file_path');
            } elseif ($request->input('type') === 'link') {
                $query->whereNotNull('link_url');
            }
        }

        $documents = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Document::count(),
            'with_file' => Document::whereNotNull('file_path')->count(),
            'with_link' => Document::whereNotNull('link_url')->count(),
            'total_downloads' => Document::sum('download_count'),
        ];

        $categories = Document::whereNotNull('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.documents.index', compact('documents', 'stats', 'categories'));
    }

    public function create(): View
    {
        $existingCategories = Document::whereNotNull('category')->distinct()->pluck('category')->filter()->values();
        return view('admin.documents.create', compact('existingCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:100'],
            'link_url'    => ['nullable', 'url', 'max:2048'],
            // NO RESTRICTION ON FILE FORMAT: ppt, doc, xls, pdf, png, hwp, mp4, etc. are all accepted
            'file'        => ['nullable', 'file', 'max:512000'], // up to 500MB
            'is_active'   => ['nullable', 'boolean'],
        ]);

        if (!$request->hasFile('file') && !$request->filled('link_url')) {
            return back()->withInput()->with('error', 'Please provide either an uploaded file or an external reference link (or both).');
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;
        $mimeType = null;

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = strtolower($file->getClientOriginalExtension());
            $fileSize = $file->getSize();
            $mimeType = $file->getClientMimeType();

            // Store cleanly in public storage
            $filePath = $file->store('documents', 'public');
        }

        Document::create([
            'title'          => $request->input('title'),
            'description'    => $request->input('description'),
            'category'       => $request->input('category') ?: 'General',
            'link_url'       => $request->input('link_url'),
            'file_path'      => $filePath,
            'file_name'      => $fileName,
            'file_type'      => $fileType,
            'file_size'      => $fileSize,
            'mime_type'      => $mimeType,
            'is_active'      => $request->boolean('is_active', true),
            'download_count' => 0,
            'created_by'     => auth()->id(),
        ]);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document / Resource uploaded successfully.');
    }

    public function edit(Document $document): View
    {
        $existingCategories = Document::whereNotNull('category')->distinct()->pluck('category')->filter()->values();
        return view('admin.documents.edit', compact('document', 'existingCategories'));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:100'],
            'link_url'    => ['nullable', 'url', 'max:2048'],
            'file'        => ['nullable', 'file', 'max:512000'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'category'    => $request->input('category') ?: 'General',
            'link_url'    => $request->input('link_url'),
            'is_active'   => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // Delete old file if present
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $updateData['file_name'] = $file->getClientOriginalName();
            $updateData['file_type'] = strtolower($file->getClientOriginalExtension());
            $updateData['file_size'] = $file->getSize();
            $updateData['mime_type'] = $file->getClientMimeType();
            $updateData['file_path'] = $file->store('documents', 'public');
        }

        $document->update($updateData);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document): BinaryFileResponse|StreamedResponse|RedirectResponse
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            if ($document->link_url) {
                return redirect()->away($document->link_url);
            }
            return back()->with('error', 'File not found in storage.');
        }

        $document->increment('download_count');

        $downloadFilename = $document->file_name ?: ('document_' . $document->id . '.' . ($document->file_type ?: 'dat'));

        return Storage::disk('public')->download($document->file_path, $downloadFilename);
    }
}
