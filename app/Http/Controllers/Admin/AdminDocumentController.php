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
        $query = Document::with(['uploader', 'files'])->latest();

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
            'file'        => ['nullable', 'file', 'max:512000'], // up to 500MB
            'files'       => ['nullable', 'array'],
            'files.*'     => ['file', 'max:512000'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles) && ! $request->filled('link_url')) {
            return back()->withInput()->with('error', 'Please provide either uploaded file(s) or an external reference link (or both).');
        }

        $document = Document::create([
            'title'          => $request->input('title'),
            'description'    => $request->input('description'),
            'category'       => $request->input('category') ?: 'General',
            'link_url'       => $request->input('link_url'),
            'is_active'      => $request->boolean('is_active', true),
            'download_count' => 0,
            'created_by'     => auth()->id(),
        ]);

        $firstDocFile = null;
        foreach ($uploadedFiles as $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('documents', 'public');
                $df = $document->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => strtolower($file->getClientOriginalExtension()),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_path' => $path,
                ]);
                if (! $firstDocFile) {
                    $firstDocFile = $df;
                }
            }
        }

        if ($firstDocFile) {
            $document->update([
                'file_path' => $firstDocFile->file_path,
                'file_name' => $firstDocFile->file_name,
                'file_type' => $firstDocFile->file_type,
                'file_size' => $firstDocFile->file_size,
                'mime_type' => $firstDocFile->mime_type,
            ]);
        }

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document / Resource uploaded successfully.');
    }

    public function edit(Document $document): View
    {
        $document->load('files');
        $existingCategories = Document::whereNotNull('category')->distinct()->pluck('category')->filter()->values();
        return view('admin.documents.edit', compact('document', 'existingCategories'));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'category'          => ['nullable', 'string', 'max:100'],
            'link_url'          => ['nullable', 'url', 'max:2048'],
            'file'              => ['nullable', 'file', 'max:512000'],
            'files'             => ['nullable', 'array'],
            'files.*'           => ['file', 'max:512000'],
            'delete_file_ids'   => ['nullable', 'array'],
            'delete_file_ids.*' => ['integer'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'category'    => $request->input('category') ?: 'General',
            'link_url'    => $request->input('link_url'),
            'is_active'   => $request->boolean('is_active', true),
        ];

        // 1. Delete specific selected files
        if ($request->has('delete_file_ids') && is_array($request->input('delete_file_ids'))) {
            $filesToDelete = $document->files()->whereIn('id', $request->input('delete_file_ids'))->get();
            foreach ($filesToDelete as $df) {
                if ($df->file_path && Storage::disk('public')->exists($df->file_path)) {
                    Storage::disk('public')->delete($df->file_path);
                }
                $df->delete();
            }
        }

        // 2. Remove all files if remove_file is checked
        if ($request->boolean('remove_file')) {
            foreach ($document->files as $df) {
                if ($df->file_path && Storage::disk('public')->exists($df->file_path)) {
                    Storage::disk('public')->delete($df->file_path);
                }
                $df->delete();
            }
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $updateData['file_path'] = null;
            $updateData['file_name'] = null;
            $updateData['file_type'] = null;
            $updateData['file_size'] = null;
            $updateData['mime_type'] = null;
        }

        // 3. Upload new files (multiple allowed!)
        $newFiles = [];
        if ($request->hasFile('files')) {
            $newFiles = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $newFiles = [$request->file('file')];
        }

        foreach ($newFiles as $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('documents', 'public');
                $document->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => strtolower($file->getClientOriginalExtension()),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_path' => $path,
                ]);
            }
        }

        // 4. Synchronize primary file fields with latest file
        $latestFile = $document->files()->latest()->first();
        if ($latestFile) {
            $updateData['file_path'] = $latestFile->file_path;
            $updateData['file_name'] = $latestFile->file_name;
            $updateData['file_type'] = $latestFile->file_type;
            $updateData['file_size'] = $latestFile->file_size;
            $updateData['mime_type'] = $latestFile->mime_type;
        } elseif ($document->files()->count() === 0) {
            $updateData['file_path'] = null;
            $updateData['file_name'] = null;
            $updateData['file_type'] = null;
            $updateData['file_size'] = null;
            $updateData['mime_type'] = null;
        }

        $document->update($updateData);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function deleteFile(Document $document, \App\Models\DocumentFile $file): RedirectResponse
    {
        if ($file->document_id === $document->id) {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();

            $latest = $document->files()->latest()->first();
            $document->update([
                'file_path' => $latest?->file_path,
                'file_name' => $latest?->file_name,
                'file_type' => $latest?->file_type,
                'file_size' => $latest?->file_size,
                'mime_type' => $latest?->mime_type,
            ]);
        }

        return back()->with('success', 'File deleted successfully.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        foreach ($document->files as $df) {
            if ($df->file_path && Storage::disk('public')->exists($df->file_path)) {
                Storage::disk('public')->delete($df->file_path);
            }
        }

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
