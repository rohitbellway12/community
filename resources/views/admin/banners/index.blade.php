@extends('layouts.admin')

@section('title', 'Banners Management')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="bannerManagement()">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-800 text-xs shadow-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <span>⚠️</span>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside pl-4 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Landing Page</span>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500">Top Header Banner</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-0.5">Banners Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Upload image or promotional banners to showcase at the very top of the community landing page</p>
        </div>

        <button
            type="button"
            @click="openCreateModal()"
            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition flex items-center gap-2 shrink-0 active:scale-95"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create New Banner</span>
        </button>
    </div>

    {{-- RECOMMENDED SIZE GUIDANCE NOTICE --}}
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/90 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs shadow-xs">
        <div class="flex items-start gap-3">
            <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-lg shrink-0">
                📐
            </span>
            <div>
                <h3 class="font-extrabold text-amber-950 text-sm">Banner Ka Sahi Size (Recommended Dimensions)</h3>
                <p class="text-amber-800 text-xs mt-0.5">
                    Landing page par best fit ke liye <strong>Horizontal / Landscape</strong> banner upload karein:
                    <span class="font-bold text-slate-900 bg-white/90 px-2 py-0.5 rounded border border-amber-300 ml-1">1200 x 300 px</span> ya
                    <span class="font-bold text-slate-900 bg-white/90 px-2 py-0.5 rounded border border-amber-300 ml-1">1400 x 320 px</span>
                    (Aspect Ratio: <strong>4:1</strong> ya <strong>5:1</strong>).
                </p>
            </div>
        </div>
        <div class="text-[11px] text-amber-700 font-medium shrink-0 bg-white/80 px-3 py-1.5 rounded-xl border border-amber-200/60">
            ⚠️ Vertical / Square image na dalein, horizontal wide banner dalein.
        </div>
    </div>

    {{-- STATS & LIVE PREVIEW --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Banners --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Banners</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalBanners) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Created in system</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                🖼️
            </div>
        </div>

        {{-- Active Banners --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Banners</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($activeBannersCount) }}</div>
                <div class="text-xs text-emerald-600 font-medium mt-0.5">Visible to users</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                ✨
            </div>
        </div>

        {{-- Live Banner Status Card --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Current Live Banner</div>
                @if($latestActiveBanner)
                    <div class="text-sm font-bold text-slate-900 truncate mt-1">
                        {{ $latestActiveBanner->title ?: 'Image Banner' }}
                    </div>
                    <div class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live on Landing Page
                    </div>
                @else
                    <div class="text-sm font-bold text-slate-400 mt-1">No Active Banner</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Landing page banner is hidden</div>
                @endif
            </div>
            @if($latestActiveBanner && $latestActiveBanner->image_url)
                <img src="{{ $latestActiveBanner->image_url }}" alt="Live Banner" class="w-16 h-12 rounded-lg object-cover border border-slate-200 shrink-0 ml-2">
            @else
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    📢
                </div>
            @endif
        </div>
    </div>

    {{-- CURRENT LIVE PREVIEW BANNER (IF ACTIVE) --}}
    @if($latestActiveBanner)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Live Banner Preview (How it looks on Landing Page)</h2>
                </div>
                <span class="text-[11px] text-slate-400">Desktop & Mobile responsive</span>
            </div>

            {{-- Render sample banner --}}
            <div class="rounded-2xl overflow-hidden border border-slate-200/80 shadow-md relative group bg-slate-950 text-white w-full">
                @if($latestActiveBanner->image_url)
                    <div class="relative w-full h-36 sm:h-44 md:h-52 max-h-[210px] overflow-hidden">
                        <img
                            src="{{ $latestActiveBanner->image_url }}"
                            alt="{{ $latestActiveBanner->title ?: 'Live Banner' }}"
                            class="w-full h-full object-cover object-center"
                        >

                        @if(!empty($latestActiveBanner->title) || !empty($latestActiveBanner->description) || !empty($latestActiveBanner->button_text))
                            <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-slate-950/90 via-slate-950/60 to-transparent/10 flex items-end md:items-center p-6 sm:p-8 z-20">
                                <div class="max-w-xl space-y-2">
                                    @if($latestActiveBanner->title)
                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug drop-shadow-md">
                                            {{ $latestActiveBanner->title }}
                                        </h3>
                                    @endif

                                    @if($latestActiveBanner->description)
                                        <p class="text-xs sm:text-sm text-slate-200 line-clamp-2 leading-relaxed font-medium drop-shadow-sm">
                                            {{ $latestActiveBanner->description }}
                                        </p>
                                    @endif

                                    @if($latestActiveBanner->button_text || $latestActiveBanner->link_url)
                                        <div class="pt-1">
                                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-400 text-slate-950 font-bold text-xs shadow-md">
                                                {{ $latestActiveBanner->button_text ?: 'Explore Now' }} →
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Text-Only Banner --}}
                    <div class="p-6 md:p-8 bg-gradient-to-r from-[#0B132B] via-[#1C2541] to-[#0B132B] relative flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="space-y-2 max-w-xl z-10">
                            @if($latestActiveBanner->title)
                                <h3 class="text-xl md:text-2xl font-black text-white tracking-tight">
                                    {{ $latestActiveBanner->title }}
                                </h3>
                            @endif

                            @if($latestActiveBanner->description)
                                <p class="text-xs md:text-sm text-slate-300 line-clamp-2">
                                    {{ $latestActiveBanner->description }}
                                </p>
                            @endif

                            @if($latestActiveBanner->button_text || $latestActiveBanner->link_url)
                                <div class="pt-1">
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-400 text-slate-950 font-bold text-xs shadow-md">
                                        {{ $latestActiveBanner->button_text ?: 'Explore Now' }} →
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- BANNERS LIST TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-900 text-sm">All Banners</h2>
            <span class="text-xs text-slate-400 font-medium">{{ $banners->total() }} Total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5">Preview</th>
                        <th class="p-3.5">Title & Subtitle</th>
                        <th class="p-3.5">Link / Button</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Created</th>
                        <th class="p-3.5 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- Preview --}}
                            <td class="p-3.5 pl-5">
                                @if($banner->image_url)
                                    <img src="{{ $banner->image_url }}" alt="Banner" class="w-20 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                @else
                                    <div class="w-20 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 font-bold text-[10px]">
                                        No Image
                                    </div>
                                @endif
                            </td>

                            {{-- Title & Description --}}
                            <td class="p-3.5 max-w-[260px]">
                                <div class="font-bold text-slate-900 text-xs truncate">
                                    {{ $banner->title ?: '(Image Banner - No Title)' }}
                                </div>
                                @if($banner->description)
                                    <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                        {{ $banner->description }}
                                    </div>
                                @endif
                            </td>

                            {{-- Link / Button --}}
                            <td class="p-3.5 max-w-[200px]">
                                @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}" target="_blank" class="text-amber-600 font-bold hover:underline truncate block flex items-center gap-1">
                                        <span class="truncate">{{ $banner->button_text ?: 'Open Link' }}</span>
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <div class="text-[10px] text-slate-400 truncate">{{ $banner->link_url }}</div>
                                @else
                                    <span class="text-slate-400 text-[11px]">No link</span>
                                @endif
                            </td>

                            {{-- Status Toggle --}}
                            <td class="p-3.5">
                                <form action="{{ route('admin.banners.updateStatus', $banner) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="px-2.5 py-1 rounded-full text-[11px] font-bold transition flex items-center gap-1.5 {{ $banner->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}"
                                        title="Click to toggle status"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $banner->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ ucfirst($banner->status) }}
                                    </button>
                                </form>
                            </td>

                            {{-- Created At --}}
                            <td class="p-3.5 text-slate-400 whitespace-nowrap">
                                {{ $banner->created_at->format('d M Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="p-3.5 pr-5 text-right space-x-1.5 whitespace-nowrap">
                                <button
                                    type="button"
                                    @click="openEditModal(@js($banner), '{{ $banner->image_url }}')"
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition"
                                >
                                    Edit
                                </button>

                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <div class="text-3xl mb-2">🖼️</div>
                                <div class="font-bold text-slate-700 text-sm">No banners created yet</div>
                                <p class="text-xs text-slate-400 mt-1">Click "+ Create New Banner" above to upload your first landing page banner.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $banners->links() }}
            </div>
        @endif
    </div>

    {{-- CREATE / EDIT MODAL --}}
    <div
        x-show="modalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.away="modalOpen = false"
            class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all"
        >
            {{-- Modal Header --}}
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base" x-text="isEdit ? 'Edit Banner' : 'Create New Banner'"></h3>
                    <p class="text-xs text-slate-500 mt-0.5">Fill in banner details or upload an image banner</p>
                </div>
                <button @click="modalOpen = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center text-lg font-bold transition">
                    ✕
                </button>
            </div>

            {{-- Modal Form --}}
            <form :action="formAction" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 text-xs">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Banner Image Upload --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-700 block">
                            Banner Image
                        </label>
                        <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded border border-amber-300">
                            Sahi Size: 1200 x 300 px (4:1 Ratio)
                        </span>
                    </div>

                    <div class="p-2.5 bg-amber-50/70 border border-amber-200/80 rounded-xl text-[11px] text-amber-900 flex items-center gap-2">
                        <span class="text-sm shrink-0">💡</span>
                        <span><strong>Tip:</strong> Horizontal (chouda) banner upload karein. Height 200–250px se zyada lambee na ho taaki page clean dikhe.</span>
                    </div>

                    <div class="border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-xl p-4 text-center cursor-pointer transition relative bg-slate-50/60">
                        <input
                            type="file"
                            name="image"
                            accept="image/*"
                            @change="previewImage($event)"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        >
                        <template x-if="imagePreview">
                            <div class="space-y-2">
                                <img :src="imagePreview" alt="Preview" class="max-h-36 mx-auto rounded-lg object-cover shadow-xs border border-slate-200">
                                <p class="text-[11px] text-amber-600 font-bold">Click or drag another image to change</p>
                            </div>
                        </template>
                        <template x-if="!imagePreview">
                            <div class="space-y-1 py-3 text-slate-400">
                                <div class="text-2xl">📤</div>
                                <div class="font-bold text-slate-700">Click to upload or drag & drop</div>
                                <div class="text-[10px]">JPG, PNG, WEBP, GIF up to 5MB</div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Title (Optional) --}}
                <div class="space-y-1.5">
                    <label for="banner_title" class="font-bold text-slate-700 block">
                        Title <span class="text-slate-400 font-normal">(Optional - leave empty for image-only banner)</span>
                    </label>
                    <input
                        type="text"
                        id="banner_title"
                        name="title"
                        x-model="formData.title"
                        placeholder="e.g. Join the REIAC Global Education Summit 2026"
                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                    >
                </div>

                {{-- Description (Optional) --}}
                <div class="space-y-1.5">
                    <label for="banner_desc" class="font-bold text-slate-700 block">
                        Description / Subtitle <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <textarea
                        id="banner_desc"
                        name="description"
                        x-model="formData.description"
                        rows="2"
                        placeholder="e.g. Connect with top international university representatives and counselors this weekend."
                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                    ></textarea>
                </div>

                {{-- Button Text & Link URL Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="banner_button" class="font-bold text-slate-700 block">
                            Button Text <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input
                            type="text"
                            id="banner_button"
                            name="button_text"
                            x-model="formData.button_text"
                            placeholder="e.g. Register Now"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label for="banner_url" class="font-bold text-slate-700 block">
                            Link / Action URL <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input
                            type="text"
                            id="banner_url"
                            name="link_url"
                            x-model="formData.link_url"
                            placeholder="e.g. https://reiac.org/register or /community/groups"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                        >
                    </div>
                </div>

                {{-- Status --}}
                <div class="space-y-1.5 pt-1">
                    <label class="font-bold text-slate-700 block">Status</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="active" x-model="formData.status" class="text-amber-500 focus:ring-amber-500">
                            <span class="font-bold text-emerald-700">Active (Visible)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="inactive" x-model="formData.status" class="text-amber-500 focus:ring-amber-500">
                            <span class="text-slate-500">Inactive (Draft/Hidden)</span>
                        </label>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button
                        type="button"
                        @click="modalOpen = false"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold shadow-xs transition"
                        x-text="isEdit ? 'Update Banner' : 'Publish Banner'"
                    ></button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function bannerManagement() {
    return {
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('admin.banners.store') }}',
        imagePreview: null,
        formData: {
            title: '',
            description: '',
            button_text: '',
            link_url: '',
            status: 'active'
        },

        openCreateModal() {
            this.isEdit = false;
            this.formAction = '{{ route('admin.banners.store') }}';
            this.imagePreview = null;
            this.formData = {
                title: '',
                description: '',
                button_text: '',
                link_url: '',
                status: 'active'
            };
            this.modalOpen = true;
        },

        openEditModal(banner, imageUrl) {
            this.isEdit = true;
            this.formAction = '{{ route('admin.banners.index') }}/' + banner.id;
            this.imagePreview = imageUrl || null;
            this.formData = {
                title: banner.title || '',
                description: banner.description || '',
                button_text: banner.button_text || '',
                link_url: banner.link_url || '',
                status: banner.status || 'active'
            };
            this.modalOpen = true;
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreview = URL.createObjectURL(file);
            }
        }
    };
}
</script>
@endpush
