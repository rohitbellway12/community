@props([
    'notificationsCount' => 0,
])

@php
    $authUser = auth()->user();

    $topbarProfile = $authUser?->profile;

    $topbarAvatar = $topbarProfile?->avatar
        ? asset('storage/' . $topbarProfile->avatar)
        : 'https://ui-avatars.com/api/?name=' .
            urlencode($authUser?->name ?? 'User') .
            '&background=0c1b33&color=fff&size=100';
@endphp

<header
    class="bg-[#0b1329] text-white sticky top-0 z-50"
    x-data="{ mobileMenuOpen: false }"
>
    <div class="max-w-[1520px] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="h-16 flex items-center justify-between gap-4">

            {{-- LEFT SIDE --}}
            <div class="flex items-center gap-3 sm:gap-4 lg:gap-10 min-w-0">

                {{-- Mobile Menu Button --}}
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    type="button"
                    class="lg:hidden shrink-0 w-9 h-9 rounded-lg flex items-center justify-center text-slate-300 hover:text-white hover:bg-white/10 transition"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Toggle navigation"
                >
                    <svg
                        x-show="!mobileMenuOpen"
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>

                    <svg
                        x-show="mobileMenuOpen"
                        x-cloak
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 6l12 12M18 6L6 18"
                        />
                    </svg>
                </button>

                {{-- Logo --}}
                <a
                    href="{{ route('community.index') }}"
                    class="text-base sm:text-xl lg:text-2xl font-black tracking-tight lg:tracking-wider text-white whitespace-nowrap truncate"
                >
                    REIAC Community
                </a>

                {{-- DESKTOP NAVIGATION --}}
                <nav class="hidden lg:flex items-center gap-5 xl:gap-7 text-[13px] font-medium text-slate-300">

                    {{-- Home --}}
                    <a
                        href="https://www.reiaconsultancy.com/"
                        class="hover:text-white transition whitespace-nowrap"
                    >
                        Home
                    </a>

                    {{-- About Us --}}
                    <a
                        href="https://www.reiaconsultancy.com/about-us"
                        class="hover:text-white transition whitespace-nowrap"
                    >
                        About Us
                    </a>

                    {{-- Services --}}
                    <div
                        x-data="{ servicesOpen: false }"
                        class="relative"
                    >
                        <button
                            type="button"
                            @click="servicesOpen = !servicesOpen"
                            @click.outside="servicesOpen = false"
                            class="flex items-center gap-1 text-[13px] font-medium text-slate-300 hover:text-white transition"
                            :class="{ 'text-white': servicesOpen }"
                        >
                            <span>Services</span>

                            <svg
                                class="w-3 h-3 transition-transform duration-200"
                                :class="{ 'rotate-180': servicesOpen }"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 9l6 6 6-6"
                                />
                            </svg>
                        </button>

                        <div
                            x-show="servicesOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                            class="absolute left-1/2 -translate-x-1/2 top-full mt-6 w-[230px] bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50"
                            style="display: none;"
                        >
                            <a
                                href="https://www.reiaconsultancy.com/services/university-admissions"
                                class="block px-5 py-4 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-amber-500 transition"
                            >
                                University Admissions
                            </a>

                            <a
                                href="https://www.reiaconsultancy.com/services/visa-documentation"
                                class="block px-5 py-4 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-amber-500 transition"
                            >
                                Visa & Documentation
                            </a>

                            <a
                                href="https://www.reiaconsultancy.com/services/institutional-partnerships"
                                class="block px-5 py-4 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-amber-500 transition"
                            >
                                Institutional Partnerships
                            </a>

                            <a
                                href="https://www.reiaconsultancy.com/services/career-counseling"
                                class="block px-5 py-4 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-amber-500 transition"
                            >
                                Career Counseling
                            </a>
                        </div>
                    </div>

                    {{-- For Students --}}
                    <a
                        href="https://www.reiaconsultancy.com/apply"
                        class="hover:text-white transition whitespace-nowrap"
                    >
                        For Students
                    </a>

                    {{-- Community --}}
                    <a
                        href="{{ route('community.index') }}"
                        class="text-white font-semibold relative whitespace-nowrap py-1"
                    >
                        Community

                        <span class="absolute left-0 right-0 -bottom-1 h-0.5 bg-amber-400 rounded-full"></span>
                    </a>

                    {{-- Notices --}}
                    <a
                        href="https://www.reiaconsultancy.com/notices"
                        class="hover:text-white transition whitespace-nowrap"
                    >
                        Notices
                    </a>

                    {{-- F-2-7 Calculator --}}
                    <a
                        href="https://www.reiaconsultancy.com/visa-calculator"
                        class="hover:text-white transition whitespace-nowrap"
                    >
                        F-2-7 Points Calculator
                    </a>

                    {{-- Contact --}}
                    <a
                        href="https://www.reiaconsultancy.com/contact-us"
                        class="hover:text-white transition whitespace-nowrap"
                    >
                        Contact Us
                    </a>
                </nav>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">

                @auth

                    {{-- DESKTOP NOTIFICATIONS --}}
                    <div
                        class="relative hidden sm:block"
                        x-data="{
                            notificationOpen: false,
                            notificationLoading: false,
                            notifications: [],
                            unreadCount: {{ (int) $notificationsCount }},
                            hoverTimer: null,

                            openNotifications() {
                                clearTimeout(this.hoverTimer);
                                this.notificationOpen = true;
                                this.loadNotifications();
                            },

                            closeNotifications() {
                                clearTimeout(this.hoverTimer);

                                this.hoverTimer = setTimeout(() => {
                                    this.notificationOpen = false;
                                }, 180);
                            },

                            toggleNotifications() {
                                clearTimeout(this.hoverTimer);
                                this.notificationOpen = !this.notificationOpen;

                                if (this.notificationOpen) {
                                    this.loadNotifications();
                                }
                            },

                            async loadNotifications() {
                                this.notificationLoading = true;

                                try {
                                    const response = await fetch(
                                        '{{ route('community.notifications') }}',
                                        {
                                            method: 'GET',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        }
                                    );

                                    if (!response.ok) {
                                        throw new Error('Unable to load notifications.');
                                    }

                                    const result = await response.json();

                                    if (result.success) {
                                        this.notifications =
                                            (result.data.notifications || []).map(n => ({
                                                ...n,
                                                actionStatus: null
                                            }));

                                        this.unreadCount =
                                            result.data.unread_count || 0;
                                    }
                                } catch (error) {
                                    console.error('Notification error:', error);
                                } finally {
                                    this.notificationLoading = false;
                                }
                            },

                            async markAsRead(notification) {
                                if (notification.read) {
                                    return;
                                }

                                try {
                                    const response = await fetch(
                                        '{{ url('/community/notifications') }}/' +
                                        notification.id +
                                        '/read',
                                        {
                                            method: 'PATCH',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN':
                                                    document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        }
                                    );

                                    if (!response.ok) {
                                        return;
                                    }

                                    notification.read = true;

                                    if (this.unreadCount > 0) {
                                        this.unreadCount--;
                                    }
                                } catch (error) {
                                    console.error('Read notification error:', error);
                                }
                            },

                            async openNotification(notification) {
                                await this.markAsRead(notification);

                                const data = notification.data || {};

                                this.notificationOpen = false;

                                if (data.url) {
                                    window.location.href = data.url;
                                    return;
                                }

                                if (data.post_id) {
                                    window.location.href =
                                        '{{ url('/community/posts') }}/' +
                                        data.post_id;
                                }
                            },

                            async markAllAsRead() {
                                try {
                                    const response = await fetch(
                                        '{{ route('community.notifications.read-all') }}',
                                        {
                                            method: 'PATCH',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN':
                                                    document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        }
                                    );

                                    if (!response.ok) {
                                        throw new Error('Unable to mark notifications as read.');
                                    }

                                    const result = await response.json();

                                    if (result.success) {
                                        this.notifications =
                                            this.notifications.map(notification => ({
                                                ...notification,
                                                read: true
                                            }));

                                        this.unreadCount = 0;
                                    }
                                } catch (error) {
                                    console.error('Mark all notification error:', error);
                                }
                            },

                            async handleGroupRequest(notification, action) {
                                const data = notification.data || {};

                                if (!data.group_slug || !data.user_id) {
                                    return;
                                }

                                const endpoint =
                                    `/community/groups/${data.group_slug}/requests/${data.user_id}/${action}`;

                                try {
                                    const response = await fetch(endpoint, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN':
                                                document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });

                                    if (!response.ok) {
                                        alert('Something went wrong.');
                                        return;
                                    }

                                    notification.actionStatus =
                                        action === 'accept' ? 'accepted' : 'rejected';

                                    await fetch(
                                        `{{ url('/community/notifications') }}/${notification.id}`,
                                        {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN':
                                                    document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        }
                                    );

                                    setTimeout(() => {
                                        this.notifications =
                                            this.notifications.filter(
                                                n => n.id !== notification.id
                                            );

                                        if (!notification.read && this.unreadCount > 0) {
                                            this.unreadCount--;
                                        }
                                    }, 1000);

                                } catch (error) {
                                    console.error('Group request error:', error);
                                    alert('Something went wrong.');
                                }
                            }
                        }"
                        @click.outside="notificationOpen = false"
                        @mouseenter="openNotifications()"
                        @mouseleave="closeNotifications()"
                    >
                        {{-- Bell --}}
                        <button
                            type="button"
                            @click.prevent.stop="toggleNotifications()"
                            class="relative w-9 h-9 rounded-full bg-[#1e293b] flex items-center justify-center text-slate-300 hover:text-white hover:bg-[#263449] transition focus:outline-none"
                            aria-label="Notifications"
                            :aria-expanded="notificationOpen"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.5"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"
                                />
                            </svg>

                            <span
                                x-show="unreadCount > 0"
                                x-transition
                                x-text="unreadCount > 99 ? '99+' : unreadCount"
                                class="absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1 bg-amber-400 text-slate-950 font-bold text-[8px] rounded-full flex items-center justify-center border-2 border-[#0b1329]"
                            ></span>
                        </button>

                        {{-- Notification Dropdown --}}
                        <div
                            x-show="notificationOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                            @mouseenter="clearTimeout(hoverTimer)"
                            @mouseleave="closeNotifications()"
                            class="absolute right-0 top-11 w-[380px] max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-[9999]"
                            style="display: none;"
                        >
                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">
                                        Notifications
                                    </h3>

                                    <p
                                        class="text-[11px] text-slate-400 mt-1"
                                        x-text="
                                            unreadCount > 0
                                                ? unreadCount + ' unread notification' + (unreadCount === 1 ? '' : 's')
                                                : 'You are all caught up'
                                        "
                                    ></p>
                                </div>

                                <button
                                    type="button"
                                    x-show="unreadCount > 0"
                                    @click.prevent.stop="markAllAsRead()"
                                    class="text-[11px] font-semibold text-amber-500 hover:text-amber-600 transition"
                                >
                                    Mark all as read
                                </button>
                            </div>

                            <div x-show="notificationLoading" class="px-5 py-10 text-center">
                                <svg class="w-5 h-5 animate-spin mx-auto text-amber-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z" />
                                </svg>

                                <p class="text-xs text-slate-400 mt-2">
                                    Loading notifications...
                                </p>
                            </div>

                            <div x-show="!notificationLoading" class="max-h-[420px] overflow-y-auto">

                                <template x-for="notification in notifications" :key="notification.id">
                                    <div
                                        class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-slate-50 transition"
                                        :class="notification.read ? 'bg-white' : 'bg-amber-50/70'"
                                    >
                                        <div
                                            class="flex items-start gap-3 cursor-pointer"
                                            @click="openNotification(notification)"
                                        >
                                            <div
                                                class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center"
                                                :class="notification.read ? 'bg-slate-100 text-slate-400' : 'bg-amber-100 text-amber-600'"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"
                                                    />
                                                </svg>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-slate-700 leading-snug"
                                                    :class="notification.read ? 'font-medium' : 'font-semibold'"
                                                    x-text="notification.message || notification.data?.message"
                                                ></p>

                                                <p
                                                    class="text-[10px] text-slate-400 mt-1"
                                                    x-text="notification.created_at"
                                                ></p>
                                            </div>

                                            <span
                                                x-show="!notification.read"
                                                class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0"
                                            ></span>
                                        </div>

                                        {{-- Group Join Request Actions --}}
                                        <template x-if="notification.data && notification.data.type === 'group_join_requested'">
                                            <div class="mt-2 ml-12">

                                                <template x-if="notification.data.status === 'active'">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-semibold rounded">
                                                        Added
                                                    </span>
                                                </template>

                                                <template x-if="notification.data.status !== 'active'">
                                                    <div>
                                                        <div
                                                            class="flex items-center gap-2"
                                                            x-show="!notification.actionStatus"
                                                        >
                                                            <button
                                                                type="button"
                                                                @click.prevent.stop="handleGroupRequest(notification, 'accept')"
                                                                class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold rounded-lg transition shadow-sm"
                                                            >
                                                                Accept
                                                            </button>

                                                            <button
                                                                type="button"
                                                                @click.prevent.stop="handleGroupRequest(notification, 'reject')"
                                                                class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-semibold rounded-lg transition shadow-sm"
                                                            >
                                                                Reject
                                                            </button>
                                                        </div>

                                                        <div
                                                            x-show="notification.actionStatus"
                                                            x-cloak
                                                            class="mt-1"
                                                        >
                                                            <template x-if="notification.actionStatus === 'accepted'">
                                                                <span class="inline-flex items-center text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[10px] font-semibold">
                                                                    Accepted
                                                                </span>
                                                            </template>

                                                            <template x-if="notification.actionStatus === 'rejected'">
                                                                <span class="inline-flex items-center text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 text-[10px] font-semibold">
                                                                    Rejected
                                                                </span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <div
                                    x-show="!notificationLoading && notifications.length === 0"
                                    class="px-5 py-10 text-center"
                                >
                                    <div class="w-11 h-11 mx-auto rounded-full bg-slate-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"
                                            />
                                        </svg>
                                    </div>

                                    <p class="text-xs font-semibold text-slate-500 mt-3">
                                        You're all caught up
                                    </p>

                                    <p class="text-[10px] text-slate-400 mt-1">
                                        New activity will appear here.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PROFILE DROPDOWN --}}
                    <div
                        class="relative hidden sm:block"
                        x-data="{ profileDropdownOpen: false }"
                    >
                        <button
                            @click="profileDropdownOpen = !profileDropdownOpen"
                            @click.outside="profileDropdownOpen = false"
                            type="button"
                            class="relative flex items-center pl-1 focus:outline-none"
                            aria-label="My profile"
                        >
                            <img
                                src="{{ $topbarAvatar }}"
                                alt="{{ $authUser?->name ?? 'User' }}"
                                class="w-9 h-9 rounded-full object-cover ring-2 ring-amber-400 hover:ring-white transition"
                            >

                            <span
                                class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#0b1329]"
                            ></span>
                        </button>

                        <div
                            x-show="profileDropdownOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 text-slate-700"
                            style="display: none;"
                        >
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-900 truncate">
                                    {{ $authUser?->name ?? 'User' }}
                                </p>

                                <p class="text-[11px] text-slate-400 truncate">
                                    {{ $topbarProfile?->username ? '@' . $topbarProfile->username : 'no-handle' }}
                                </p>
                            </div>

                            @if ($authUser?->role instanceof \App\Enums\UserRole)
                                @if ($authUser->role->value === 'admin')
                                    <a
                                        href="{{ url('/admin/dashboard') }}"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                                    >
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-18v6h8V3h-8z" />
                                        </svg>
                                        <span>Admin Dashboard</span>
                                    </a>
                                @endif
                            @endif

                            <a
                                href="{{ $topbarProfile?->username ? url('/community/profile/' . $topbarProfile->username) : '#' }}"
                                class="flex items-center px-4 py-2.5 text-xs font-semibold hover:bg-slate-50 transition text-slate-700"
                            >
                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                View Profile
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="w-full flex items-center px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left"
                                >
                                    <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>

                @else

                    {{-- GUEST LOGIN --}}
                    <a
                        href="{{ route('login') }}"
                        class="hidden sm:flex items-center justify-center px-4 h-9 rounded-lg bg-amber-400 text-slate-950 text-xs font-bold hover:bg-amber-500 transition"
                    >
                        Community Login
                    </a>

                @endauth

            </div>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-t border-white/10 bg-[#0b1329]"
        style="display: none;"
    >
        <nav class="max-w-[1520px] mx-auto px-4 sm:px-6 py-4 space-y-1">

            {{-- Home --}}
            <a
                href="https://www.reiaconsultancy.com/"
                @click="mobileMenuOpen = false"
                class="flex items-center px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition"
            >
                Home
            </a>

            {{-- About Us --}}
            <a
                href="https://www.reiaconsultancy.com/about-us"
                @click="mobileMenuOpen = false"
                class="flex items-center px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition"
            >
                About Us
            </a>

            {{-- Services --}}
            <div x-data="{ mobileServicesOpen: false }">

                <button
                    type="button"
                    @click="mobileServicesOpen = !mobileServicesOpen"
                    class="w-full flex items-center justify-between px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition"
                >
                    <span>Services</span>

                    <svg
                        class="w-4 h-4 transition-transform duration-200"
                        :class="{ 'rotate-180': mobileServicesOpen }"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>

                <div
                    x-show="mobileServicesOpen"
                    x-cloak
                    x-transition
                    class="ml-3 mt-1 pl-3 border-l border-white/10 space-y-1"
                >
                    <a
                        href="https://www.reiaconsultancy.com/services/university-admissions"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2.5 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition"
                    >
                        University Admissions
                    </a>

                    <a
                        href="https://www.reiaconsultancy.com/services/visa-documentation"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2.5 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition"
                    >
                        Visa & Documentation
                    </a>

                    <a
                        href="https://www.reiaconsultancy.com/services/institutional-partnerships"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2.5 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition"
                    >
                        Institutional Partnerships
                    </a>

                    <a
                        href="https://www.reiaconsultancy.com/services/career-counseling"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2.5 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition"
                    >
                        Career Counseling
                    </a>
                </div>
            </div>

            {{-- For Students --}}
            <a
                href="https://www.reiaconsultancy.com/apply"
                @click="mobileMenuOpen = false"
                class="flex items-center px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition"
            >
                For Students
            </a>

            {{-- Community --}}
            <a
                href="{{ route('community.index') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center px-3 py-3 rounded-xl text-sm font-semibold text-white bg-white/10"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-3"></span>
                Community
            </a>

            {{-- Notices --}}
            <a
                href="https://www.reiaconsultancy.com/notices"
                @click="mobileMenuOpen = false"
                class="flex items-center px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition"
            >
                Notices
            </a>

            {{-- F-2-7 Points Calculator --}}
            <a
                href="https://www.reiaconsultancy.com/visa-calculator"
                @click="mobileMenuOpen = false"
                class="flex items-center px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition"
            >
                F-2-7 Points Calculator
            </a>

            {{-- Contact Us --}}
            <a
                href="https://www.reiaconsultancy.com/contact-us"
                @click="mobileMenuOpen = false"
                class="flex items-center px-3 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition"
            >
                Contact Us
            </a>

            {{-- MOBILE AUTH --}}
            @auth

                <div class="pt-3 mt-3 border-t border-white/15 space-y-3">

                    {{-- User --}}
                    <div class="flex items-center gap-3 px-3 py-2">
                        <div class="relative shrink-0">
                            <img
                                src="{{ $topbarAvatar }}"
                                alt="{{ $authUser?->name ?? 'User' }}"
                                class="w-10 h-10 rounded-full object-cover ring-2 ring-amber-400"
                            >

                            <span
                                class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#0b1329]"
                            ></span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white truncate">
                                {{ $authUser?->name ?? 'User' }}
                            </p>

                            @if ($topbarProfile?->username)
                                <p class="text-xs text-slate-400 truncate">
                                    {{ '@' . $topbarProfile->username }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Admin Dashboard --}}
                    @if ($authUser?->role instanceof \App\Enums\UserRole)
                        @if ($authUser->role->value === 'admin')
                            <a
                                href="{{ url('/admin/dashboard') }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-200 hover:bg-white/10 transition"
                            >
                                <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-18v6h8V3h-8z" />
                                </svg>
                                Admin Dashboard
                            </a>
                        @endif
                    @endif

                    {{-- View Profile --}}
                    <a
                        href="{{ $topbarProfile?->username ? url('/community/profile/' . $topbarProfile->username) : '#' }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-200 hover:bg-white/10 transition"
                    >
                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        View Profile
                    </a>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="w-full flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-500/10 transition text-left"
                        >
                            <svg class="w-4 h-4 mr-2.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>

                </div>

            @else

                {{-- Guest Login --}}
                <div class="pt-3 mt-3 border-t border-white/10 px-3">
                    <a
                        href="{{ route('login') }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center justify-center w-full py-2.5 rounded-xl bg-amber-400 text-slate-950 text-xs font-bold hover:bg-amber-500 transition"
                    >
                        Community Login
                    </a>
                </div>

            @endauth

        </nav>
    </div>

    {{-- MOBILE BOTTOM NAVIGATION --}}
    <div
        class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 lg:hidden px-3 pt-2 pb-3 z-40 flex items-center justify-between shadow-[0_-4px_20px_rgba(0,0,0,0.06)]"
        x-data="{
            mobileNotifOpen: false,
            notifLoading: false,
            notifications: [],
            unreadCount: {{ (int) $notificationsCount }},

            toggleMobileNotifs() {
                @auth
                    this.mobileNotifOpen = !this.mobileNotifOpen;

                    if (this.mobileNotifOpen) {
                        this.loadMobileNotifications();
                    }
                @else
                    window.location.href = '{{ route('login') }}';
                @endauth
            },

            async loadMobileNotifications() {
                this.notifLoading = true;

                try {
                    const response = await fetch(
                        '{{ route('community.notifications') }}',
                        {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Failed to load notifications.');
                    }

                    const result = await response.json();

                    if (result.success) {
                        this.notifications =
                            (result.data.notifications || []).map(n => ({
                                ...n,
                                actionStatus: null
                            }));

                        this.unreadCount =
                            result.data.unread_count || 0;
                    }
                } catch (error) {
                    console.error('Mobile notification error:', error);
                } finally {
                    this.notifLoading = false;
                }
            },

            async markAsRead(notification) {
                if (notification.read) {
                    return;
                }

                try {
                    await fetch(
                        '{{ url('/community/notifications') }}/' +
                        notification.id +
                        '/read',
                        {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN':
                                    document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    notification.read = true;

                    if (this.unreadCount > 0) {
                        this.unreadCount--;
                    }
                } catch (error) {
                    console.error('Read notification error:', error);
                }
            },

            async openNotification(notification) {
                await this.markAsRead(notification);

                const data = notification.data || {};

                this.mobileNotifOpen = false;

                if (data.url) {
                    window.location.href = data.url;
                    return;
                }

                if (data.post_id) {
                    window.location.href =
                        '{{ url('/community/posts') }}/' +
                        data.post_id;
                }
            },

            async markAllAsRead() {
                try {
                    const response = await fetch(
                        '{{ route('community.notifications.read-all') }}',
                        {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN':
                                    document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    const result = await response.json();

                    if (result.success) {
                        this.notifications =
                            this.notifications.map(n => ({
                                ...n,
                                read: true
                            }));

                        this.unreadCount = 0;
                    }
                } catch (error) {
                    console.error('Mark all mobile notifications error:', error);
                }
            },

            async handleGroupRequest(notification, action) {
                const data = notification.data || {};

                if (!data.group_slug || !data.user_id) {
                    return;
                }

                const endpoint =
                    `/community/groups/${data.group_slug}/requests/${data.user_id}/${action}`;

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN':
                                document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        alert('Something went wrong.');
                        return;
                    }

                    notification.actionStatus =
                        action === 'accept' ? 'accepted' : 'rejected';

                    await fetch(
                        `{{ url('/community/notifications') }}/${notification.id}`,
                        {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN':
                                    document.querySelector('meta[name=csrf-token]')?.getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    setTimeout(() => {
                        this.notifications =
                            this.notifications.filter(
                                n => n.id !== notification.id
                            );

                        if (!notification.read && this.unreadCount > 0) {
                            this.unreadCount--;
                        }
                    }, 1000);

                } catch (error) {
                    console.error('Mobile group request error:', error);
                    alert('Something went wrong.');
                }
            }
        }"
    >

        {{-- Groups --}}
        <a
            href="{{ route('community.groups.index') }}"
            class="flex flex-1 flex-col items-center justify-center gap-1 transition {{ request()->routeIs('community.groups.*') ? 'text-[#0b1329] font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20H4v-2a4 4 0 014-4h1" />
                <circle cx="9" cy="7" r="4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3.5a4 4 0 010 7" />
            </svg>

            <span class="text-[10px] tracking-tight">
                Groups
            </span>
        </a>

        {{-- Community --}}
        <a
            href="{{ route('community.index') }}"
            class="flex flex-1 flex-col items-center justify-center gap-1 transition {{ request()->routeIs('community.index') ? 'text-[#0b1329] font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
            </svg>

            <span class="text-[10px] tracking-tight">
                Community
            </span>
        </a>

        {{-- Create --}}
        <div class="flex flex-1 flex-col items-center justify-center">
            <button
                type="button"
                @click="$dispatch('{{ auth()->check() ? 'open-post-modal' : 'open-login-modal' }}')"
                class="group relative -top-4 w-12 h-12 bg-[#0b1329] hover:bg-[#162247] rounded-full flex items-center justify-center text-white shadow-xl shadow-slate-900/20 border-4 border-white transition transform active:scale-95"
            >
                <svg
                    class="w-6 h-6 transition-transform group-hover:rotate-90 duration-300"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </button>

            <span class="text-[10px] font-semibold text-slate-700 -mt-2">
                Create
            </span>
        </div>

        {{-- Notifications --}}
        <div class="flex flex-1 flex-col items-center justify-center relative">

            <button
                type="button"
                @click="toggleMobileNotifs()"
                class="flex flex-col items-center justify-center gap-1 text-slate-400 hover:text-slate-600 relative focus:outline-none w-full"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"
                    />
                </svg>

                @auth
                    <span
                        x-show="unreadCount > 0"
                        x-text="unreadCount > 99 ? '99+' : unreadCount"
                        class="absolute top-0 right-3 bg-amber-500 text-slate-950 text-[9px] min-w-[16px] h-4 px-1 rounded-full flex items-center justify-center font-bold border border-white"
                    ></span>
                @endauth

                <span class="text-[10px] font-medium tracking-tight">
                    Alerts
                </span>
            </button>

            {{-- Mobile Notification Popup --}}
            @auth
                <div
                    x-show="mobileNotifOpen"
                    x-cloak
                    @click.outside="mobileNotifOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                    class="absolute bottom-16 right-0 w-[calc(100vw-2rem)] max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-[9999]"
                    style="display: none;"
                >
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">
                                Notifications
                            </h3>

                            <p
                                class="text-[10px] text-slate-400 mt-0.5"
                                x-text="unreadCount > 0 ? unreadCount + ' unread' : 'All caught up'"
                            ></p>
                        </div>

                        <button
                            type="button"
                            x-show="unreadCount > 0"
                            @click="markAllAsRead()"
                            class="text-[10px] font-semibold text-amber-600 hover:underline"
                        >
                            Mark all read
                        </button>
                    </div>

                    <div x-show="notifLoading" class="p-6 text-center">
                        <svg class="w-5 h-5 animate-spin mx-auto text-amber-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z" />
                        </svg>

                        <p class="text-[11px] text-slate-400 mt-2">
                            Loading...
                        </p>
                    </div>

                    <div x-show="!notifLoading" class="max-h-[350px] overflow-y-auto divide-y divide-slate-100">

                        <template x-for="notification in notifications" :key="notification.id">

                            <div
                                class="w-full text-left px-4 py-3 flex flex-col gap-2 transition text-xs"
                                :class="notification.read ? 'bg-white' : 'bg-amber-50/70'"
                            >
                                <div
                                    class="flex items-start gap-2.5 cursor-pointer"
                                    @click="openNotification(notification)"
                                >
                                    <div
                                        class="w-7 h-7 rounded-full shrink-0 flex items-center justify-center mt-0.5"
                                        :class="notification.read ? 'bg-slate-100 text-slate-400' : 'bg-amber-100 text-amber-600'"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"
                                            />
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-slate-700 leading-snug"
                                            :class="notification.read ? 'font-medium' : 'font-semibold'"
                                            x-text="notification.message || notification.data?.message"
                                        ></p>

                                        <p
                                            class="text-[9px] text-slate-400 mt-1"
                                            x-text="notification.created_at"
                                        ></p>
                                    </div>

                                    <span
                                        x-show="!notification.read"
                                        class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0"
                                    ></span>
                                </div>

                                {{-- Mobile Group Request Actions --}}
                                <template x-if="notification.data && notification.data.type === 'group_join_requested'">
                                    <div class="mt-1 ml-9">

                                        <template x-if="notification.data.status === 'active'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-semibold rounded">
                                                Added
                                            </span>
                                        </template>

                                        <template x-if="notification.data.status !== 'active'">
                                            <div>

                                                <div
                                                    class="flex items-center gap-2"
                                                    x-show="!notification.actionStatus"
                                                >
                                                    <button
                                                        type="button"
                                                        @click.prevent.stop="handleGroupRequest(notification, 'accept')"
                                                        class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold rounded-lg transition shadow-sm"
                                                    >
                                                        Accept
                                                    </button>

                                                    <button
                                                        type="button"
                                                        @click.prevent.stop="handleGroupRequest(notification, 'reject')"
                                                        class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-semibold rounded-lg transition shadow-sm"
                                                    >
                                                        Reject
                                                    </button>
                                                </div>

                                                <div
                                                    x-show="notification.actionStatus"
                                                    x-cloak
                                                    class="mt-1 text-[10px] font-semibold"
                                                >
                                                    <template x-if="notification.actionStatus === 'accepted'">
                                                        <span class="inline-flex items-center text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                            Accepted
                                                        </span>
                                                    </template>

                                                    <template x-if="notification.actionStatus === 'rejected'">
                                                        <span class="inline-flex items-center text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                            Rejected
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div
                            x-show="!notifLoading && notifications.length === 0"
                            class="p-6 text-center"
                        >
                            <p class="text-xs text-slate-500 font-medium">
                                No notifications found.
                            </p>
                        </div>
                    </div>
                </div>
            @endauth
        </div>

        {{-- Profile / Login --}}
        @auth
            <a
                href="{{ $topbarProfile?->username ? url('/community/profile/' . $topbarProfile->username) : '#' }}"
                class="flex flex-1 flex-col items-center justify-center gap-1 text-slate-400 hover:text-slate-600 font-medium"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7-7h14a7 7 0 00-7-7"
                    />
                </svg>

                <span class="text-[10px] tracking-tight">
                    Profile
                </span>
            </a>
        @else
            <a
                href="{{ route('login') }}"
                class="flex flex-1 flex-col items-center justify-center gap-1 text-slate-400 hover:text-slate-600 font-medium"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10 17l5-5-5-5"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 12H3"
                    />
                </svg>

                <span class="text-[10px] tracking-tight">
                    Community Login
                </span>
            </a>
        @endauth

    </div>
</header>
