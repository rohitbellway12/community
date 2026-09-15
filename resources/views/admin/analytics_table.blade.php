@extends('layouts.admin')

@section('title', 'Analytics Data Table')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="analyticsTablePage(@js($analytics))">
    
    {{-- TOP NAVIGATION & BREADCRUMB --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <span>←</span> Dashboard
                </a>
                <span>/</span>
                <span class="text-slate-700">Analytics Data Table</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <span>📋</span> Growth & Activity Data Table
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Detailed date-wise breakdown of user registrations, discussions published, and active engagement
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                @click="exportCsv()"
                class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-1.5"
                title="Export this data table as CSV"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export CSV</span>
            </button>

            <a
                href="{{ route('admin.dashboard') }}"
                class="px-3.5 py-2 rounded-xl bg-[#0B132B] hover:bg-[#1C2541] text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5"
            >
                <span>📈</span>
                <span>View Graph on Dashboard</span>
            </a>
        </div>
    </div>

    {{-- FILTER & DATE RANGE BAR --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Tabs --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Period:</span>
                <div class="inline-flex p-1 bg-slate-100/90 rounded-xl border border-slate-200 text-xs font-semibold">
                    <button
                        type="button"
                        @click="setFilter('daily')"
                        :class="filter === 'daily' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 rounded-lg transition"
                    >
                        Daily
                    </button>
                    <button
                        type="button"
                        @click="setFilter('monthly')"
                        :class="filter === 'monthly' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 rounded-lg transition"
                    >
                        Monthly
                    </button>
                    <button
                        type="button"
                        @click="setFilter('yearly')"
                        :class="filter === 'yearly' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 rounded-lg transition"
                    >
                        Yearly
                    </button>
                    <button
                        type="button"
                        @click="setFilter('custom')"
                        :class="filter === 'custom' ? 'bg-[#0B132B] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 rounded-lg transition"
                    >
                        Custom Date
                    </button>
                </div>

                {{-- Loading Spinner --}}
                <div x-show="isLoading" x-cloak class="flex items-center gap-1.5 text-xs text-amber-600 font-semibold px-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading data...</span>
                </div>
            </div>

            {{-- Period Badge --}}
            <div class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                <span>Active Range:</span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-bold text-slate-800" x-text="startDate + ' to ' + endDate"></span>
            </div>
        </div>

        {{-- Custom Date Inputs (when Custom is selected) --}}
        <div
            x-show="showCustomPicker"
            x-cloak
            x-transition
            class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3 text-xs"
        >
            <div class="flex items-center gap-2">
                <label for="page_start_date" class="font-bold text-slate-700">From:</label>
                <input
                    type="date"
                    id="page_start_date"
                    x-model="customStart"
                    class="bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                >
            </div>
            <div class="flex items-center gap-2">
                <label for="page_end_date" class="font-bold text-slate-700">To:</label>
                <input
                    type="date"
                    id="page_end_date"
                    x-model="customEnd"
                    class="bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none"
                >
            </div>
            <button
                type="button"
                @click="applyCustomDate()"
                :disabled="isLoading"
                class="px-4 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold shadow-xs transition disabled:opacity-50"
            >
                Apply Range
            </button>
            <span class="text-[11px] text-slate-400">
                Ranges > 90 days automatically group by month.
            </span>
        </div>
    </div>

    {{-- SUMMARY KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Registrations --}}
        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Total Registrations</div>
                <div class="text-2xl font-black text-slate-900 mt-1" x-text="totals.registrations.toLocaleString()"></div>
                <div class="text-[11px] text-slate-400 mt-0.5">In selected period</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-lg">
                👥
            </div>
        </div>

        {{-- Posts --}}
        <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-blue-800 uppercase tracking-wider">Posts Created</div>
                <div class="text-2xl font-black text-slate-900 mt-1" x-text="totals.posts.toLocaleString()"></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Discussions started</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-lg">
                ✍️
            </div>
        </div>

        {{-- Active Users --}}
        <div class="bg-white p-5 rounded-2xl border border-purple-100 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-purple-800 uppercase tracking-wider">Active Users</div>
                <div class="text-2xl font-black text-slate-900 mt-1" x-text="totals.active_users.toLocaleString()"></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Engaged members</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-black text-lg">
                ⚡
            </div>
        </div>

        {{-- Total Combined Actions --}}
        <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-amber-800 uppercase tracking-wider">Combined Total</div>
                <div class="text-2xl font-black text-slate-900 mt-1" x-text="(totals.registrations + totals.posts + totals.active_users).toLocaleString()"></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Total recorded interactions</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-lg">
                📊
            </div>
        </div>
    </div>

    {{-- MAIN DATA TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 sm:p-6 space-y-4">
        {{-- Table Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">All Date Records</h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Showing <span class="font-bold text-slate-700" x-text="filteredRows.length"></span> date interval records
                </p>
            </div>

            {{-- Quick Search Input --}}
            <div class="relative w-full sm:w-64">
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search date..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500"
                >
                <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
            </div>
        </div>

        {{-- Responsive Table Container --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="p-3.5 w-12 text-center">#</th>
                        <th class="p-3.5">Date / Interval</th>
                        <th class="p-3.5 text-emerald-800">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                New Registrations
                            </span>
                        </th>
                        <th class="p-3.5 text-blue-800">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Posts Created
                            </span>
                        </th>
                        <th class="p-3.5 text-purple-800">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                Active Users
                            </span>
                        </th>
                        <th class="p-3.5 text-right font-extrabold text-slate-800">Combined Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <template x-for="(row, idx) in paginatedRows" :key="idx">
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 text-center text-slate-400 font-bold text-[11px]" x-text="(currentPage - 1) * pageSize + idx + 1"></td>
                            <td class="p-3.5 font-bold text-slate-900" x-text="row.date"></td>
                            <td class="p-3.5">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold"
                                    :class="row.registrations > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'text-slate-400'"
                                    x-text="row.registrations"
                                ></span>
                            </td>
                            <td class="p-3.5">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold"
                                    :class="row.posts > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-slate-400'"
                                    x-text="row.posts"
                                ></span>
                            </td>
                            <td class="p-3.5">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold"
                                    :class="row.active_users > 0 ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'text-slate-400'"
                                    x-text="row.active_users"
                                ></span>
                            </td>
                            <td class="p-3.5 text-right font-extrabold text-slate-900" x-text="(row.registrations + row.posts + row.active_users).toLocaleString()"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredRows.length === 0">
                        <td colspan="6" class="p-8 text-center text-slate-400">
                            No records matching your search or selected date range.
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-xs text-slate-900">
                    <tr>
                        <td colspan="2" class="p-3.5 uppercase tracking-wider text-[11px] text-slate-600">Grand Total Sum</td>
                        <td class="p-3.5 text-emerald-700 font-black text-sm" x-text="totals.registrations.toLocaleString()"></td>
                        <td class="p-3.5 text-blue-700 font-black text-sm" x-text="totals.posts.toLocaleString()"></td>
                        <td class="p-3.5 text-purple-700 font-black text-sm" x-text="totals.active_users.toLocaleString()"></td>
                        <td class="p-3.5 text-right font-black text-slate-950 text-sm" x-text="(totals.registrations + totals.posts + totals.active_users).toLocaleString()"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination Controls --}}
        <div x-show="totalPages > 1" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 text-xs">
            <div class="text-slate-400">
                Page <span class="font-bold text-slate-700" x-text="currentPage"></span> of <span class="font-bold text-slate-700" x-text="totalPages"></span>
            </div>
            <div class="inline-flex items-center gap-1">
                <button
                    type="button"
                    @click="currentPage = Math.max(1, currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 font-bold text-slate-700 transition"
                >
                    ← Previous
                </button>
                <template x-for="p in totalPages" :key="p">
                    <button
                        type="button"
                        @click="currentPage = p"
                        :class="currentPage === p ? 'bg-[#0B132B] text-white font-bold' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200'"
                        class="w-8 h-8 rounded-lg text-xs font-semibold transition"
                        x-text="p"
                    ></button>
                </template>
                <button
                    type="button"
                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 font-bold text-slate-700 transition"
                >
                    Next →
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('analyticsTablePage', (initialData) => ({
        filter: initialData.filter || 'daily',
        startDate: initialData.start_date || '',
        endDate: initialData.end_date || '',
        customStart: initialData.start_date || '',
        customEnd: initialData.end_date || '',
        showCustomPicker: (initialData.filter === 'custom'),
        isLoading: false,
        totals: initialData.totals || { registrations: 0, posts: 0, active_users: 0 },
        tableRows: initialData.table_rows || [],
        searchQuery: '',
        currentPage: 1,
        pageSize: 25,

        get filteredRows() {
            if (!this.searchQuery) return this.tableRows;
            const q = this.searchQuery.toLowerCase();
            return this.tableRows.filter(r => r.date.toLowerCase().includes(q));
        },

        get totalPages() {
            return Math.ceil(this.filteredRows.length / this.pageSize) || 1;
        },

        get paginatedRows() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredRows.slice(start, start + this.pageSize);
        },

        setFilter(newFilter) {
            this.filter = newFilter;
            this.currentPage = 1;
            if (newFilter === 'custom') {
                this.showCustomPicker = true;
                if (this.customStart && this.customEnd) {
                    this.fetchData({
                        filter: 'custom',
                        start_date: this.customStart,
                        end_date: this.customEnd
                    });
                }
                return;
            }
            this.showCustomPicker = false;
            this.fetchData({ filter: newFilter });
        },

        applyCustomDate() {
            if (!this.customStart || !this.customEnd) {
                alert('Please select both start and end dates.');
                return;
            }
            this.filter = 'custom';
            this.currentPage = 1;
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
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                return res.json();
            })
            .then(data => {
                this.totals = data.totals || { registrations: 0, posts: 0, active_users: 0 };
                this.startDate = data.start_date || '';
                this.endDate = data.end_date || '';
                this.tableRows = data.table_rows || [];
                if (this.filter !== 'custom') {
                    this.customStart = data.start_date || '';
                    this.customEnd = data.end_date || '';
                }
                this.currentPage = 1;
            })
            .catch(err => {
                console.error('Failed to load table analytics', err);
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        exportCsv() {
            if (!this.tableRows || !this.tableRows.length) {
                alert('No data to export.');
                return;
            }
            const headers = ['Date / Interval', 'Registrations', 'Posts Created', 'Active Users', 'Combined Total'];
            const rows = this.tableRows.map(r => [
                `"${r.date}"`,
                r.registrations,
                r.posts,
                r.active_users,
                r.registrations + r.posts + r.active_users
            ]);
            rows.push([
                '"TOTAL"',
                this.totals.registrations,
                this.totals.posts,
                this.totals.active_users,
                this.totals.registrations + this.totals.posts + this.totals.active_users
            ]);
            const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', `analytics_${this.filter}_${this.startDate}_${this.endDate}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }));
});
</script>
@endpush
