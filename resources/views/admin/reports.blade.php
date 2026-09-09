@extends('layouts.admin')

@section('title', 'Moderation Reports')

@section('content')
<div class="space-y-6" x-data="{
    reports: [
        { id: 'REP-9012', reporter: 'Vikram Malhotra', contentSnippet: 'Can someone explain the F-2-7 visa requirements?', reason: 'Misleading Information', reportedUser: 'Arjun Patel', status: 'Pending', date: '2026-09-02 08:30' },
        { id: 'REP-9013', reporter: 'Ananya Roy', contentSnippet: 'This comment is completely spreading false information!', reason: 'Harassment', reportedUser: 'Suresh Raina', status: 'Under Review', date: '2026-09-01 14:15' },
        { id: 'REP-9014', reporter: 'Rohan Gupta', contentSnippet: 'Click here to buy guaranteed visa permits online', reason: 'Spam', reportedUser: 'BotAccount99', status: 'Pending', date: '2026-09-02 10:00' }
    ]
}">
    
    <div>
        <h2 class="text-2xl font-bold text-reiac-navy">Moderation Reports</h2>
        <p class="text-xs text-slate-500">Review user complaints, reported discussions, and handle policy violations.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Total Reports</span>
            <p class="text-2xl font-black text-slate-800 mt-1">142</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold text-amber-500 uppercase">Pending Review</span>
            <p class="text-2xl font-black text-amber-600 mt-1">37</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold text-emerald-500 uppercase">Resolved Today</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">89</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold text-red-500 uppercase">Banned Accounts</span>
            <p class="text-2xl font-black text-red-600 mt-1">16</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase">
                        <th class="py-3 px-4">Report ID</th>
                        <th class="py-3 px-4">Flagged Content</th>
                        <th class="py-3 px-4">Reason</th>
                        <th class="py-3 px-4">Target User</th>
                        <th class="py-3 px-4">Reporter</th>
                        <th class="py-3 px-4 text-right">Moderation Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <template x-for="rep in reports" :key="rep.id">
                        <tr>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500" x-text="rep.id"></td>
                            <td class="py-3 px-4 font-medium text-slate-800 max-w-xs truncate" x-text="rep.contentSnippet"></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700" x-text="rep.reason"></span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-700" x-text="rep.reportedUser"></td>
                            <td class="py-3 px-4 text-slate-500" x-text="rep.reporter"></td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold shadow-sm">Dismiss</button>
                                <button class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-[11px] font-bold shadow-sm">Delete Content</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection