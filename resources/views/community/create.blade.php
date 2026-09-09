<x-community.shell title="Create a post | REIAC" active="home" :user="auth()->user()" :rightbar="false">
    <div x-data="{ openModal: false }" class="max-w-2xl mx-auto">
        
        {{-- Trigger / Page Header Button to Open Modal --}}
        <div class="mb-4">
            <a href="{{ route('community.index') }}" class="text-sm font-semibold text-[#0d3c81]">← Back to Community</a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex items-center gap-3">
            @if(auth()->user() && auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" class="w-11 h-11 rounded-full object-cover">
            @else
                <div class="w-11 h-11 rounded-full bg-[#0d3c81] text-white flex items-center justify-center font-bold text-sm">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
            @endif
            <button @click="openModal = true" type="button" class="flex-1 text-left rounded-full border border-slate-300 bg-slate-50 px-5 py-3 text-sm text-slate-500 hover:bg-slate-100 font-medium transition">
                Start a post, share ideas or questions...
            </button>
        </div>

        {{-- LinkedIn Style Professional Modal --}}
        <div x-show="openModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
             x-transition.opacity>
            
            <div @click.away="openModal = false" class="relative w-full max-w-xl rounded-2xl bg-white shadow-2xl border border-slate-100 overflow-hidden my-8">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if(auth()->user() && auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" class="w-12 h-12 rounded-full object-cover border border-slate-200">
                        @else
                            <div class="w-12 h-12 rounded-full bg-[#0d3c81] text-white flex items-center justify-center font-bold text-base">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">{{ auth()->user()->name ?? 'User' }}</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md cursor-pointer hover:bg-slate-200">
                                    🌐 Post to Anyone ▾
                                </span>
                            </div>
                        </div>
                    </div>
                    <button @click="openModal = false" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Form Content --}}
                <form class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    
                    {{-- Category Selection Badges --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Select Category</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['General', 'Ask a Solution', 'Jobs Info'] as $index => $category)
                                <label class="cursor-pointer">
                                    <input type="radio" name="category" class="peer hidden" {{ $index === 0 ? 'checked' : '' }}>
                                    <span class="inline-block rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 peer-checked:border-[#0d3c81] peer-checked:bg-[#edf3ff] peer-checked:text-[#0d3c81] transition">
                                        {{ $category }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Title Input --}}
                    <div>
                        <input type="text" placeholder="Title / Subject of your discussion" class="w-full font-bold text-lg text-slate-900 placeholder:text-slate-400 border-0 border-b border-slate-200 pb-2 focus:border-[#0d3c81] focus:ring-0 outline-none">
                    </div>

                    {{-- Textarea Body -----}}
                    <div>
                        <textarea rows="5" class="w-full resize-none text-sm text-slate-800 placeholder:text-slate-400 border-0 focus:ring-0 outline-none" placeholder="What do you want to talk about? Share questions, experiences, or insights..."></textarea>
                    </div>

                    {{-- Image Attachment Box --}}
                    <div class="rounded-xl border-2 border-dashed border-slate-200 p-4 text-center hover:border-[#0d3c81] transition bg-slate-50/50">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-xs font-semibold text-slate-700">Add an image to your post</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG or WEBP up to 5MB</p>
                            <button type="button" class="mt-2 text-xs font-bold text-[#0d3c81] bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm hover:bg-slate-50">Browse files</button>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-1 text-slate-400">
                            <button type="button" class="p-2 rounded-full hover:bg-slate-100 hover:text-slate-600 transition" title="Add Emoji">😊</button>
                            <button type="button" class="p-2 rounded-full hover:bg-slate-100 hover:text-slate-600 transition" title="Attach Media">🖼️</button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openModal = false" type="button" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                            <button type="button" class="rounded-full bg-[#0d3c81] px-5 py-2 text-sm font-bold text-white hover:bg-[#0b316b] shadow-sm transition">Post</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</x-community.shell>