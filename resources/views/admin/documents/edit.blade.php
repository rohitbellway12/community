@extends('layouts.admin')

@section('title', 'Edit Document - ' . $document->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Document / Resource</h1>
            <p class="text-xs text-slate-500 mt-1">Update title, description, replace file, or modify external clickable link.</p>
        </div>

        <a href="{{ route('admin.documents.index') }}"
           class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition">
            ← Back to List
        </a>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('error') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- EDIT FORM --}}
    <form action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data"
          x-data="{
              fileName: '',
              fileSize: '',
              fileExt: '',
              linkUrl: '{{ old('link_url', $document->link_url) }}',
              handleFile(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.fileName = file.name;
                      this.fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                      const parts = file.name.split('.');
                      this.fileExt = parts.length > 1 ? parts.pop().toUpperCase() : 'FILE';
                  } else {
                      this.fileName = '';
                      this.fileSize = '';
                      this.fileExt = '';
                  }
              }
          }"
          class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. TITLE --}}
        <div>
            <label for="title" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                Document Title <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="title" name="title" value="{{ old('title', $document->title) }}" required
                   class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-reiac-navy focus:outline-hidden font-medium">
        </div>

        {{-- 2. CATEGORY & STATUS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                    Category / Subject
                </label>
                <input type="text" id="category" name="category" value="{{ old('category', $document->category) }}" list="categoryList"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-reiac-navy focus:outline-hidden">
                <datalist id="categoryList">
                    <option value="General">
                    <option value="Study Materials">
                    <option value="Grammar Notes">
                    <option value="Official Forms">
                    <option value="Exam Papers">
                    <option value="Visa Guidelines">
                    @foreach($existingCategories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
            </div>

            <div class="flex items-center pt-6">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $document->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 rounded text-reiac-navy border-slate-300 focus:ring-reiac-navy cursor-pointer">
                    <span class="text-xs font-extrabold text-slate-800">Published / Active for students</span>
                </label>
            </div>
        </div>

        {{-- 3. DESCRIPTION --}}
        <div>
            <label for="description" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                Description & Instructions
            </label>
            <textarea id="description" name="description" rows="3"
                      class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-reiac-navy focus:outline-hidden">{{ old('description', $document->description) }}</textarea>
        </div>

        {{-- 4. CURRENT FILE & REPLACE UPLOADER --}}
        <div class="border-t border-slate-200 pt-5">
            <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                File Attachment (Any Format: PPT, DOC, PDF, HWP, Image, Video, etc.)
            </label>

            @if($document->file_path)
                @php $badge = $document->file_badge; @endphp
                <div class="p-3.5 mb-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }} shrink-0">
                            {{ $badge['icon'] }} {{ $badge['label'] }}
                        </span>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate">{{ $document->file_name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">
                                Size: {{ $document->formatted_size }} · Downloads: {{ $document->download_count }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.documents.download', $document) }}"
                           class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition">
                            ⬇ Download
                        </a>
                        <a href="{{ $document->file_url }}" target="_blank"
                           class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                            View ↗
                        </a>
                    </div>
                </div>
            @endif

            <div class="border-2 border-dashed border-slate-300 hover:border-reiac-navy rounded-2xl p-5 text-center bg-slate-50/50 hover:bg-slate-50 transition relative cursor-pointer">
                <input type="file" name="file" id="file" @change="handleFile($event)"
                       class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10">

                <template x-if="!fileName">
                    <div>
                        <p class="text-xs font-extrabold text-slate-800">
                            {{ $document->file_path ? 'Click to replace with a new file (Optional)' : 'Click to attach a file' }}
                        </p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Any format supported: PPT, DOC, PDF, HWP, Images, Videos, Audio, ZIP (Up to 500MB)
                        </p>
                    </div>
                </template>

                <template x-if="fileName">
                    <div class="flex items-center justify-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white font-black text-xs flex items-center justify-center shrink-0" x-text="fileExt"></div>
                        <div class="text-left min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate max-w-sm" x-text="fileName"></div>
                            <div class="text-[11px] text-slate-500 font-mono" x-text="fileSize"></div>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 ml-2">✓ New file selected</span>
                    </div>
                </template>
            </div>
        </div>

        {{-- 5. CLICKABLE EXTERNAL LINK --}}
        <div class="border-t border-slate-200 pt-5">
            <div class="flex items-center justify-between mb-2">
                <label for="link_url" class="block text-xs font-black uppercase tracking-wider text-slate-700">
                    External Clickable Link / URL
                </label>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                    Clickable Link
                </span>
            </div>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    🔗
                </div>
                <input type="url" id="link_url" name="link_url" x-model="linkUrl"
                       placeholder="https://drive.google.com/... or https://youtube.com/..."
                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-reiac-navy focus:outline-hidden font-mono">
            </div>

            <template x-if="linkUrl && linkUrl.startsWith('http')">
                <div class="mt-3 p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-emerald-700 font-bold">Link Preview:</span>
                        <span class="font-mono text-emerald-900 truncate" x-text="linkUrl"></span>
                    </div>
                    <a :href="linkUrl" target="_blank" class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-bold text-[10px] uppercase hover:bg-emerald-700 shrink-0">
                        Test Link ↗
                    </a>
                </div>
            </template>
        </div>

        {{-- SUBMIT BUTTONS --}}
        <div class="border-t border-slate-200 pt-6 flex items-center justify-between gap-4">
            <a href="{{ route('admin.documents.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition">
                Cancel
            </a>

            <button type="submit"
                    class="px-8 py-3.5 rounded-xl bg-reiac-navy hover:bg-slate-900 text-reiac-gold font-black text-xs uppercase tracking-wider transition shadow-md cursor-pointer flex items-center gap-2">
                <span>Save Changes</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>

    </form>

</div>
@endsection
