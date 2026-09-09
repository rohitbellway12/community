@extends('layouts.admin') 

@section('title', 'Country Management')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ openAddModal: false, editModal: false, currentCountry: {} }">
    
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Country Management</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage global regions, ISO codes, and active statuses.</p>
        </div>
        <button @click="openAddModal = true" class="px-4 py-2 bg-reiac-navy text-reiac-gold text-xs font-bold rounded-lg shadow hover:bg-slate-800 transition-colors">
            + Add New Country
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Countries Table -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Country Name</th>
                    <th class="py-3 px-4">Code</th>
                    <th class="py-3 px-4">ISO Code</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                @forelse($countries as $country)
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3 px-4 font-mono text-slate-400">{{ $country->id }}</td>
                        <td class="py-3 px-4 font-semibold text-slate-900">{{ $country->name }}</td>
                        <td class="py-3 px-4 font-mono">{{ $country->code }}</td>
                        <td class="py-3 px-4 font-mono">{{ $country->iso_code }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $country->status ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $country->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button @click="editModal = true; currentCountry = {{ json_encode($country) }}" class="text-indigo-600 hover:underline font-semibold">Edit</button>
                            <form action="{{ route('admin.countries.destroy', $country->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">No countries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $countries->links() }}
        </div>
    </div>

    <!-- Add Country Modal -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6" @click.outside="openAddModal = false">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Add Country</h3>
            <form action="{{ route('admin.countries.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Country Name</label>
                    <input type="text" name="name" required class="w-full text-xs p-2.5 border rounded-lg focus:outline-none focus:border-reiac-gold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Code (e.g. IND)</label>
                    <input type="text" name="code" required class="w-full text-xs p-2.5 border rounded-lg focus:outline-none focus:border-reiac-gold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ISO Code (e.g. IN)</label>
                    <input type="text" name="iso_code" required class="w-full text-xs p-2.5 border rounded-lg focus:outline-none focus:border-reiac-gold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full text-xs p-2.5 border rounded-lg focus:outline-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="openAddModal = false" class="px-3 py-1.5 text-xs text-slate-600 bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 text-xs font-bold text-reiac-navy bg-reiac-gold rounded-lg">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Country Modal -->
    <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6" @click.outside="editModal = false">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Edit Country</h3>
            <form :action="'/admin/countries/' + currentCountry.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Country Name</label>
                    <input type="text" name="name" x-model="currentCountry.name" required class="w-full text-xs p-2.5 border rounded-lg focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Code</label>
                    <input type="text" name="code" x-model="currentCountry.code" required class="w-full text-xs p-2.5 border rounded-lg focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ISO Code</label>
                    <input type="text" name="iso_code" x-model="currentCountry.iso_code" required class="w-full text-xs p-2.5 border rounded-lg focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" x-model="currentCountry.status" class="w-full text-xs p-2.5 border rounded-lg focus:outline-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-3 py-1.5 text-xs text-slate-600 bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 text-xs font-bold text-reiac-navy bg-reiac-gold rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection