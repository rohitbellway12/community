<div x-data="communityShareModal()"
    @open-share-modal.window="handleShareRequest($event.detail)"
    @keydown.escape.window="closeModal()"
    x-cloak>

    {{-- Modal Backdrop --}}
    <div x-show="isOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeModal()"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 overflow-y-auto">

        {{-- Modal Dialog --}}
        <div x-show="isOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
            @click.stop
            class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">

            {{-- Modal Header --}}
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[#0b1329] leading-tight flex items-center gap-2">
                            Share Post
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200/60">Community</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">Share across social apps, chats & direct links</p>
                    </div>
                </div>

                <button type="button" @click="closeModal()"
                    class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition flex items-center justify-center cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto space-y-5">

                {{-- NATIVE DEVICE SHARE BUTTON (Primary trigger if supported on user's phone or desktop) --}}
                <div x-show="hasNativeShare">
                    <button type="button" @click="triggerNativeShare()"
                        class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-600 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-between group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-xs group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-extrabold leading-tight">Share with Phone / System Apps</div>
                                <div class="text-[11px] text-amber-100 font-medium">WhatsApp, Instagram, AirDrop, Nearby Share & more</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-white/80 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>

                {{-- Real Post Card Preview (Gives genuine "Post Share" feel) --}}
                <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 via-white to-slate-50/80 border border-slate-200/90 shadow-xs space-y-3">
                    {{-- Author Meta --}}
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <img :src="authorAvatar || 'https://ui-avatars.com/api/?name=User&background=0c1b33&color=fff'"
                                :alt="authorName"
                                class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-200/80 shadow-2xs shrink-0">
                            <div class="min-w-0">
                                <p class="text-xs font-extrabold text-slate-900 truncate" x-text="authorName || 'Community Member'"></p>
                                <p class="text-[11px] text-slate-400 font-medium truncate" x-text="authorUsername || '@community'"></p>
                            </div>
                        </div>

                        <span x-show="categoryName"
                            x-text="categoryName"
                            class="shrink-0 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200/80 text-slate-700">
                        </span>
                    </div>

                    {{-- Post Content & Excerpt --}}
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 leading-snug line-clamp-2" x-text="postTitle"></h4>
                        <p x-show="postSnippet" class="text-xs text-slate-600 mt-1.5 line-clamp-2 leading-relaxed" x-text="postSnippet"></p>
                    </div>

                    {{-- Media Preview (Video or Image) --}}
                    <template x-if="mediaUrl">
                        <div class="rounded-xl overflow-hidden border border-slate-200/80 bg-slate-950 flex items-center justify-center max-h-52">
                            <template x-if="isVideo()">
                                <div class="w-full relative bg-black flex items-center justify-center">
                                    <video :src="mediaUrl"
                                        class="w-full max-h-52 object-contain rounded-xl"
                                        controls
                                        playsinline
                                        preload="metadata">
                                        Your browser does not support video.
                                    </video>
                                </div>
                            </template>

                            <template x-if="!isVideo()">
                                <img :src="mediaUrl"
                                    alt="Post Preview"
                                    class="w-full max-h-52 object-cover rounded-xl">
                            </template>
                        </div>
                    </template>

                    {{-- Card Watermark / Brand Badge --}}
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                        <span class="flex items-center gap-1.5 text-slate-500 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                            REIAC Community Post
                        </span>
                        <span class="truncate max-w-[200px]" x-text="postDomain"></span>
                    </div>
                </div>

                {{-- DIRECT 1-CLICK SOCIAL PLATFORMS GRID --}}
                <div class="space-y-2.5">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                        Share Directly To Apps:
                    </label>

                    <div class="grid grid-cols-4 gap-2.5 sm:gap-3">
                        {{-- WhatsApp --}}
                        <button type="button" @click="shareToWhatsApp()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-emerald-300 bg-white hover:bg-emerald-50/50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-[#25D366] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.761.884 2.791.884 3.179 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.768-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.276.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.716.937 3.684 1.471 5.779 1.471 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12zm0 21.633c-1.875 0-3.628-.51-5.138-1.399l-.368-.218-3.481.913.929-3.393-.238-.38c-.975-1.554-1.49-3.36-1.49-5.223 0-5.314 4.324-9.638 9.638-9.638 5.313 0 9.638 4.324 9.638 9.638 0 5.314-4.325 9.638-9.638 9.638z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">WhatsApp</span>
                        </button>

                        {{-- X (Twitter) --}}
                        <button type="button" @click="shareToTwitter()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-slate-400 bg-white hover:bg-slate-50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-black text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">X (Twitter)</span>
                        </button>

                        {{-- LinkedIn --}}
                        <button type="button" @click="shareToLinkedIn()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-blue-400 bg-white hover:bg-blue-50/50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-[#0A66C2] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.53 1.53 0 1 0 0-3.05 1.53 1.53 0 0 0 0 3.05m1.39 9.74v-8.37H5.07v8.37h2.78z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">LinkedIn</span>
                        </button>

                        {{-- Facebook --}}
                        <button type="button" @click="shareToFacebook()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-blue-300 bg-white hover:bg-blue-50/50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-[#1877F2] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">Facebook</span>
                        </button>

                        {{-- Telegram --}}
                        <button type="button" @click="shareToTelegram()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-sky-300 bg-white hover:bg-sky-50/50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-[#229ED9] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .37z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">Telegram</span>
                        </button>

                        {{-- MS Teams --}}
                        <button type="button" @click="shareToTeams()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-indigo-300 bg-white hover:bg-indigo-50/50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-[#464EB8] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                    <path d="M19.5 7.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5zm-6 2.25a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 1.5h-2.25c-.27 0-.525.045-.765.12A4.47 4.47 0 0 1 18 13.5v4.5h4.5a1.5 1.5 0 0 0 1.5-1.5v-3.75a2.25 2.25 0 0 0-2.25-2.25zm-6.75.75h-4.5A2.25 2.25 0 0 0 4 14.25v4.5A1.5 1.5 0 0 0 5.5 20.25h10.5a1.5 1.5 0 0 0 1.5-1.5v-4.5a2.25 2.25 0 0 0-2.25-2.25z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">MS Teams</span>
                        </button>

                        {{-- Email --}}
                        <button type="button" @click="shareToEmail()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-red-300 bg-white hover:bg-red-50/50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-[#EA4335] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-5 h-5 fill-none stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">Email</span>
                        </button>

                        {{-- Copy Quote & Link (Formatted for Chat Pasting) --}}
                        <button type="button" @click="copyFullShareText()"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-amber-300 bg-white hover:bg-amber-50/50 hover:shadow-md transition-all group cursor-pointer text-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center shadow-xs group-hover:scale-105 transition transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v7.518z" />
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 mt-1.5">Copy Text</span>
                        </button>
                    </div>
                </div>

                {{-- Direct Post Link Copy --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                        Direct Post Link
                    </label>

                    <div class="flex items-center gap-2">
                        <div class="relative flex-1 min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <input type="text" readonly :value="postUrl" @click="$event.target.select()"
                                class="w-full pl-9 pr-3 py-2.5 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl text-slate-700 select-all focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        </div>

                        <button type="button" @click="copyLink()"
                            class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shrink-0 shadow-xs cursor-pointer"
                            :class="copiedLink ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-[#0b1329] text-white hover:bg-slate-800'">
                            <svg x-show="!copiedLink" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                            </svg>
                            <svg x-show="copiedLink" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="copiedLink ? 'Copied!' : 'Copy Link'"></span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Universal Floating Toast Notification (Shows globally across screen for native & modal shares) --}}
    <div x-show="toastMessage"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-6 inset-x-0 mx-auto w-fit max-w-sm px-4 py-2.5 rounded-full bg-slate-900/95 text-white text-xs font-bold shadow-2xl border border-slate-700/80 backdrop-blur-md flex items-center gap-2.5 pointer-events-none z-[99999]">
        <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <span x-text="toastMessage"></span>
    </div>

</div>

<script>
function communityShareModal() {
    return {
        isOpen: false,
        postId: null,
        postTitle: '',
        postUrl: '',
        postSnippet: '',
        mediaUrl: null,
        mediaType: 'image',
        authorName: '',
        authorUsername: '',
        authorAvatar: '',
        categoryName: '',
        shareEndpoint: '',
        copiedLink: false,
        toastMessage: '',
        toastTimer: null,
        hasNativeShare: typeof navigator !== 'undefined' && Boolean(navigator.share),

        get postDomain() {
            try {
                return new URL(this.postUrl).hostname || window.location.hostname;
            } catch (e) {
                return window.location.hostname;
            }
        },

        isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                (typeof navigator !== 'undefined' && navigator.maxTouchPoints > 1 && window.innerWidth <= 768);
        },

        // Handler called when user clicks Share anywhere on the page
        async handleShareRequest(data) {
            this.populateData(data);

            // Directly open native Desktop/Phone share sheet without showing popup!
            if (this.hasNativeShare && !data.forceModal) {
                const nativeTriggered = await this.triggerNativeShare();
                if (nativeTriggered) {
                    return; // Successfully opened native desktop/phone share drawer!
                }
            }

            // Fallback ONLY if browser does not support native sharing
            this.openModalDirect();
        },

        populateData(data) {
            data = data || {};
            this.postId = data.id || null;
            this.postTitle = data.title || 'REIAC Community Post';
            this.postUrl = data.url || window.location.href;
            this.postSnippet = data.text || '';
            this.mediaUrl = data.mediaUrl || data.image || null;
            this.mediaType = data.mediaType || (this.detectIsVideo(this.mediaUrl) ? 'video' : 'image');
            this.authorName = data.authorName || '';
            this.authorUsername = data.authorUsername || '';
            this.authorAvatar = data.authorAvatar || '';
            this.categoryName = data.category || '';
            this.shareEndpoint = data.shareEndpoint || '';
            this.copiedLink = false;
        },

        openModalDirect() {
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        detectIsVideo(url) {
            if (!url) return false;
            return /\.(mp4|mov|avi|webm|mkv|m4v|qt|3gp|ogg|wmv)(\?.*)?$/i.test(url);
        },

        isVideo() {
            if (this.mediaType === 'video') return true;
            return this.detectIsVideo(this.mediaUrl);
        },

        showToast(message) {
            this.toastMessage = message;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => {
                this.toastMessage = '';
            }, 3000);
        },

        // Trigger native share sheet on Phone (Android/iOS) or Desktop (Windows/macOS)
        async triggerNativeShare() {
            if (!navigator.share) return false;

            const cleanExcerpt = this.postSnippet ? this.postSnippet.replace(/https?:\/\/\S+/g, '').trim() : '';
            const shareText = cleanExcerpt ? `${this.postTitle} — "${cleanExcerpt}"` : this.postTitle;

            // Auto-copy to clipboard as backup for desktop apps that don't auto-fill input
            const fullPostText = (this.postTitle ? `*${this.postTitle}*\n\n` : '') +
                (cleanExcerpt ? `"${cleanExcerpt}"\n\n` : '') +
                `👉 ${this.postUrl}`;
            try {
                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(fullPostText);
                }
            } catch (e) {}

            const shareData = {
                title: this.postTitle,
                text: shareText,
                url: this.postUrl
            };

            try {
                // If browser has canShare, verify validity
                if (navigator.canShare && !navigator.canShare(shareData)) {
                    // Try without text if full payload is unsupported
                    delete shareData.text;
                }

                await navigator.share(shareData);
                this.trackShare();
                this.showToast('Post shared successfully! ✨');
                if (this.isOpen) {
                    this.closeModal();
                }
                return true;
            } catch (err) {
                if (err && err.name === 'AbortError') {
                    // User dismissed native share sheet (e.g. tapped close/back)
                    return true;
                }
                console.warn('Native Web Share API error:', err);
                return false;
            }
        },

        // Track share count on backend
        trackShare() {
            if (!this.shareEndpoint) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch(this.shareEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            }).then(res => res.json()).then(data => {
                if (data?.success && this.postId) {
                    window.dispatchEvent(new CustomEvent('post-shared', {
                        detail: {
                            postId: this.postId,
                            sharesCount: data.shares_count
                        }
                    }));
                }
            }).catch(() => {});
        },

        // 1. WhatsApp with Rich Formatted Message (Auto-fills text in chat input)
        shareToWhatsApp() {
            this.trackShare();
            const textParts = [];
            if (this.postTitle) textParts.push('*' + this.postTitle + '*');
            if (this.postSnippet) textParts.push('"' + this.postSnippet + '"');
            textParts.push('👉 Read full post: ' + this.postUrl);
            const fullText = textParts.join('\n\n');

            try {
                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(fullText);
                }
            } catch (e) {}

            const text = encodeURIComponent(fullText);
            window.open('https://api.whatsapp.com/send?text=' + text, '_blank');
            this.showToast('Opening WhatsApp... Message ready! (Ctrl+V also available) 💬');
        },

        // 2. Twitter / X with Hashtag and Post Link
        shareToTwitter() {
            this.trackShare();
            const tweet = encodeURIComponent(this.postTitle || '');
            const url = encodeURIComponent(this.postUrl);
            window.open(`https://twitter.com/intent/tweet?text=${tweet}&url=${url}`, '_blank', 'width=600,height=500');
        },

        // 3. LinkedIn
        shareToLinkedIn() {
            this.trackShare();
            const url = encodeURIComponent(this.postUrl);
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=600');
        },

        // 4. Facebook
        shareToFacebook() {
            this.trackShare();
            const url = encodeURIComponent(this.postUrl);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=500');
        },

        // 5. Telegram
        shareToTelegram() {
            this.trackShare();
            const text = encodeURIComponent(this.postTitle || '');
            const url = encodeURIComponent(this.postUrl);
            window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank');
        },

        // 6. Microsoft Teams (Auto-fills text in Teams dialog & auto-copies for Ctrl+V)
        shareToTeams() {
            this.trackShare();
            const cleanExcerpt = this.postSnippet ? this.postSnippet.replace(/https?:\/\/\S+/g, '').trim() : '';
            const fullPostText = (this.postTitle ? `*${this.postTitle}*\n\n` : '') +
                (cleanExcerpt ? `"${cleanExcerpt}"\n\n` : '') +
                `👉 ${this.postUrl}`;

            // Auto-copy to clipboard for instant Ctrl+V into any chat
            try {
                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(fullPostText);
                }
            } catch (e) {}

            const msgParts = [];
            if (this.postTitle) msgParts.push(this.postTitle);
            if (cleanExcerpt) msgParts.push(`"${cleanExcerpt}"`);
            const msgText = encodeURIComponent(msgParts.join(' — '));
            const href = encodeURIComponent(this.postUrl);

            window.open(`https://teams.microsoft.com/share?href=${href}&msgText=${msgText}`, '_blank', 'width=700,height=600');
            this.showToast('Opening Teams... Post copied! (Press Ctrl+V to paste anytime) 📋');
        },

        // 7. Email
        shareToEmail() {
            this.trackShare();
            const subject = encodeURIComponent(this.postTitle || 'REIAC Community Post');
            const body = encodeURIComponent(
                (this.postSnippet ? this.postSnippet + '\n\n' : '') +
                'Read full discussion on REIAC Community:\n' + this.postUrl
            );
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        },

        // 8. Copy Clean URL
        async copyLink() {
            this.trackShare();
            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(this.postUrl);
                } else {
                    const temp = document.createElement('input');
                    temp.value = this.postUrl;
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    document.body.removeChild(temp);
                }
                this.copiedLink = true;
                this.showToast('Post link copied to clipboard! 📋');
                setTimeout(() => {
                    this.copiedLink = false;
                }, 2500);
            } catch (err) {
                window.prompt('Copy post link:', this.postUrl);
            }
        },

        // 9. Copy Formatted Post (Title + Quote + Link) for pasting in chats
        async copyFullShareText() {
            this.trackShare();
            const fullText = (this.postTitle ? `*${this.postTitle}*\n\n` : '') +
                (this.postSnippet ? `"${this.postSnippet}"\n\n` : '') +
                `👉 ${this.postUrl}`;

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(fullText);
                } else {
                    const temp = document.createElement('textarea');
                    temp.value = fullText;
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    document.body.removeChild(temp);
                }
                this.showToast('Post quote & link copied! Ready to paste. 💬');
            } catch (err) {
                this.copyLink();
            }
        }
    };
}

// Register with Alpine safely
if (window.Alpine) {
    window.Alpine.data('communityShareModal', communityShareModal);
}
document.addEventListener('alpine:init', () => {
    Alpine.data('communityShareModal', communityShareModal);
});

// Global convenience helper
window.openCommunityShareModal = function(postData) {
    window.dispatchEvent(new CustomEvent('open-share-modal', { detail: postData }));
};
</script>
