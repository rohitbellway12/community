@extends('layouts.admin')

@section('title', 'Add New Service')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="serviceCreateForm()">

    {{-- VALIDATION ERRORS --}}
    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs shadow-xs space-y-1">
            <div class="font-extrabold flex items-center gap-2 text-rose-900">
                <span class="w-5 h-5 rounded-md bg-rose-100 text-rose-700 flex items-center justify-center font-black">✕</span>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside pl-7 text-rose-700 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TOP BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.services.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 transition flex items-center gap-1.5 uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Services Catalog</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-semibold text-slate-500">Add Service</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Create Consultancy Service</h1>
            <p class="text-xs text-slate-500 mt-0.5">Add a new service listing with direct WhatsApp inquiry integration.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a
                href="{{ route('admin.services.index') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition"
            >
                Cancel
            </a>
            <button
                type="submit"
                form="serviceCreateForm"
                class="px-6 py-2.5 rounded-xl bg-reiac-gold hover:bg-amber-500 text-reiac-navy font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Save Service</span>
            </button>
        </div>
    </div>

    {{-- MAIN FORM --}}
    <form id="serviceCreateForm" action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        {{-- LEFT COLUMN: Primary Details & Media (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- CARD 1: SERVICE INFORMATION --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Service Information</h2>
                        <p class="text-[11px] text-slate-500">Title, category classification, and descriptions.</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-800">
                                Service Title <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] font-semibold text-slate-400">
                                <span x-text="title.length">0</span>/120
                            </span>
                        </div>
                        <input
                            type="text"
                            name="title"
                            x-model="title"
                            maxlength="120"
                            required
                            placeholder="e.g. F-2-7 Points-Based Resident Visa Consultation"
                            class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 py-2.5 px-3.5 transition"
                        >
                    </div>

                    {{-- Category Selector --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-800">Service Category</label>
                            <a href="{{ route('admin.service-categories.index') }}" target="_blank" class="text-[11px] font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                                <span>+ New Category</span>
                            </a>
                        </div>
                        <select
                            name="service_category_id"
                            x-model="categoryId"
                            class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 py-2.5 px-3.5 text-slate-700"
                        >
                            <option value="">-- Select Category (Optional) --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-name="{{ $category->name }}" {{ old('service_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Short Summary --}}
                    <div>
                        <label class="text-xs font-bold text-slate-800 block mb-1.5">
                            Short Summary (Card Preview)
                        </label>
                        <textarea
                            name="short_description"
                            x-model="shortDescription"
                            rows="2"
                            placeholder="Brief 1-2 sentence overview shown on service cards and preview lists..."
                            class="w-full text-xs bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 py-2.5 px-3.5 leading-relaxed text-slate-700"
                        >{{ old('short_description') }}</textarea>
                    </div>

                    {{-- Full Description --}}
                    <div>
                        <label class="text-xs font-bold text-slate-800 block mb-1.5">
                            Detailed Description
                        </label>
                        <textarea
                            name="description"
                            rows="5"
                            placeholder="Full details, service eligibility, documents required, and process steps..."
                            class="w-full text-xs bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 py-2.5 px-3.5 leading-relaxed text-slate-700"
                        >{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- CARD 2: SERVICE THUMBNAIL (Integrated seamlessly in the main flow) --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Service Thumbnail &amp; Cover Image</h2>
                        <p class="text-[11px] text-slate-500">Visual image displayed on website service cards and catalog preview.</p>
                    </div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded-md">Optional</span>
                </div>

                <div class="p-6">
                    <input
                        type="file"
                        name="image"
                        id="serviceImageInput"
                        accept="image/png,image/jpeg,image/webp,image/jpg"
                        @change="handleImageUpload($event)"
                        class="hidden"
                    >

                    {{-- If NO image selected --}}
                    <div
                        x-show="!imagePreview"
                        @click="triggerFileInput()"
                        class="border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-2xl p-6 flex flex-col sm:flex-row items-center gap-5 cursor-pointer transition bg-slate-50/50 hover:bg-amber-50/20 group"
                    >
                        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="text-center sm:text-left space-y-1">
                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                <span class="text-xs font-extrabold text-slate-800 group-hover:text-amber-700 transition">Click to upload service thumbnail</span>
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">Browse</span>
                            </div>
                            <p class="text-[11px] text-slate-500">Supports PNG, JPG, or WebP up to 5MB.</p>
                            <p class="text-[10px] text-slate-400">Recommended aspect ratio: 16:9 or 800×600 pixels for best card display.</p>
                        </div>
                    </div>

                    {{-- If Image is SELECTED / PREVIEW --}}
                    <div x-show="imagePreview" x-cloak class="flex flex-col sm:flex-row items-center gap-5 p-4 rounded-2xl border border-slate-200 bg-slate-50/60">
                        <div class="w-36 h-24 rounded-xl overflow-hidden border border-slate-200 bg-white shrink-0 shadow-xs">
                            <img :src="imagePreview" alt="Thumbnail Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center sm:text-left space-y-2">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Thumbnail Selected</span>
                                </span>
                                <p class="text-xs font-bold text-slate-800 mt-1">Ready for upload</p>
                            </div>
                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                <button
                                    type="button"
                                    @click="triggerFileInput()"
                                    class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-[11px] shadow-xs transition"
                                >
                                    Change Image
                                </button>
                                <button
                                    type="button"
                                    @click="removeSelectedImage()"
                                    class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[11px] transition"
                                >
                                    ✕ Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 3: WHATSAPP & CONTACT DETAILS --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">WhatsApp Inquiry Setup</h2>
                        <p class="text-[11px] text-slate-500">Dedicated consultant number and prefilled chat message.</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- WhatsApp Number --}}
                    <div>
                        <label class="text-xs font-bold text-slate-800 block mb-1.5">
                            WhatsApp Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                                💬
                            </span>
                            <input
                                type="text"
                                name="whatsapp_number"
                                x-model="whatsappNumber"
                                required
                                placeholder="+82 10-2148-2834"
                                class="w-full pl-9 pr-3.5 py-2.5 text-xs font-bold bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500 text-slate-800"
                            >
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Include country code (e.g. +82 for South Korea, +91 for India).</p>
                    </div>

                    {{-- Prefilled WhatsApp Message --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-800">Prefilled WhatsApp Inquiry Message</label>
                            <button
                                type="button"
                                @click="refreshPrefilledMessage()"
                                class="text-[11px] font-bold text-amber-600 hover:text-amber-700 transition"
                            >
                                ↺ Reset Message
                            </button>
                        </div>
                        <textarea
                            name="whatsapp_message"
                            x-model="whatsappMessage"
                            rows="3"
                            placeholder="Hello REIAC Consultancy, I am interested in this service..."
                            class="w-full text-xs bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500 py-2.5 px-3.5 leading-relaxed text-slate-700"
                        ></textarea>
                    </div>

                    {{-- Secondary Contacts (Phone & Email) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Direct Phone (Optional)</label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="+82 10-2148-2834"
                                class="w-full text-xs bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 py-2.5 px-3 text-slate-700"
                            >
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Email Address (Optional)</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="consultant@reiac.com"
                                class="w-full text-xs bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 py-2.5 px-3 text-slate-700"
                            >
                        </div>
                    </div>
                </div>
            </div>



        </div>

        {{-- RIGHT COLUMN: Status & Live Preview (4 cols) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- CARD 4: PUBLISH & STATUS (With Sliding Toggle Switch) --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-slate-900">Publish &amp; Visibility</h2>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black"
                        :class="isActive ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                    >
                        <span class="w-2 h-2 rounded-full" :class="isActive ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                        <span x-text="isActive ? 'Active' : 'Inactive'"></span>
                    </span>
                </div>

                <div class="p-5 space-y-5">
                    {{-- Status: Interactive Sliding Toggle Switch --}}
                    <div>
                        <div class="flex items-center justify-between p-3.5 rounded-xl border transition"
                             :class="isActive ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                            <div class="pr-2">
                                <span class="text-xs font-bold text-slate-800 block">Service Status</span>
                                <span class="text-[11px] text-slate-500" x-text="isActive ? 'Live on catalog & app' : 'Hidden as draft' "></span>
                            </div>

                            <button
                                type="button"
                                role="switch"
                                :aria-checked="isActive ? 'true' : 'false'"
                                @click="toggleActiveStatus()"
                                class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30"
                                :class="isActive ? 'bg-emerald-500' : 'bg-slate-300'"
                            >
                                <span
                                    class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                                    :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>
                        {{-- Hidden input synced with form submission --}}
                        <input type="hidden" name="status" :value="isActive ? 'active' : 'inactive'">
                        <input type="hidden" name="is_published" :value="isActive ? '1' : '0'">

                        <p class="text-[11px] text-slate-400 mt-2">
                            <span class="font-bold">Active:</span> Service will be immediately visible on public catalog and app.
                            <br>
                            <span class="font-bold">Inactive:</span> Kept as draft, visible only to administrators.
                        </p>
                    </div>

                    {{-- Sort Order --}}
                    <div class="pt-3 border-t border-slate-100">
                        <label class="text-xs font-bold text-slate-800 block mb-1">Display Sort Order</label>
                        <input
                            type="number"
                            name="sort_order"
                            value="{{ old('sort_order', 0) }}"
                            min="0"
                            class="w-full text-xs font-bold bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 py-2.5 px-3 text-slate-800"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Lower numbers (0, 1, 2) appear first in catalog.</p>
                    </div>
                </div>
            </div>

            {{-- CARD 5: LIVE CARD PREVIEW (Shows how it appears to customers) --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs">👁️</span>
                        <h2 class="text-sm font-extrabold text-slate-900">Customer Preview</h2>
                    </div>
                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60">Live</span>
                </div>

                <div class="p-5">
                    {{-- Mini Card Mockup --}}
                    <div class="rounded-2xl border border-slate-200 overflow-hidden bg-white shadow-xs">
                        {{-- Image / Fallback --}}
                        <div class="h-32 w-full bg-slate-100 relative overflow-hidden flex items-center justify-center">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <div class="w-full h-full bg-linear-to-br from-amber-50 via-orange-50 to-amber-100 flex flex-col items-center justify-center text-amber-600/70 p-4 text-center">
                                    <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] font-bold text-amber-700">Thumbnail Preview</span>
                                </div>
                            </template>
                            <span
                                class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full text-[9px] font-black"
                                :class="isActive ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-700 text-white'"
                                x-text="isActive ? 'ACTIVE' : 'DRAFT'"
                            ></span>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-3.5 space-y-2">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/60 inline-block"
                                  x-text="getCategoryName()">
                            </span>

                            <h3 class="text-xs font-bold text-slate-800 line-clamp-1"
                                x-text="title ? title : 'Your Service Title Here'">
                            </h3>

                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed"
                               x-text="shortDescription ? shortDescription : 'Short overview of your service will appear here on customer cards.'">
                            </p>

                            <div class="pt-2">
                                <div class="w-full py-2 px-3 rounded-xl bg-emerald-600 text-white font-bold text-[11px] flex items-center justify-center gap-1.5 shadow-2xs">
                                    <span>💬</span>
                                    <span>Inquire on WhatsApp</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 text-center mt-2.5">Simulated view of the service card in user catalog.</p>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function serviceCreateForm() {
    const initialStatus = @json(old('status', 'active'));
    return {
        title: @json(old('title', '')),
        categoryId: @json(old('service_category_id', '')),
        shortDescription: @json(old('short_description', '')),
        whatsappNumber: @json(old('whatsapp_number', '+82 10-2148-2834')),
        whatsappMessage: @json(old('whatsapp_message', '')),
        isActive: initialStatus === 'active',
        imagePreview: null,

        init() {
            if (!this.whatsappMessage && this.title) {
                this.refreshPrefilledMessage();
            }

            this.$watch('title', (val) => {
                if (!this.whatsappMessage || this.whatsappMessage.startsWith('Hello REIAC Consultancy, I am interested in')) {
                    const serviceName = val ? val : 'this service';
                    this.whatsappMessage = `Hello REIAC Consultancy, I am interested in ${serviceName}. Please guide me with more details.`;
                }
            });
        },

        toggleActiveStatus() {
            this.isActive = !this.isActive;
        },

        getCategoryName() {
            if (!this.categoryId) return 'Consultancy';
            const select = document.querySelector('select[name="service_category_id"]');
            if (select && select.selectedOptions && select.selectedOptions[0]) {
                const text = select.selectedOptions[0].getAttribute('data-name');
                return text || 'Consultancy';
            }
            return 'Consultancy';
        },

        refreshPrefilledMessage() {
            const serviceName = this.title ? this.title : 'this service';
            this.whatsappMessage = `Hello REIAC Consultancy, I am interested in ${serviceName}. Please guide me with more details.`;
        },

        triggerFileInput() {
            const input = document.getElementById('serviceImageInput');
            if (input) {
                input.click();
            }
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removeSelectedImage() {
            this.imagePreview = null;
            const input = document.getElementById('serviceImageInput');
            if (input) {
                input.value = '';
            }
        }
    }
}
</script>
@endpush
@endsection
