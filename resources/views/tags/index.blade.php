@extends('layouts.admin')

@section('title', 'Tag Management | REIAC')

@section('content')

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div
    x-data="tagManager()"
    class="space-y-6"
>

    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}

    @if(session('success'))

        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3500)"
            class="fixed right-5 bottom-5 z-[100] flex items-center gap-3 bg-slate-900 text-white px-5 py-3 rounded-xl shadow-2xl"
        >

            <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>

            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>

            <button
                type="button"
                @click="show = false"
                class="ml-2 text-slate-400 hover:text-white"
            >
                ×
            </button>

        </div>

    @endif


    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}

    @if($errors->any())

        <div class="bg-red-50 border border-red-200 rounded-xl p-4">

            <div class="flex items-start gap-3">

                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold">
                    !
                </div>

                <div>

                    <h3 class="text-sm font-bold text-red-700">
                        Please fix the following errors
                    </h3>

                    <ul class="mt-1 text-xs text-red-600 list-disc ml-4">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>

            <h1 class="text-2xl font-bold text-slate-900">
                Tag Management
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Create, edit and manage community discussion tags.
            </p>

        </div>


        {{-- CREATE BUTTON --}}

        <button
            type="button"
            @click="openCreateModal()"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold shadow-sm transition"
        >

            <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Create Tag

        </button>

    </div>


    {{-- =========================================================
        STAT CARDS
    ========================================================== --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- Total Tags --}}

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Total Tags
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-900">
                        {{ number_format($tags->total()) }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-amber-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 7h.01M3 6a2 2 0 012-2h6l10 10a2 2 0 010 3l-4 4a2 2 0 01-3 0L4 11V6z"
                        />
                    </svg>

                </div>

            </div>

        </div>


        {{-- Showing --}}

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Showing
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-900">
                        {{ $tags->count() }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-sky-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>

                </div>

            </div>

        </div>


        {{-- Tagged Posts --}}

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Tagged Posts
                    </p>

                    <p class="mt-2 text-3xl font-extrabold text-slate-900">
                        {{ number_format($tags->sum('posts_count')) }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-emerald-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A2 2 0 0114 3.586L19.414 9A2 2 0 0120 10.414V19a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        SEARCH
    ========================================================== --}}

    <form
        method="GET"
        action="{{ route('tags.index') }}"
        class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm"
    >

        <div class="flex flex-col md:flex-row gap-3">

            <div class="relative flex-1">

                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search tags by name or slug..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10"
                >

            </div>

            <button
                type="submit"
                class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold"
            >
                Search
            </button>

            @if($search)

                <a
                    href="{{ route('tags.index') }}"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold text-center"
                >
                    Clear
                </a>

            @endif

        </div>

    </form>


    {{-- =========================================================
        TABLE
    ========================================================== --}}

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-100">

            <h2 class="text-sm font-bold text-slate-900">
                All Tags
            </h2>

            <p class="text-xs text-slate-400 mt-0.5">
                Manage tags used throughout the community.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead>

                    <tr class="bg-slate-50 border-b border-slate-200">

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            #
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            Tag
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            Slug
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 text-center">
                            Posts
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            Created
                        </th>

                        <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($tags as $tag)

                        <tr class="hover:bg-slate-50/70 transition">

                            {{-- ID --}}

                            <td class="px-5 py-4">

                                <span class="text-xs font-bold text-slate-400">
                                    {{ $tag->id }}
                                </span>

                            </td>


                            {{-- NAME --}}

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">

                                        <svg
                                            class="w-4 h-4 text-amber-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 7h.01M3 6a2 2 0 012-2h6l10 10a2 2 0 010 3l-4 4a2 2 0 01-3 0L4 11V6z"
                                            />
                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-sm font-bold text-slate-900">
                                            {{ $tag->name }}
                                        </p>

                                        <p class="text-[10px] text-slate-400">
                                            Tag #{{ $tag->id }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- SLUG --}}

                            <td class="px-5 py-4">

                                <span class="inline-flex px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono">
                                    #{{ $tag->slug }}
                                </span>

                            </td>


                            {{-- POSTS --}}

                            <td class="px-5 py-4 text-center">

                                <span class="inline-flex min-w-8 justify-center px-2.5 py-1 bg-sky-50 text-sky-700 rounded-lg text-xs font-bold">
                                    {{ $tag->posts_count }}
                                </span>

                            </td>


                            {{-- CREATED --}}

                            <td class="px-5 py-4">

                                <p class="text-xs font-semibold text-slate-700">
                                    {{ $tag->created_at->format('d M Y') }}
                                </p>

                                <p class="text-[10px] text-slate-400">
                                    {{ $tag->created_at->format('h:i A') }}
                                </p>

                            </td>


                            {{-- ACTIONS --}}

                            <td class="px-5 py-4">

                                <div class="flex items-center justify-end gap-2">


                                    {{-- =================================================
                                        EDIT BUTTON
                                        NO @json()
                                    ================================================== --}}

                                    <button
                                        type="button"

                                        data-id="{{ $tag->id }}"
                                        data-name="{{ $tag->name }}"
                                        data-slug="{{ $tag->slug }}"
                                        data-posts-count="{{ $tag->posts_count }}"

                                        @click="openEditModal($el)"

                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition"
                                    >

                                        <svg
                                            class="w-3.5 h-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 7.5-7.5z"
                                            />
                                        </svg>

                                        Edit

                                    </button>


                                    {{-- =================================================
                                        DELETE BUTTON
                                    ================================================== --}}

                                    <button
                                        type="button"

                                        data-id="{{ $tag->id }}"
                                        data-name="{{ $tag->name }}"

                                        @click="openDeleteModal($el)"

                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition"
                                    >

                                        <svg
                                            class="w-3.5 h-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"
                                            />
                                        </svg>

                                        Delete

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5 py-14 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mb-3">

                                        <svg
                                            class="w-6 h-6 text-slate-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 7h.01M3 6a2 2 0 012-2h6l10 10a2 2 0 010 3l-4 4a2 2 0 01-3 0L4 11V6z"
                                            />
                                        </svg>

                                    </div>

                                    <h3 class="text-sm font-bold text-slate-700">
                                        No tags found
                                    </h3>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Create your first tag to get started.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        @if($tags->hasPages())

            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">

                {{ $tags->links() }}

            </div>

        @endif

    </div>


    {{-- =========================================================
        CREATE MODAL
    ========================================================== --}}

    <div
        x-show="createModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4"
    >

        <div
            x-show="createModalOpen"
            x-transition
            @click.outside="closeCreateModal()"
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden"
        >

            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">

                <div>

                    <h3 class="text-lg font-bold text-slate-900">
                        Create Tag
                    </h3>

                    <p class="text-xs text-slate-400 mt-1">
                        Add a new community tag.
                    </p>

                </div>

                <button
                    type="button"
                    @click="closeCreateModal()"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400"
                >
                    ×
                </button>

            </div>


            <form
                method="POST"
                action="{{ route('tags.store') }}"
                class="p-6"
            >

                @csrf

                <label class="block text-xs font-bold text-slate-700 mb-2">
                    Tag Name
                </label>

                <input
                    type="text"
                    name="name"
                    required
                    maxlength="100"
                    value="{{ old('name') }}"
                    placeholder="e.g. Study Abroad"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:bg-white focus:border-amber-500"
                >

                <p class="text-[10px] text-slate-400 mt-2">
                    Slug will be generated automatically.
                </p>


                <div class="flex justify-end gap-2 mt-6">

                    <button
                        type="button"
                        @click="closeCreateModal()"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold"
                    >
                        Create Tag
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        EDIT MODAL
    ========================================================== --}}

    <div
        x-show="editModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4"
    >

        <div
            x-show="editModalOpen"
            x-transition
            @click.outside="closeEditModal()"
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden"
        >

            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">

                <div>

                    <h3 class="text-lg font-bold text-slate-900">
                        Edit Tag
                    </h3>

                    <p class="text-xs text-slate-400 mt-1">
                        Update tag information.
                    </p>

                </div>

                <button
                    type="button"
                    @click="closeEditModal()"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400"
                >
                    ×
                </button>

            </div>


            <form
                x-ref="editForm"
                method="POST"
                :action="editUrl"
                class="p-6"
            >

                @csrf

                @method('PUT')


                <label class="block text-xs font-bold text-slate-700 mb-2">
                    Tag Name
                </label>

                <input
                    type="text"
                    name="name"
                    x-model="selectedTag.name"
                    required
                    maxlength="100"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:bg-white focus:border-amber-500"
                >


                <div class="mt-4">

                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Current Slug
                    </label>

                    <div class="px-4 py-3 bg-slate-100 rounded-xl">

                        <span
                            class="text-xs font-mono text-slate-600"
                            x-text="'#' + selectedTag.slug"
                        ></span>

                    </div>

                    <p class="text-[10px] text-slate-400 mt-2">
                        Slug will be regenerated automatically.
                    </p>

                </div>


                <div class="mt-4 flex items-center justify-between px-4 py-3 bg-sky-50 rounded-xl">

                    <span class="text-xs font-semibold text-sky-700">
                        Posts using this tag
                    </span>

                    <span
                        class="text-sm font-extrabold text-sky-800"
                        x-text="selectedTag.posts_count"
                    ></span>

                </div>


                <div class="flex justify-end gap-2 mt-6">

                    <button
                        type="button"
                        @click="closeEditModal()"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        @click="editConfirmOpen = true"
                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold"
                    >
                        Update Tag
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        EDIT CONFIRMATION MODAL
    ========================================================== --}}

    <div
        x-show="editConfirmOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
    >

        <div
            x-show="editConfirmOpen"
            x-transition
            @click.outside="editConfirmOpen = false"
            class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6"
        >

            <div class="flex items-start gap-4">

                <div class="w-11 h-11 flex-shrink-0 rounded-full bg-amber-50 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-amber-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
                        />
                    </svg>

                </div>

                <div>

                    <h3 class="text-base font-bold text-slate-900">
                        Update this tag?
                    </h3>

                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">

                        Are you sure you want to update

                        <strong
                            class="text-slate-700"
                            x-text="selectedTag.name"
                        ></strong>

                        ?

                    </p>

                </div>

            </div>


            <div class="flex justify-end gap-2 mt-6">

                <button
                    type="button"
                    @click="editConfirmOpen = false"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    @click="submitEdit()"
                    class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold"
                >
                    Yes, Update
                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
        DELETE CONFIRMATION MODAL
    ========================================================== --}}

    <div
        x-show="deleteModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
    >

        <div
            x-show="deleteModalOpen"
            x-transition
            @click.outside="closeDeleteModal()"
            class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6"
        >

            <div class="flex items-start gap-4">

                <div class="w-11 h-11 flex-shrink-0 rounded-full bg-red-50 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z"
                        />
                    </svg>

                </div>


                <div>

                    <h3 class="text-base font-bold text-slate-900">
                        Delete Tag?
                    </h3>

                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">

                        Are you sure you want to permanently delete

                        <strong
                            class="text-slate-700"
                            x-text="deleteTag.name"
                        ></strong>

                        ?

                    </p>

                    <p class="text-[10px] text-red-500 mt-2">
                        This action cannot be undone.
                    </p>

                </div>

            </div>


            {{-- DIRECT DELETE FORM --}}

            <form
                method="POST"
                :action="deleteUrl"
                class="flex justify-end gap-2 mt-6"
            >

                @csrf

                @method('DELETE')

                <button
                    type="button"
                    @click="closeDeleteModal()"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold"
                >
                    Delete Tag
                </button>

            </form>

        </div>

    </div>

</div>


{{-- =============================================================
    ALPINE COMPONENT
============================================================= --}}

<script>

function tagManager() {

    return {

        createModalOpen: false,

        editModalOpen: false,

        editConfirmOpen: false,

        deleteModalOpen: false,


        selectedTag: {

            id: null,

            name: '',

            slug: '',

            posts_count: 0

        },


        deleteTag: {

            id: null,

            name: ''

        },


        editUrl: '',

        deleteUrl: '',


        {{-- ==============================
            CREATE
        =============================== --}}

        openCreateModal() {

            this.createModalOpen = true;

        },


        closeCreateModal() {

            this.createModalOpen = false;

        },


        {{-- ==============================
            EDIT
        =============================== --}}

        openEditModal(button) {

            this.selectedTag = {

                id: button.dataset.id,

                name: button.dataset.name,

                slug: button.dataset.slug,

                posts_count: button.dataset.postsCount

            };


            this.editUrl =
                '{{ url('/tags') }}/' +
                this.selectedTag.id;


            this.editModalOpen = true;

        },


        closeEditModal() {

            this.editModalOpen = false;

            this.editConfirmOpen = false;

        },


        submitEdit() {

            this.editConfirmOpen = false;

            this.$refs.editForm.submit();

        },


        {{-- ==============================
            DELETE
        =============================== --}}

        openDeleteModal(button) {

            this.deleteTag = {

                id: button.dataset.id,

                name: button.dataset.name

            };


            this.deleteUrl =
                '{{ url('/tags') }}/' +
                this.deleteTag.id;


            this.deleteModalOpen = true;

        },


        closeDeleteModal() {

            this.deleteModalOpen = false;

        }

    };

}

</script>

@endsection