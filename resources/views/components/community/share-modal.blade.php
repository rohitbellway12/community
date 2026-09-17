<div x-data="communityShareModal()"
    @open-share-modal.window="openModal($event.detail)"
    @keydown.escape.window="closeModal()"
    x-cloak>

    {{-- Modal Backdrop --}}
    <div x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
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
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            @click.stop
            class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">

            {{-- Modal Header --}}
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-none">Share Post</h3>
                        <p class="text-xs text-slate-400 mt-1">Share this post with your device apps & link</p>
                    </div>
                </div>

                <button type="button" @click="closeModal()"
                    class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto space-y-5">

                {{-- Post Preview Card --}}
                <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 border border-slate-200/80 shadow-xs space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <img :src="authorAvatar || 'https://ui-avatars.com/api/?name=User&background=0c1b33&color=fff'"
                                :alt="authorName"
                                class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200 shrink-0">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate" x-text="authorName || 'Community Member'"></p>
                                <p class="text-[11px] text-slate-400 truncate" x-text="authorUsername || '@community'"></p>
                            </div>
                        </div>

                        <span x-show="categoryName"
                            x-text="categoryName"
                            class="shrink-0 text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600">
                        </span>
                    </div>

                    {{-- Title & Excerpt --}}
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 leading-snug line-clamp-2" x-text="postTitle"></h4>
                        <p x-show="postSnippet" class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed" x-text="postSnippet"></p>
                    </div>

                    {{-- Media Preview (Video or Image) --}}
                    <template x-if="mediaUrl">
                        <div class="rounded-xl overflow-hidden border border-slate-200/80 bg-slate-950 flex items-center justify-center">
                            {{-- Video player if media is video --}}
                            <template x-if="isVideo()">
                                <div class="w-full relative bg-black flex items-center justify-center">
                                    <video :src="mediaUrl"
                                        class="w-full max-h-56 object-contain rounded-xl"
                                        controls
                                        playsinline
                                        preload="metadata">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </template>

                            {{-- Image if media is image --}}
                            <template x-if="!isVideo()">
                                <img :src="mediaUrl"
                                    alt="Post Preview"
                                    class="w-full max-h-52 object-cover rounded-xl">
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Device Native Share (Opens device apps: WhatsApp, Instagram, Telegram, Messages, etc.) --}}
                <div>
                    <button type="button" @click="shareToDevice()"
                        style="background: linear-gradient(135deg, #0b1329 0%, #1e293b 100%) !important; color: #ffffff !important;"
                        class="w-full flex items-center justify-between p-4 rounded-2xl border border-slate-800 shadow-lg hover:opacity-95 transition group cursor-pointer">
                        <div class="flex items-center gap-3.5">
                            <div style="background-color: #f59e0b !important; color: #0b1329 !important;"
                                class="w-11 h-11 rounded-xl flex items-center justify-center font-bold shadow-md shrink-0 group-hover:scale-105 transition transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-bold flex items-center gap-2" style="color: #ffffff !important;">
                                    <span>Share via Device Apps</span>
                                    <span style="background-color: rgba(245, 158, 11, 0.25) !important; color: #fbbf24 !important; border: 1px solid rgba(245, 158, 11, 0.4) !important;"
                                        class="text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        Installed Apps
                                    </span>
                                </div>
                                <div class="text-xs mt-0.5" style="color: #cbd5e1 !important;">
                                    Open your phone or system apps to share
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center pl-2" style="color: #94a3b8 !important;">
                            <svg class="w-5 h-5 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>
                </div>

                {{-- Copy Link Section --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Share Post URL
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
                                class="w-full pl-9 pr-3 py-2.5 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl text-slate-700 select-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        </div>

                        <button type="button" @click="copyLink()"
                            class="px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shrink-0 shadow-xs cursor-pointer"
                            :class="copiedLink ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-slate-900 text-white hover:bg-slate-800'">
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

            {{-- Floating Toast Notification inside modal --}}
            <div x-show="toastMessage"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 rounded-full bg-slate-900 text-white text-xs font-bold shadow-xl border border-slate-700 flex items-center gap-2 pointer-events-none z-20">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="toastMessage"></span>
            </div>

        </div>
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

        openModal(data) {
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
            this.toastMessage = '';
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
            }, 2500);
        },

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

        async shareToDevice() {
            this.trackShare();
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: this.postTitle,
                        text: this.postSnippet ? (this.postTitle + ' — ' + this.postSnippet) : this.postTitle,
                        url: this.postUrl
                    });
                    this.showToast('Shared successfully! ✨');
                } catch (err) {
                    if (err?.name !== 'AbortError') {
                        this.copyLink();
                    }
                }
            } else {
                this.copyLink();
                this.showToast('Link copied! Paste into any device app to share.');
            }
        },

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
                this.showToast('Link copied to clipboard! ✨');
                setTimeout(() => {
                    this.copiedLink = false;
                }, 2500);
            } catch (err) {
                window.prompt('Copy this link:', this.postUrl);
            }
        }
    };
}

// Register in Alpine if already loaded, or on alpine:init
if (window.Alpine) {
    window.Alpine.data('communityShareModal', communityShareModal);
}
document.addEventListener('alpine:init', () => {
    Alpine.data('communityShareModal', communityShareModal);
});

// Global helper for convenient triggering
window.openCommunityShareModal = function(postData) {
    window.dispatchEvent(new CustomEvent('open-share-modal', { detail: postData }));
};
</script>
