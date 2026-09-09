<section class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

    {{-- Header --}}
    <div class="flex justify-between">

        <h3 class="font-bold text-slate-900">
            Trending Topics
        </h3>

        <a
            href="#"
            class="text-xs font-bold text-[#0d3c81]"
        >
            View All
        </a>

    </div>

    {{-- Trending Topics --}}
    <div class="mt-4 space-y-4 text-sm">

        @foreach (
            [
                ['Canada Visa', '1.2k'],
                ['Study In UK', '982'],
                ['Scholarships', '756'],
                ['Australia PR', '642'],
                ['Part Time Jobs', '532'],
            ] as $topic
        )

            <div class="flex">

                {{-- Hashtag --}}
                <span class="mr-4 text-[#0d5ab3]">
                    #
                </span>

                {{-- Topic --}}
                <span class="flex-1 font-medium">
                    {{ $topic[0] }}
                </span>

                {{-- Post Count --}}
                <span class="text-xs text-slate-500">
                    {{ $topic[1] }} posts
                </span>

            </div>

        @endforeach

    </div>

</section>