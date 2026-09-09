@props([
    'title' => 'Recent Notifications',
    'all' => false,
])

<section class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

    {{-- Header --}}
    <div class="flex justify-between">
        <h3 class="font-bold text-slate-900">
            {{ $title }}
        </h3>

        @unless ($all)
            <a
                href="{{ route('community.notifications') }}"
                class="text-xs font-bold text-[#0d3c81]"
            >
                View All
            </a>
        @endunless
    </div>

    {{-- Notifications --}}
    <div class="mt-4 space-y-4">

        @foreach (
            [
                ['Priya Sharma', 'commented on your post.', '2m ago', 47],
                ['Arjun Patel', 'liked your post.', '10m ago', 12],
                ['Neha Verma', 'replied to your comment.', '25m ago', 44],
                ['', 'Your question has a new answer.', '1h ago', 68],
                ['Vikas Mehta', 'shared your post.', '2h ago', 13],
            ] as $notification
        )

            <div class="flex gap-3">

                {{-- Avatar --}}
                <img
                    src="https://i.pravatar.cc/64?img={{ $notification[3] }}"
                    alt="{{ $notification[0] ?: 'Notification' }}"
                    class="h-8 w-8 rounded-full"
                >

                {{-- Notification Content --}}
                <p class="text-xs leading-4 text-slate-600">

                    @if ($notification[0])
                        <b class="text-slate-800">
                            {{ $notification[0] }}
                        </b>
                    @endif

                    {{ $notification[1] }}

                    <span class="mt-0.5 block text-[10px] text-slate-400">
                        {{ $notification[2] }}
                    </span>

                </p>

            </div>

        @endforeach

    </div>

</section>