@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto">
    
    {{-- PENDING MODERATION ALERT BANNER --}}
    @if($pendingReportsCount > 0)
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 font-bold shrink-0">
                    ⚠️
                </span>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">Moderation Attention Required</h3>
                    <p class="text-xs text-amber-700">There are <strong class="font-black">{{ $pendingReportsCount }} pending user reports</strong> waiting for review.</p>
                </div>
            </div>
            <a href="{{ route('admin.reports', ['status' => 'pending']) }}"
               class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition shrink-0">
                Review Reports →
            </a>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Community Overview</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time statistics, engagement metrics & growth analytics for REIAC Community</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                System Healthy
            </div>
        </div>
    </div>

    {{-- STATS CARDS (OVERALL) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Members</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalMembers) }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">Registered Users</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Posts</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalPosts) }}</div>
            <div class="text-[11px] text-blue-600 font-semibold mt-0.5">Discussions</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Comments</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalComments) }}</div>
            <div class="text-[11px] text-purple-600 font-semibold mt-0.5">Replies given</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Categories</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalCategories) }}</div>
            <a href="{{ route('admin.categories.index') }}" class="text-[11px] text-amber-600 font-semibold mt-0.5 hover:underline block">Manage topics →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Groups</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalGroups) }}</div>
            <a href="{{ route('admin.groups.index') }}" class="text-[11px] text-indigo-600 font-semibold mt-0.5 hover:underline block">Inspect groups →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Reports</div>
            <div class="text-2xl font-extrabold {{ $pendingReportsCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1.5">
                {{ number_format($pendingReportsCount) }}
            </div>
            <a href="{{ route('admin.reports') }}" class="text-[11px] text-rose-600 font-semibold mt-0.5 hover:underline block">View reports →</a>
        </div>
    </div>

    {{-- TODAY'S ACTIVITY HIGHLIGHT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Today Registrations --}}
        <div class="bg-gradient-to-br from-emerald-50/70 via-white to-white p-5 rounded-2xl border border-emerald-100/90 shadow-xs flex items-center justify-between">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Today's Registrations</span>
                </div>
                <div class="text-3xl font-black text-slate-900 mt-2">
                    {{ number_format($todayRegistrations) }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    New user accounts created today
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center shrink-0 text-xl font-black">
                👥
            </div>
        </div>

        {{-- Today Posts --}}
        <div class="bg-gradient-to-br from-blue-50/70 via-white to-white p-5 rounded-2xl border border-blue-100/90 shadow-xs flex items-center justify-between">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-xs font-bold text-blue-800 uppercase tracking-wider">Today's Posts</span>
                </div>
                <div class="text-3xl font-black text-slate-900 mt-2">
                    {{ number_format($todayPosts) }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    New discussions published today
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100/80 text-blue-600 flex items-center justify-center shrink-0 text-xl font-black">
                ✍️
            </div>
        </div>

        {{-- Today Active Users --}}
        <div class="bg-gradient-to-br from-purple-50/70 via-white to-white p-5 rounded-2xl border border-purple-100/90 shadow-xs flex items-center justify-between">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    <span class="text-xs font-bold text-purple-800 uppercase tracking-wider">Today's Active Users</span>
                </div>
                <div class="text-3xl font-black text-slate-900 mt-2">
                    {{ number_format($todayActiveUsers) }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    Users active or interacting today
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-100/80 text-purple-600 flex items-center justify-center shrink-0 text-xl font-black">
                ⚡
            </div>
        </div>
    </div>

    {{-- INTERACTIVE ANALYTICS GRAPH CARD --}}
    <div
        class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 sm:p-6"
        x-data="analyticsDashboard(@js($initialChartData))"
    >
        {{-- Card Header with Filter Controls --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    <h2 class="text-base font-extrabold text-slate-900">Growth & Activity Analytics</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Compare new registrations, discussions posted, and active user engagement on the same graph
                </p>
            </div>

            {{-- Filter Controls --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Filter Tabs --}}
                <div class="inline-flex p-1 bg-slate-100/80 rounded-xl border border-slate-200/80 text-xs font-semibold">
                    <button
                        type="button"
                        @click="setFilter('daily')"
                        :class="filter === 'daily' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        Daily
                    </button>
                    <button
                        type="button"
                        @click="setFilter('monthly')"
                        :class="filter === 'monthly' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        Monthly
                    </button>
                    <button
                        type="button"
                        @click="setFilter('yearly')"
                        :class="filter === 'yearly' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        Yearly
                    </button>
                    <button
                        type="button"
                        @click="setFilter('custom')"
                        :class="filter === 'custom' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        Custom Date
                    </button>
                </div>

                {{-- Loading Indicator --}}
                <div x-show="isLoading" x-cloak class="flex items-center gap-1.5 text-xs text-amber-600 font-semibold px-2">
                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Updating...</span>
                </div>
            </div>
        </div>

        {{-- Custom Date Range Picker Form (Shown when Custom Date is active) --}}
        <div
            x-show="showCustomPicker"
            x-cloak
            x-transition
            class="mt-4 p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex flex-wrap items-center gap-3 text-xs"
        >
            <div class="flex items-center gap-2">
                <label for="custom_start_date" class="font-bold text-slate-700">From:</label>
                <input
                    type="date"
                    id="custom_start_date"
                    x-model="customStart"
                    class="bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                >
            </div>
            <div class="flex items-center gap-2">
                <label for="custom_end_date" class="font-bold text-slate-700">To:</label>
                <input
                    type="date"
                    id="custom_end_date"
                    x-model="customEnd"
                    class="bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                >
            </div>
            <button
                type="button"
                @click="applyCustomDate()"
                :disabled="isLoading"
                class="px-3.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold shadow-xs transition active:scale-95 disabled:opacity-50"
            >
                Apply Range
            </button>
            <span class="text-[11px] text-slate-400">
                Tip: If range is more than 90 days, it automatically groups by month.
            </span>
        </div>

        {{-- Interactive Legend Bar & Range Summary --}}
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                {{-- Registrations Legend --}}
                <button
                    type="button"
                    @click="toggleDataset(0)"
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-100/70 transition"
                    :class="{ 'opacity-40 line-through': !isDatasetVisible(0) }"
                    title="Click to toggle Registrations on chart"
                >
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="font-bold text-emerald-900">Registrations:</span>
                    <span class="font-extrabold text-emerald-700" x-text="totals.registrations.toLocaleString()"></span>
                </button>

                {{-- Posts Legend --}}
                <button
                    type="button"
                    @click="toggleDataset(1)"
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg border border-blue-200 bg-blue-50/60 hover:bg-blue-100/70 transition"
                    :class="{ 'opacity-40 line-through': !isDatasetVisible(1) }"
                    title="Click to toggle Posts on chart"
                >
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    <span class="font-bold text-blue-900">Posts:</span>
                    <span class="font-extrabold text-blue-700" x-text="totals.posts.toLocaleString()"></span>
                </button>

                {{-- Active Users Legend --}}
                <button
                    type="button"
                    @click="toggleDataset(2)"
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg border border-purple-200 bg-purple-50/60 hover:bg-purple-100/70 transition"
                    :class="{ 'opacity-40 line-through': !isDatasetVisible(2) }"
                    title="Click to toggle Active Users on chart"
                >
                    <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                    <span class="font-bold text-purple-900">Active Users:</span>
                    <span class="font-extrabold text-purple-700" x-text="totals.active_users.toLocaleString()"></span>
                </button>
            </div>

            <div class="text-[11px] text-slate-400 font-medium">
                Period: <span class="font-bold text-slate-600" x-text="startDate + ' to ' + endDate"></span>
            </div>
        </div>

        {{-- Chart Canvas Container --}}
        <div class="mt-4 relative w-full h-[320px] sm:h-[360px]">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

    {{-- GRID FOR DISCUSSIONS, TOP CONTRIBUTORS & NEW MEMBERS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- RECENT POSTS TABLE (2 cols on large screen) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-900 text-sm">Recent Community Discussions</h2>
                    <a href="{{ route('admin.posts') }}" class="text-xs font-bold text-amber-600 hover:underline">View All →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-3 rounded-l-xl">Title / Post</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Author</th>
                                <th class="p-3 rounded-r-xl">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($recentPosts as $post)
                                <tr>
                                    <td class="p-3 text-slate-900 font-bold max-w-[220px] truncate">
                                        {{ $post->title }}
                                    </td>
                                    <td class="p-3">
                                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[10px] font-bold">
                                            {{ $post->category->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-600">
                                        {{ $post->user->name ?? 'Anonymous' }}
                                    </td>
                                    <td class="p-3 text-slate-400 whitespace-nowrap">
                                        {{ $post->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-slate-400">No recent discussions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.posts') }}" class="w-full block text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Manage All {{ number_format($totalPosts) }} Discussions
                </a>
            </div>
        </div>

        {{-- SIDE COLUMN: TOP CONTRIBUTORS & NEW MEMBERS --}}
        <div class="space-y-6">
            
            {{-- TOP CONTRIBUTORS WIDGET --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-1.5">
                        <span class="text-amber-500 text-sm">⭐</span>
                        <h2 class="font-bold text-slate-900 text-sm">Top Contributors</h2>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Points</span>
                </div>

                <div class="space-y-3">
                    @forelse($topContributors as $idx => $contributor)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-4 font-bold text-slate-400 text-[11px] shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="w-7 h-7 rounded-full bg-reiac-navy text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                    {{ strtoupper(substr($contributor->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 truncate">{{ $contributor->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $contributor->posts_count }} posts • {{ $contributor->comments_count }} comments</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 font-bold text-[11px] shrink-0">
                                {{ $contributor->contributor_points }} pts
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 text-xs py-2">No contributors yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- NEW MEMBERS PANEL --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-900 text-sm">New Members</h2>
                    <a href="{{ route('admin.users') }}" class="text-xs text-slate-400 hover:text-amber-600 font-semibold transition">Manage →</a>
                </div>

                <div class="space-y-3">
                    @forelse($newMembers as $member)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 text-xs shrink-0">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 text-xs truncate">{{ $member->name }}</div>
                                    <div class="text-[10px] text-slate-400 truncate">{{ $member->email }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium shrink-0">
                                {{ $member->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 text-xs py-2">No new members.</div>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.users') }}" class="w-full block text-center py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl transition">
                        View All {{ number_format($totalMembers) }} Users
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('analyticsDashboard', (initialData) => ({
        filter: initialData.filter || 'daily',
        startDate: initialData.start_date || '',
        endDate: initialData.end_date || '',
        customStart: initialData.start_date || '',
        customEnd: initialData.end_date || '',
        showCustomPicker: false,
        isLoading: false,
        totals: initialData.totals || { registrations: 0, posts: 0, active_users: 0 },
        chart: null,

        init() {
            this.$nextTick(() => {
                this.renderChart(initialData);
            });
        },

        setFilter(newFilter) {
            if (newFilter === 'custom') {
                this.showCustomPicker = true;
                this.filter = 'custom';
                return;
            }
            this.showCustomPicker = false;
            this.filter = newFilter;
            this.fetchData({ filter: newFilter });
        },

        applyCustomDate() {
            if (!this.customStart || !this.customEnd) {
                alert('Please select both start and end dates.');
                return;
            }
            this.filter = 'custom';
            this.fetchData({
                filter: 'custom',
                start_date: this.customStart,
                end_date: this.customEnd
            });
        },

        fetchData(params) {
            this.isLoading = true;
            const query = new URLSearchParams(params).toString();
            fetch(`{{ route('admin.dashboard.analytics') }}?${query}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.totals = data.totals;
                this.startDate = data.start_date;
                this.endDate = data.end_date;
                this.updateChart(data);
            })
            .catch(err => {
                console.error('Failed to load analytics data', err);
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        renderChart(data) {
            const canvas = document.getElementById('analyticsChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            
            // Subtle gradient area fills
            const regGrad = ctx.createLinearGradient(0, 0, 0, 320);
            regGrad.addColorStop(0, 'rgba(16, 185, 129, 0.20)');
            regGrad.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

            const postGrad = ctx.createLinearGradient(0, 0, 0, 320);
            postGrad.addColorStop(0, 'rgba(37, 99, 235, 0.20)');
            postGrad.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

            const activeGrad = ctx.createLinearGradient(0, 0, 0, 320);
            activeGrad.addColorStop(0, 'rgba(124, 58, 237, 0.20)');
            activeGrad.addColorStop(1, 'rgba(124, 58, 237, 0.00)');

            this.chart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'New Registrations',
                            data: data.datasets.registrations,
                            borderColor: '#10b981',
                            backgroundColor: regGrad,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3.5,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Posts Created',
                            data: data.datasets.posts,
                            borderColor: '#2563eb',
                            backgroundColor: postGrad,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3.5,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#2563eb',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Active Users',
                            data: data.datasets.active_users,
                            borderColor: '#7c3aed',
                            backgroundColor: activeGrad,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3.5,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#7c3aed',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(11, 19, 43, 0.95)',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 10,
                            boxPadding: 6,
                            usePointStyle: true
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: { size: 11 },
                                maxRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.6)',
                                strokeDash: [4, 4]
                            },
                            ticks: {
                                precision: 0,
                                color: '#64748b',
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        },

        updateChart(data) {
            if (!this.chart) return;
            this.chart.data.labels = data.labels;
            this.chart.data.datasets[0].data = data.datasets.registrations;
            this.chart.data.datasets[1].data = data.datasets.posts;
            this.chart.data.datasets[2].data = data.datasets.active_users;
            this.chart.update();
        },

        toggleDataset(index) {
            if (!this.chart) return;
            const isVisible = this.chart.isDatasetVisible(index);
            this.chart.setDatasetVisibility(index, !isVisible);
            this.chart.update();
        },

        isDatasetVisible(index) {
            if (!this.chart) return true;
            return this.chart.isDatasetVisible(index);
        }
    }));
});
</script>
@endpush