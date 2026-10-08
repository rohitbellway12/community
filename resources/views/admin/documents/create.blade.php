@extends('layouts.admin')

@section('title', 'Upload New Document / Resource')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Upload Document or Link</h1>
            <p class="text-xs text-slate-500 mt-1">Add files in any format (PDF, PPT, Word, Excel, HWP, Images, Videos, etc.) or external clickable links.</p>
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

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- UPLOAD FORM --}}
    <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data"
          x-data="{
              filesList: [],
              linkUrl: '{{ old('link_url', '') }}',
              handleFiles(event) {
                  const files = event.target.files;
                  this.filesList = [];
                  if (files && files.length > 0) {
                      for (let i = 0; i < files.length; i++) {
                          const file = files[i];
                          const parts = file.name.split('.');
                          const ext = parts.length > 1 ? parts.pop().toUpperCase() : 'FILE';
                          const size = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                          this.filesList.push({ name: file.name, ext: ext, size: size });
                      }
                  }
              }
          }"
          class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-6">
        @csrf

        {{-- 1. TITLE --}}
        <div>
            <label for="title" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                Document Title <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                   placeholder="e.g. TOPIK II Grammar Master Sheet, Student Visa Guide, EPS-TOPIK Question Set"
                   class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-reiac-navy focus:outline-hidden font-medium">
            <p class="text-[11px] text-slate-400 mt-1">A clear, descriptive title visible to students and users.</p>
        </div>

        {{-- 2. CATEGORY --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                    Category / Subject
                </label>
                <input type="text" id="category" name="category" value="{{ old('category', 'General') }}" list="categoryList"
                       placeholder="e.g. Study Materials, Official Forms, Exam Papers"
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
                <p class="text-[11px] text-slate-400 mt-1">Select an existing category or type a new one.</p>
            </div>

            <div class="flex items-center pt-6">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-5 h-5 rounded text-reiac-navy border-slate-300 focus:ring-reiac-navy cursor-pointer">
                    <span class="text-xs font-extrabold text-slate-800">Publish immediately (Active for students)</span>
                </label>
            </div>
        </div>

        {{-- 3. DESCRIPTION --}}
        <div>
            <label for="description" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                Description & Instructions (Optional)
            </label>
            <textarea id="description" name="description" rows="3"
                      placeholder="Brief overview of this document or notes for students who download it..."
                      class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-reiac-navy focus:outline-hidden">{{ old('description') }}</textarea>
        </div>

        {{-- 4. UNIVERSAL FILE UPLOADER (ALL FORMATS SUPPORTED) --}}
        <div class="border-t border-slate-200 pt-5">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700">
                    File Attachment (Universal Format Support)
                </label>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                    All Formats Accepted
                </span>
            </div>

            <div class="border-2 border-dashed border-slate-300 hover:border-reiac-navy rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-slate-50 transition relative cursor-pointer">
                <input type="file" name="files[]" id="files" multiple @change="handleFiles($event)"
                       class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10">

                <template x-if="filesList.length === 0">
                    <div>
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center text-xl mb-3 shadow-xs">
                            📂
                        </div>
                        <p class="text-xs font-extrabold text-slate-800">
                            Click to browse or drag & drop files here (You can select multiple files: 2, 3 or more)
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1">
                            PDF, Word (DOC/DOCX), Excel, PPT, HWP, Images, Videos, Audio, ZIP (Up to 500MB each)
                        </p>
                    </div>
                </template>

                <template x-if="filesList.length > 0">
                    <div class="space-y-2 relative z-20 pointer-events-none">
                        <div class="text-xs font-black text-emerald-700 mb-2">
                            ✓ <span x-text="filesList.length"></span> File(s) selected:
                        </div>
                        <template x-for="(f, idx) in filesList" :key="idx">
                            <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl bg-white border border-slate-200 shadow-xs max-w-lg mx-auto">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white font-black text-[10px] flex items-center justify-center shrink-0" x-text="f.ext"></span>
                                    <div class="text-left min-w-0">
                                        <div class="text-xs font-extrabold text-slate-900 truncate" x-text="f.name"></div>
                                        <div class="text-[10px] text-slate-500 font-mono" x-text="f.size"></div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-600 shrink-0">Ready</span>
                            </div>
                        </template>
                        <p class="text-[11px] text-slate-400 mt-2">Click to select different or additional files</p>
                    </div>
                </template>
            </div>
            <p class="text-[11px] text-slate-400 mt-1.5">No restriction on file extensions. Select multiple files at once using Ctrl/Shift.</p>
        </div>

        {{-- 5. CLICKABLE EXTERNAL LINK --}}
        <div class="border-t border-slate-200 pt-5">
            <div class="flex items-center justify-between mb-2">
                <label for="link_url" class="block text-xs font-black uppercase tracking-wider text-slate-700">
                    External Clickable Link / URL (Optional)
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
                       placeholder="https://drive.google.com/... or https://youtube.com/... or official portal"
                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-reiac-navy focus:outline-hidden font-mono">
            </div>

            <p class="text-[11px] text-slate-400 mt-1">
                If provided, students will see a direct <strong>Clickable Button</strong> that opens this link in a new browser tab.
            </p>

            {{-- LIVE LINK PREVIEW --}}
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
                <span>Upload & Publish Document</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>

    </form>

</div>
@endsection
