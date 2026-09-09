@props(['post'])

<article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

    {{-- Post Header --}}
    <div class="flex gap-3">
       

        {{-- Avatar --}}
        <img
            src="{{ $post['avatar'] }}"
            alt=""
            class="h-10 w-10 rounded-full object-cover"
        >

        <div class="min-w-0 flex-1">

            {{-- User Info & Post Type --}}
            <div class="flex items-start justify-between gap-2">

                <div>
                    <p class="text-sm font-bold text-slate-900">
                        {{ $post['name'] }}
                    </p>

                    <p class="mt-0.5 text-xs text-slate-500">
                        {{ $post['role'] }}

                        <span class="mx-1">•</span>

                        {{ $post['time'] }}
                    </p>
                </div>

                <div class="flex gap-3">

                    <span
                        class="rounded-full px-3 py-1 text-[10px] font-bold {{ $post['typeClass'] }}"
                    >
                        {{ $post['type'] }}
                    </span>

                    <button
                        type="button"
                        class="text-xl leading-none text-slate-500"
                    >
                        •••
                    </button>

                </div>

            </div>

            {{-- Post Content --}}
            <div
                class="mt-3 {{ !empty($post['image'])
                    ? 'sm:grid sm:grid-cols-[minmax(0,1fr)_155px] sm:gap-5'
                    : '' }}"
            >

                <div>

                    {{-- Post Title --}}
                    <a
                        href="{{ route('community.posts.show') }}"
                        class="text-[17px] font-bold leading-5 text-slate-900 hover:text-[#0d3c81]"
                    >
                        {{ $post['title'] }}
                    </a>

                    {{-- Post Body --}}
                    <p class="mt-3 text-sm leading-5 text-slate-600">
                        {{ $post['body'] }}
                    </p>

                    {{-- Post Metadata --}}
                    @if (!empty($post['meta']))
                        <p class="mt-4 text-sm text-slate-500">
                            {!! $post['meta'] !!}
                        </p>
                    @endif

                </div>

                {{-- Post Image --}}
                @if (!empty($post['image']))
                    <img
                        src="{{ $post['image'] }}"
                        alt="Post image"
                        class="mt-4 h-32 w-full rounded-lg object-cover sm:mt-0"
                    >
                @endif

            </div>

            {{-- Post Actions --}}
            <div
                class="mt-4 flex items-center gap-6 border-t border-slate-100 pt-3 text-sm text-slate-600"
            >

                {{-- Like --}}
                <button type="button">
                    ♡
                    <span class="ml-1">
                        {{ $post['likes'] }}
                    </span>
                </button>

                {{-- Comments --}}
                <a href="{{ route('community.posts.show') }}">
                    ▢
                    <span class="ml-1">
                        {{ $post['comments'] }}
                    </span>
                </a>

                {{-- Share --}}
                <button type="button">
                    ↗
                    <span class="ml-1">
                        Share
                    </span>
                </button>

                {{-- Save --}}
                <button
                    type="button"
                    class="ml-auto"
                >
                    ♧
                    <span class="ml-1 hidden sm:inline">
                        Save
                    </span>
                </button>

            </div>

        </div>

    </div>

</article>