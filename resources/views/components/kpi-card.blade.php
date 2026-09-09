@props([
    'title' => 'Metric',
    'value' => '0',
    'percentage' => '+0%',
    'isPositive' => true,
    'subtext' => '',
    'accentColor' => 'bg-reiac-navy'
])

<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
    <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $title }}</span>
        <span class="{{ $isPositive ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} text-[11px] font-bold px-2 py-0.5 rounded-full flex items-center space-x-1">
            <span>{{ $percentage }}</span>
        </span>
    </div>
    <div class="mt-3">
        <span class="text-2xl font-black text-reiac-navy">{{ $value }}</span>
    </div>
    @if($subtext)
        <div class="mt-2 text-[11px] text-slate-400">{{ $subtext }}</div>
    @endif
    <div class="absolute bottom-0 left-0 right-0 h-1 {{ $accentColor }}"></div>
</div>