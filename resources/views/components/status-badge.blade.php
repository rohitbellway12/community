@props(['status' => 'Active'])

@php
    $statusLower = strtolower($status);
    $classes = match($statusLower) {
        'active', 'published', 'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'pending', 'under review' => 'bg-amber-50 text-amber-700 border-amber-200',
        'blocked', 'deleted', 'urgent' => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-slate-50 text-slate-700 border-slate-200',
    };
@endphp

<span class="px-2.5 py-0.5 border rounded-full text-[10px] font-bold inline-flex items-center space-x-1 {{ $classes }}">
    <span class="w-1.5 h-1.5 rounded-full fill-current"></span>
    <span>{{ ucfirst($status) }}</span>
</span>