@extends('layouts.admin')

@section('title', 'Edit Service - ' . $service->title)

@section('content')
<div class="p-6 space-y-6 max-w-6xl w-full mx-auto" x-data="serviceEditForm()">

    {{-- SUCCESS / ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs shadow-xs space-y-2">
            <div class="font-extrabold flex items-center gap-2 text-rose-900">
                <span class="w-5 h-5 rounded-md bg-rose-100 text-rose-700 flex items-center justify-center font-black">✕</span>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="list-disc list-inside pl-7 space-y-1 text-rose-700 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TOP BAR / BREADCRUMBS & ACTIONS --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.services.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 transition flex items-center gap-1.5 uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Services Catalog</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-semibold text-slate-500">Edit Listing #{{ $service->id }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">{{ $service->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Modify service description, assigned WhatsApp number, prefilled inquiry text, or thumbnail image.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a
                href="{{ $service->whatsapp_url }}"
                target="_blank"
                rel="noopener noreferrer"
                class="px-4 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs shadow-xs transition flex items-center gap-2"
                title="Test WhatsApp connection"
            >
                <svg class="w-4 h-4 text-emerald-600 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88 3.182 0 5.768-2.587 5.768-5.766.001-3.187-2.575-5.766-5.768-5.766zm9.969 5.828c0 5.523-4.477 10-10 10-1.854 0-3.593-.506-5.089-1.385l-6.911 1.385 1.85-6.621c-1.155-1.583-1.85-3.528-1.85-5.379 0-5.523 4.477-10 10-10s10 4.477 10 10z"/>
                </svg>
                <span>Test WhatsApp Chat</span>
            </a>

            <button
                type="submit"
                form="serviceEditForm"
                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-md shadow-amber-500/25 hover:shadow-lg hover:shadow-amber-500/35 hover:-translate-y-0.5 active:scale-95 transition flex items-center gap-2 cursor-pointer ring-1 ring-amber-400/50"
            >
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    {{-- MAIN EDIT FORM --}}
    <form id="serviceEditForm" action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        {{-- LEFT COLUMN (7 of 12) --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- CARD 1: GENERAL INFORMATION --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-700 font-black text-xs flex items-center justify-center">
                            1
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">General Service Details</h2>
                            <p class="text-[11px] text-slate-500">Service naming, URL handle, and classification.</p>
                        </div>
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
                            class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:bg-white py-2.5 px-3.5 transition"
                        >
                    </div>

                    {{-- URL Slug (Auto Generated Preview Bar) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-slate-800">URL Slug / Web Address</label>
                                <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/80 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span x-text="isCustomSlug ? 'Custom' : 'Automatic'"></span>
                                </span>
                            </div>

                            <button
                                type="button"
                                @click="isCustomSlug = !isCustomSlug; if(!isCustomSlug) generateSlug();"
                                class="text-[11px] font-bold text-slate-500 hover:text-amber-600 transition"
                                x-text="isCustomSlug ? '↺ Auto-generate from Title' : '✎ Edit Manually'"
                            ></button>
                        </div>

                        {{-- Browser URL Mockup --}}
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-100/80 overflow-hidden text-xs font-mono transition"
                             :class="isCustomSlug ? 'bg-white ring-2 ring-amber-400 border-amber-400' : ''">
                            <div class="px-3 py-2 bg-slate-200/70 border-r border-slate-200 text-slate-500 font-semibold text-[11px] flex items-center gap-1 shrink-0 select-none">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11m0 0a8 8 0 001.378 4.472M9 11a3 3 0 116 0 3 3 0 01-6 0z" />
                                </svg>
                                <span>/services/</span>
                            </div>
                            <input
                                type="text"
                                name="slug"
                                x-model="slug"
                                :readonly="!isCustomSlug"
                                placeholder="service-url-slug"
                                class="w-full px-3 py-2 text-xs font-mono bg-transparent border-0 focus:outline-hidden text-slate-700"
                            >
                        </div>
                    </div>

                    {{-- Category Selector --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-800">Service Category</label>
                            <a href="{{ route('admin.service-categories.index') }}" target="_blank" class="text-[11px] font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                                <span>Manage Categories</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                        <div class="relative">
                            <select
                                name="service_category_id"
                                class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:bg-white py-2.5 px-3.5 appearance-none text-slate-700 pr-10"
                            >
                                <option value="">-- Select Category (Optional) --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('service_category_id', $service->service_category_id) == $category->id ? 'selected' : '' }}>
                                        📁 {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Short Description --}}
                    <div>
                        <label class="text-xs font-bold text-slate-800 block mb-1.5">
                            Short Summary / Hook
                        </label>
                        <textarea
                            name="short_description"
                            rows="2"
                            placeholder="Brief hook displayed on catalog browse cards..."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:bg-white py-2.5 px-3.5 leading-relaxed"
                        >{{ old('short_description', $service->short_description) }}</textarea>
                    </div>

                    {{-- In-depth Description --}}
                    <div>
                        <label class="text-xs font-bold text-slate-800 block mb-1.5">
                            Full In-Depth Description &amp; Deliverables
                        </label>
                        <textarea
                            name="description"
                            rows="7"
                            placeholder="Detail out eligibility requirements, document checklists, roadmap, timeline..."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:bg-white py-2.5 px-3.5 leading-relaxed"
                        >{{ old('description', $service->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- CARD 2: MEDIA & BANNER UPLOAD --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-700 font-black text-xs flex items-center justify-center">
                            2
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Service Thumbnail &amp; Media</h2>
                            <p class="text-[11px] text-slate-500">Visual banner displayed in cards and detail pages.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
                        {{-- Live Preview Box --}}
                        <div class="sm:col-span-5">
                            <div class="aspect-4/3 w-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 overflow-hidden flex flex-col items-center justify-center relative group">
                                <template x-if="imagePreview">
                                    <div class="relative w-full h-full">
                                        <img :src="imagePreview" class="w-full h-full object-cover">
                                        <button
                                            type="button"
                                            @click="removeSelectedImage()"
                                            class="absolute top-2 right-2 p-1.5 rounded-lg bg-rose-600 text-white shadow-md hover:bg-rose-700 transition"
                                            title="Remove image"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="text-center p-4 text-slate-400 space-y-1">
                                        <svg class="w-10 h-10 mx-auto opacity-40 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs font-bold text-slate-600">No Image</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Upload controls & remove options --}}
                        <div class="sm:col-span-7 space-y-3">
                            <label class="block text-xs font-bold text-slate-800">Change Service Image</label>
                            <input
                                type="file"
                                name="image"
                                id="serviceImageInput"
                                accept="image/jpeg,image/png,image/webp,image/jpg"
                                @change="handleImageUpload($event)"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer border border-slate-200 rounded-xl bg-slate-50 py-1.5 px-2"
                            >
                            <div class="space-y-1 text-[11px] text-slate-500">
                                <p class="text-slate-600 font-medium">Recommended: 800 × 600 px (4:3 ratio), Max: 5MB</p>
                            </div>

                            @if($service->image)
                                <div class="pt-1">
                                    <label class="inline-flex items-center gap-2 text-xs font-bold text-rose-600 cursor-pointer">
                                        <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                        <span>Delete current image from server</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN (5 of 12) --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- CARD 3: DEDICATED WHATSAPP INTEGRATION --}}
            <div class="bg-gradient-to-b from-emerald-500/10 via-white to-white rounded-2xl border-2 border-emerald-500/30 shadow-xs overflow-hidden">
                <div class="p-4 bg-emerald-600 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88 3.182 0 5.768-2.587 5.768-5.766.001-3.187-2.575-5.766-5.768-5.766zm9.969 5.828c0 5.523-4.477 10-10 10-1.854 0-3.593-.506-5.089-1.385l-6.911 1.385 1.85-6.621c-1.155-1.583-1.85-3.528-1.85-5.379 0-5.523 4.477-10 10-10s10 4.477 10 10z"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider text-white">WhatsApp Lead Integration</h2>
                            <p class="text-[10px] text-emerald-100">Direct chat routing with prefilled inquiry</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Assigned WhatsApp Number --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-extrabold text-slate-800">
                                Assigned WhatsApp Number <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-md">
                                Direct Chat
                            </span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                                📱
                            </span>
                            <input
                                type="text"
                                name="whatsapp_number"
                                x-model="whatsappNumber"
                                value="{{ old('whatsapp_number', $service->whatsapp_number) }}"
                                required
                                placeholder="e.g. +821021482834 or +919876543210"
                                class="w-full pl-8 pr-3 py-2 text-xs font-mono font-bold bg-white border border-emerald-300 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 shadow-2xs"
                            >
                        </div>
                    </div>

                    {{-- Prefilled Inquiry Message --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-extrabold text-slate-800">
                                Prefilled Message Template
                            </label>
                            <button
                                type="button"
                                @click="refreshPrefilledMessage()"
                                class="text-[11px] font-extrabold text-emerald-700 hover:text-emerald-900 transition flex items-center gap-1"
                            >
                                <span>↺ Auto Populate</span>
                            </button>
                        </div>
                        <textarea
                            name="whatsapp_message"
                            x-model="whatsappMessage"
                            rows="3"
                            placeholder="Hello REIAC Consultancy, I am interested in this service..."
                            class="w-full text-xs bg-white border border-emerald-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-emerald-500 py-2.5 px-3 leading-relaxed shadow-2xs text-slate-700"
                        >{{ old('whatsapp_message', $service->whatsapp_message) }}</textarea>
                    </div>

                    {{-- REALISTIC SMARTPHONE CHAT PREVIEW MOCKUP --}}
                    <div class="pt-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-900 shadow-md p-2">
                            <div class="w-16 h-1 rounded-full bg-slate-700 mx-auto mb-2"></div>

                            <div class="rounded-t-xl bg-[#075e54] text-white px-3 py-2 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-amber-400 text-slate-950 font-black text-[10px] flex items-center justify-center shrink-0">
                                    R
                                </div>
                                <div class="min-w-0 flex-1 leading-tight">
                                    <p class="text-xs font-bold truncate">REIAC Consultant</p>
                                    <p class="text-[9px] text-emerald-200 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                                        <span>online</span>
                                    </p>
                                </div>
                                <span class="text-xs opacity-75">📞</span>
                            </div>

                            <div class="bg-[#e5ddd5] p-3 rounded-b-xl min-h-[110px] flex flex-col justify-end space-y-1 bg-opacity-95">
                                <div class="self-end bg-[#dcf8c6] rounded-xl rounded-tr-none p-2.5 max-w-[90%] shadow-2xs text-[11px] text-slate-800 leading-snug">
                                    <p x-text="whatsappMessage || ('Hello REIAC Consultancy, I am interested in ' + (title || 'this service') + '. Please guide me.')"></p>
                                    <div class="flex items-center justify-end gap-1 mt-1 text-[9px] text-slate-500">
                                        <span>Just now</span>
                                        <span class="text-emerald-700 font-bold">✓✓</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Clicks Analytics Pill --}}
                    <div class="pt-2 border-t border-emerald-100 flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-bold">Total Inquiries Initiated:</span>
                        <span class="font-extrabold text-emerald-800 bg-emerald-100 px-2.5 py-1 rounded-lg">
                            {{ $service->click_count }} Clicks
                        </span>
                    </div>
                </div>
            </div>

            {{-- CARD 4: OPTIONAL CONTACT INFORMATION --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">📞</span>
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Alternate Contact (Optional)</h2>
                    </div>
                </div>

                <div class="p-5 space-y-3.5">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Direct Calling Phone</label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $service->phone) }}"
                            placeholder="+82 10-2148-2834"
                            class="w-full text-xs font-medium bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:bg-white py-2 px-3 transition"
                        >
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Department Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $service->email) }}"
                            placeholder="consultant@reiaconsultancy.com"
                            class="w-full text-xs font-medium bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:bg-white py-2 px-3 transition"
                        >
                    </div>
                </div>
            </div>

            {{-- CARD 5: VISIBILITY & ORDERING --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">⚙️</span>
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Listing Ordering &amp; Status</h2>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Display Sort Order</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                name="sort_order"
                                value="{{ old('sort_order', $service->sort_order) }}"
                                min="0"
                                class="w-24 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:bg-white py-2 px-3"
                            >
                            <span class="text-[11px] text-slate-400">Lower numbers appear first.</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-emerald-200/80 bg-emerald-50/50">
                        <label class="relative flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_published"
                                value="1"
                                {{ old('is_published', $service->is_published ? '1' : '0') == '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 rounded-md border-slate-300 focus:ring-emerald-500"
                            >
                            <div>
                                <span class="text-xs font-extrabold text-slate-900 block">Published Listing</span>
                                <span class="text-[11px] text-slate-500 block">Uncheck to unpublish and hide from app/web.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function serviceEditForm() {
    return {
        title: @json(old('title', $service->title)),
        slug: @json(old('slug', $service->slug)),
        isCustomSlug: true,
        whatsappNumber: @json(old('whatsapp_number', $service->whatsapp_number)),
        whatsappMessage: @json(old('whatsapp_message', $service->whatsapp_message)),
        imagePreview: @json($service->image_url),

        init() {
            this.$watch('title', (val) => {
                if (!this.isCustomSlug) {
                    this.slug = this.slugify(val);
                }
            });
        },

        generateSlug() {
            this.slug = this.slugify(this.title);
        },

        slugify(text) {
            return (text || '')
                .toString()
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-');
        },

        refreshPrefilledMessage() {
            const serviceName = this.title ? this.title : 'this service';
            this.whatsappMessage = `Hello REIAC Consultancy, I am interested in ${serviceName}. Please guide me with more details.`;
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
