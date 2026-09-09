@props([
    'title' => 'Notifications | REIAC Community',
    'user' => auth()->user(),
    'notificationsCount' => 0,
    'topContributors' => collect(),
    'trendingTopics' => collect(),
    'categories' => collect(),
    'tags' => collect(),
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="bg-[#f3f4f6]"
      x-data="{ mobileMenuOpen: false }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            background: #f3f4f6;
        }

        .notification-page {
            min-height: 100vh;
        }

        .notification-card {
            transition: background-color .18s ease, box-shadow .18s ease,
                        opacity .2s ease, transform .2s ease;
        }

        .notification-card:hover {
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
        }

        .group-invitation-actions {
            border-top: 1px solid #e2e8f0;
            margin-top: 12px;
            padding-top: 12px;
        }

        @media (max-width: 639px) {
            .notification-main {
                padding-bottom: 88px !important;
            }

            .group-invitation-actions {
                width: 100%;
            }

            .group-invitation-actions button {
                flex: 1 1 0;
            }
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            .notification-main {
                padding-bottom: 88px !important;
            }
        }
    </style>
</head>

<body class="notification-page min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white">

    {{-- COMMUNITY TOPBAR --}}
    <div class="sticky top-0 z-50">
        <x-community.topbar
            :notifications-count="$notificationsCount ?? 0"
            :user="$user"
        />
    </div>

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div
        x-show="mobileMenuOpen"
        x-cloak
        class="fixed inset-0 z-[60] lg:hidden flex"
    >
        <div
            @click="mobileMenuOpen = false"
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
        ></div>

        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative z-10 flex h-full w-80 max-w-[85%] flex-col overflow-y-auto bg-white shadow-2xl"
        >
            @auth
                @php
                    $mobileProfile = $user?->profile;
                    $mobileAvatar = $mobileProfile?->avatar
                        ? asset('storage/' . $mobileProfile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff';
                @endphp

                <div class="flex items-center gap-3 border-b border-slate-100 p-4">
                    <img
                        src="{{ $mobileAvatar }}"
                        alt="{{ $user?->name ?? 'User' }}"
                        class="h-10 w-10 rounded-full object-cover"
                    >

                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-slate-900">
                            {{ $user?->name }}
                        </div>

                        <a
                            href="{{ route('community.profile', $user?->profile?->username ?? $user?->id) }}"
                            class="text-xs font-semibold text-amber-600 hover:underline"
                        >
                            View Profile
                        </a>
                    </div>
                </div>
            @endauth

            <nav class="space-y-1 p-3 text-sm font-medium text-slate-600">
                <a
                    href="{{ route('community.index') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-slate-50"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 12l9-9 9 9M5 10v10h14V10M9 20v-6h6v6"/>
                    </svg>
                    Community
                </a>

                @auth
                    <a
                        href="{{ route('community.notifications') }}"
                        class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5 font-semibold text-slate-900"
                    >
                        <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifications
                    </a>
                @endauth

                <a
                    href="{{ route('community.index') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-slate-50"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-.586M7 7V6a2 2 0 012-2h8a2 2 0 012 2v6h-4l-4 4v-4H7a2 2 0 01-2-2V7h2z"/>
                    </svg>
                    Discussions
                </a>

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-red-600 hover:bg-red-50"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </div>

    {{-- MAIN COMMUNITY LAYOUT --}}
    <main class="notification-main mx-auto grid w-full max-w-[1520px] grid-cols-1 items-start gap-6 px-4 py-6 lg:grid-cols-[280px_minmax(0,1fr)_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] lg:px-6">

        {{-- LEFT SIDEBAR --}}
        <aside class="hidden lg:block">
            <x-community.sidebar
                :top-contributors="$topContributors ?? collect()"
                :categories="$categories ?? collect()"
                :tags="$tags ?? collect()"
            />
        </aside>

        {{-- CENTER --}}
        <section class="min-w-0 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="rounded-2xl border border-slate-200/70 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>

                            <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
                                Notifications
                            </h1>
                        </div>

                        <p class="mt-2 text-sm text-slate-500">
                            All your community notifications, including group invitations, read and unread activity.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                            {{ $notifications->count() }} total
                        </span>

                        @if($unreadCount > 0)
                            <span
                                id="page-unread-badge"
                                class="rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700"
                            >
                                <span id="page-unread-count">{{ $unreadCount }}</span> unread
                            </span>

                            <button
                                type="button"
                                id="mark-all-notifications-read"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-[#0b1329] px-3 py-2 text-xs font-bold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Mark all as read
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- NOTIFICATION LIST --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm">

                @forelse($notifications as $notification)

                    @php
                        $isUnread = $notification->read_at === null;

                        $data = is_array($notification->data)
                            ? $notification->data
                            : [];

                        $message = $data['message'] ?? 'You have a new notification.';
                        $type = strtolower((string) ($notification->type ?? ''));
                        $dataType = strtolower((string) ($data['type'] ?? ''));
                        $status = strtolower((string) ($data['status'] ?? 'pending'));
                        $url = $data['url'] ?? null;

                        /*
                        |--------------------------------------------------------------------------
                        | GROUP INVITATION
                        |--------------------------------------------------------------------------
                        |
                        | GroupInvitationNotification stores:
                        | type       = group_invitation
                        | status     = pending / accepted / rejected
                        | group_id
                        | group_slug
                        | group_name
                        | user_id
                        | inviter_id
                        | inviter_name
                        |
                        */
                        $isGroupInvitation =
                            $dataType === 'group_invitation' ||
                            str_contains($type, 'groupinvitationnotification');

                        $isGroupInvitationPending =
                            $isGroupInvitation && $status === 'pending';

                        $isGroupAccepted =
                            $isGroupInvitation && $status === 'accepted';

                        $isGroupRejected =
                            $isGroupInvitation && $status === 'rejected';

                        /*
                        |--------------------------------------------------------------------------
                        | Backward compatibility with older join-request data
                        |--------------------------------------------------------------------------
                        */
                        $isGroupJoinRequest =
                            $dataType === 'group_join_requested';

                        $isLegacyGroupAccepted =
                            str_contains($type, 'groupjoinrequestaccepted') ||
                            str_contains($dataType, 'group_join_request_accepted') ||
                            ($isGroupJoinRequest && $status === 'active');

                        $isLegacyGroupRejected =
                            str_contains($type, 'groupjoinrequestrejected') ||
                            str_contains($dataType, 'group_join_request_rejected') ||
                            ($isGroupJoinRequest && $status === 'rejected');

                        $notificationTitle = $data['title'] ?? null;

                        if (!$notificationTitle) {
                            if ($isGroupInvitation) {
                                $notificationTitle = 'Group Invitation';
                            } elseif ($isGroupJoinRequest) {
                                $notificationTitle = 'Group Join Request';
                            } elseif ($isLegacyGroupAccepted) {
                                $notificationTitle = 'Group Join Request Accepted';
                            } elseif ($isLegacyGroupRejected) {
                                $notificationTitle = 'Group Join Request Rejected';
                            } elseif (str_contains($type, 'comment')) {
                                $notificationTitle = 'New Comment';
                            } elseif (str_contains($type, 'reply')) {
                                $notificationTitle = 'New Reply';
                            } elseif (str_contains($type, 'like')) {
                                $notificationTitle = 'Post Liked';
                            } elseif (str_contains($type, 'follow')) {
                                $notificationTitle = 'New Follower';
                            } else {
                                $notificationTitle = 'Notification';
                            }
                        }

                        $groupName = $data['group_name'] ?? 'this group';
                        $groupSlug = $data['group_slug'] ?? null;
                    @endphp

                    <article
                        data-notification-card="{{ $notification->id }}"
                        class="notification-card border-b border-slate-100 p-4 last:border-b-0 sm:p-5 {{ $isUnread ? 'bg-blue-50/40' : 'bg-white' }}"
                    >
                        <div class="flex gap-3 sm:gap-4">

                            {{-- ICON --}}
                            <div class="shrink-0">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full
                                    {{ $isUnread ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500' }}">

                                    @if($isGroupInvitation || $isGroupJoinRequest)
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M18 8a6 6 0 00-12 0v3.5c0 .9-.3 1.8-.9 2.5L4 15h16l-1.1-1a3.5 3.5 0 01-.9-2.5V8z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M10 19a2 2 0 004 0"/>
                                        </svg>
                                    @elseif(str_contains($type, 'comment'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M8 10h8M8 14h5m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif(str_contains($type, 'like'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M14 9V5a3 3 0 00-6 0v4M8 9H5a2 2 0 00-2 2v7a2 2 0 002 2h9.5a2 2 0 001.94-1.515L18.5 12A2 2 0 0016.56 9H14z"/>
                                        </svg>
                                    @elseif(str_contains($type, 'follow'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 20a6 6 0 00-12 0m6-10a4 4 0 100-8 4 4 0 000 8zm6 3v6m3-3h-6"/>
                                        </svg>
                                    @elseif(str_contains($type, 'reply'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 17l-5-5 5-5m-5 5h10a6 6 0 016 6v1"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            {{-- CONTENT --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">

                                    <div class="min-w-0">
                                        <h2 class="text-sm font-bold {{ $isUnread ? 'text-slate-900' : 'text-slate-700' }}">
                                            {{ $notificationTitle }}
                                        </h2>

                                        <p class="mt-1 text-sm leading-6 {{ $isUnread ? 'text-slate-700' : 'text-slate-500' }}">
                                            {{ $message }}
                                        </p>

                                        @if($isGroupInvitation)
                                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                                @if($groupName)
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M3 7h18M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                                        </svg>
                                                        {{ $groupName }}
                                                    </span>
                                                @endif

                                                @if($isGroupAccepted)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Accepted
                                                    </span>
                                                @elseif($isGroupRejected)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M6 6l12 12M18 6L6 18"/>
                                                        </svg>
                                                        Rejected
                                                    </span>
                                                @elseif($isGroupInvitationPending)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">
                                                        Pending your response
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    @if($isUnread)
                                        <span
                                            class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-blue-600"
                                            title="Unread"
                                        ></span>
                                    @endif
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-2.5">
                                    <span class="text-xs text-slate-400">
                                        {{ $notification->created_at?->diffForHumans() }}
                                    </span>

                                    @if($isUnread)
                                        <span
                                            data-notification-status="{{ $notification->id }}"
                                            class="notification-status rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-semibold text-blue-700"
                                        >
                                            Unread
                                        </span>

                                        <button
                                            type="button"
                                            data-mark-read="{{ $notification->id }}"
                                            class="mark-notification-read inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-white px-2.5 py-1.5 text-[11px] font-bold text-blue-600 transition hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Mark as read
                                        </button>
                                    @else
                                        <span
                                            data-notification-status="{{ $notification->id }}"
                                            class="notification-status rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500"
                                        >
                                            Read
                                        </span>
                                    @endif

                                    {{-- =========================================================
                                         GROUP INVITATION: ACCEPT / REJECT
                                         These use the existing GroupController invitation routes.
                                         ========================================================= --}}
                                    @if(
                                        $isGroupInvitationPending &&
                                        $groupSlug
                                    )
                                        <div class="group-invitation-actions flex w-full flex-wrap items-center gap-2 sm:w-auto sm:border-0 sm:ml-2 sm:mt-0 sm:p-0">

                                            <button
                                                type="button"
                                                data-group-invitation="{{ $notification->id }}"
                                                data-group-slug="{{ $groupSlug }}"
                                                data-group-action="accept"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Accept
                                            </button>

                                            <button
                                                type="button"
                                                data-group-invitation="{{ $notification->id }}"
                                                data-group-slug="{{ $groupSlug }}"
                                                data-group-action="reject"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500/30 disabled:cursor-not-allowed disabled:opacity-60"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M6 6l12 12M18 6L6 18"/>
                                                </svg>
                                                Reject
                                            </button>
                                        </div>

                                    @elseif($isGroupAccepted)

                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Invitation accepted
                                        </span>

                                    @elseif($isGroupRejected)

                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 6l12 12M18 6L6 18"/>
                                            </svg>
                                            Invitation rejected
                                        </span>

                                    @elseif($isGroupJoinRequest)
                                        {{-- Keep older join-request notifications visible. --}}
                                        @if(!$isLegacyGroupAccepted && !$isLegacyGroupRejected)
                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                                Pending
                                            </span>
                                        @endif
                                    @endif

                                    @if($url)
                                        <a
                                            href="{{ $url }}"
                                            class="ml-auto inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-bold text-blue-600 transition hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            View
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>

                @empty

                    <div class="px-6 py-16 text-center sm:py-20">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>

                        <h2 class="mt-4 text-base font-bold text-slate-800">
                            No notifications yet
                        </h2>

                        <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">
                            When someone invites you to a group or interacts with your posts, comments, or profile, you'll see the activity here.
                        </p>

                        <a
                            href="{{ route('community.index') }}"
                            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#0b1329] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800"
                        >
                            Go to Community
                        </a>
                    </div>

                @endforelse
            </div>
        </section>

        {{-- RIGHT SIDEBAR --}}
        <aside class="hidden lg:block">
            <x-community.rightbar
                :user="$user"
                :trending-topics="$trendingTopics ?? collect()"
            />
        </aside>
    </main>

    {{-- MOBILE BOTTOM NAVIGATION --}}
    <div class="fixed bottom-0 left-0 right-0 z-40 flex items-center justify-around border-t border-slate-200 bg-white px-4 py-2 shadow-lg lg:hidden">

        <a
            href="{{ route('community.index') }}"
            class="flex flex-col items-center gap-1 text-slate-500"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 011-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 011 1m-6 0h6"/>
            </svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>

        <a
            href="{{ route('community.notifications') }}"
            class="flex flex-col items-center gap-1 font-bold text-[#0b1329]"
        >
            <div class="relative">
                <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>

                @if($unreadCount > 0)
                    <span class="absolute -right-2 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </div>

            <span class="text-[10px]">Notifications</span>
        </a>

        <button
            type="button"
            @click.prevent="
                @auth
                    $dispatch('open-post-modal')
                @else
                    window.dispatchEvent(new CustomEvent('open-login-modal'))
                @endauth
            "
            class="flex flex-col items-center justify-center -mt-5 focus:outline-none"
        >
            <div class="flex h-12 w-12 items-center justify-center rounded-full border-4 border-[#f3f4f6] bg-[#0b1329] text-white shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
            </div>
            <span class="mt-1 text-[10px] font-medium text-slate-500">Create</span>
        </button>

        @auth
            <a
                href="{{ route('community.profile', $user?->profile?->username ?? $user?->id) }}"
                class="flex flex-col items-center gap-1 text-slate-500"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 21a8 8 0 00-16 0m8-10a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
                <span class="text-[10px] font-medium">Profile</span>
            </a>
        @else
            <a
                href="{{ route('login') }}"
                class="flex flex-col items-center gap-1 text-slate-500"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4m-4-4l4-4m0 0l-4-4m4 4H3"/>
                </svg>
                <span class="text-[10px] font-medium">Login</span>
            </a>
        @endauth

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken =
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const unreadCountElement =
                document.getElementById('page-unread-count');

            const unreadBadge =
                document.getElementById('page-unread-badge');

            const markAllButton =
                document.getElementById('mark-all-notifications-read');

            function currentUnreadCount() {
                return Number(unreadCountElement?.textContent || 0);
            }

            function removeUnreadUi() {
                if (unreadCountElement) {
                    unreadCountElement.textContent = '0';
                }

                if (unreadBadge) {
                    unreadBadge.remove();
                }

                if (markAllButton) {
                    markAllButton.remove();
                }
            }

            function decrementUnreadCount() {
                if (!unreadCountElement) return;

                const count = Math.max(0, currentUnreadCount() - 1);

                unreadCountElement.textContent = String(count);

                if (count === 0) {
                    removeUnreadUi();
                }
            }

            function setNotificationReadUi(notificationId, card) {
                if (!card) return;

                card.classList.remove('bg-blue-50/40');
                card.classList.add('bg-white');

                const unreadDot = card.querySelector('[title="Unread"]');

                if (unreadDot) {
                    unreadDot.remove();
                }

                const status = card.querySelector(
                    '[data-notification-status="' + notificationId + '"]'
                );

                if (status) {
                    status.textContent = 'Read';
                    status.className =
                        'notification-status rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500';
                }

                const readButton = card.querySelector(
                    '[data-mark-read="' + notificationId + '"]'
                );

                if (readButton) {
                    readButton.remove();
                }
            }

            function setGroupInvitationResultUi(card, action) {
                if (!card) return;

                const actions = card.querySelectorAll(
                    '[data-group-invitation]'
                );

                actions.forEach(function (button) {
                    button.remove();
                });

                const oldPendingBadge = card.querySelector(
                    '.group-invitation-actions'
                );

                if (oldPendingBadge) {
                    oldPendingBadge.remove();
                }

                const contentArea = card.querySelector('.min-w-0.flex-1');

                if (!contentArea) return;

                const resultRow = document.createElement('div');

                resultRow.className =
                    'mt-2 flex flex-wrap items-center gap-2';

                if (action === 'accept') {
                    resultRow.innerHTML = `
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            Invitation accepted
                        </span>
                    `;
                } else {
                    resultRow.innerHTML = `
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 6l12 12M18 6L6 18"/>
                            </svg>
                            Invitation rejected
                        </span>
                    `;
                }

                const firstTextBlock =
                    contentArea.querySelector('.min-w-0');

                if (firstTextBlock) {
                    firstTextBlock.appendChild(resultRow);
                }

                /*
                 * Also change the notification message to the final state.
                 */
                const messageElement =
                    contentArea.querySelector('p');

                if (messageElement) {
                    if (action === 'accept') {
                        messageElement.textContent =
                            'You accepted the invitation to join this group.';
                    } else {
                        messageElement.textContent =
                            'You rejected the invitation to join this group.';
                    }
                }
            }

            async function markNotificationAsRead(notificationId, button) {
                if (!notificationId || button?.disabled) {
                    return false;
                }

                if (button) {
                    button.disabled = true;
                }

                try {
                    const response = await fetch(
                        "{{ url('/community/notifications') }}/" +
                        notificationId +
                        "/read",
                        {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Unable to mark notification as read.'
                        );
                    }

                    const card =
                        button?.closest('.notification-card');

                    setNotificationReadUi(notificationId, card);
                    decrementUnreadCount();

                    return true;

                } catch (error) {
                    console.error(
                        'Mark notification as read error:',
                        error
                    );

                    if (button) {
                        button.disabled = false;
                    }

                    alert(
                        error.message ||
                        'Unable to mark notification as read.'
                    );

                    return false;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Mark single notification as read
            |--------------------------------------------------------------------------
            */
            document
                .querySelectorAll('[data-mark-read]')
                .forEach(function (button) {

                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        markNotificationAsRead(
                            button.getAttribute('data-mark-read'),
                            button
                        );
                    });

                });

            /*
            |--------------------------------------------------------------------------
            | ACCEPT / REJECT GROUP INVITATION
            |--------------------------------------------------------------------------
            |
            | Uses the existing GroupController routes:
            |
            | POST /community/groups/{group}/invitation/accept
            | POST /community/groups/{group}/invitation/reject
            |
            |--------------------------------------------------------------------------
            */
            document
                .querySelectorAll('[data-group-invitation]')
                .forEach(function (button) {

                    button.addEventListener('click', async function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const notificationId =
                            button.getAttribute('data-group-invitation');

                        const groupSlug =
                            button.getAttribute('data-group-slug');

                        const action =
                            button.getAttribute('data-group-action');

                        if (!notificationId || !groupSlug || !action) {
                            return;
                        }

                        const card =
                            button.closest('.notification-card');

                        const actionButtons =
                            card?.querySelectorAll(
                                '[data-group-invitation]'
                            ) || [];

                        actionButtons.forEach(function (btn) {
                            btn.disabled = true;
                        });

                        const endpoint =
                            "{{ url('/community/groups') }}/" +
                            encodeURIComponent(groupSlug) +
                            "/invitation/" +
                            action;

                        try {
                            const response = await fetch(
                                endpoint,
                                {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({})
                                }
                            );

                            let result = {};

                            try {
                                result = await response.json();
                            } catch (_) {
                                result = {};
                            }

                            if (!response.ok || result.success === false) {
                                throw new Error(
                                    result.message ||
                                    'Unable to process the group invitation.'
                                );
                            }

                            /*
                             * GroupController::acceptInvitation /
                             * rejectInvitation updates the notification:
                             * - status = accepted / rejected
                             * - read_at = now()
                             */
                            setGroupInvitationResultUi(card, action);

                            setNotificationReadUi(
                                notificationId,
                                card
                            );

                            /*
                             * The invitation was unread before the action.
                             * Since the controller marks it read, decrement
                             * the page unread counter.
                             */
                            if (
                                card &&
                                card.classList.contains('bg-white')
                            ) {
                                /*
                                 * setNotificationReadUi() already changed
                                 * the class, so determine whether the
                                 * notification originally had an unread UI.
                                 */
                                const wasUnread =
                                    !!card.querySelector(
                                        '[data-notification-status="' +
                                        notificationId +
                                        '"]'
                                    )?.textContent
                                    ?.toLowerCase()
                                    ?.includes('unread');

                                /*
                                 * The status is now "Read", so the direct
                                 * status check above is not reliable after
                                 * mutation. Use a marker instead.
                                 */
                            }

                            /*
                             * More reliable counter handling:
                             * The invitation buttons are only rendered for
                             * pending invitations. They can be pending whether
                             * read or unread, so check the card's original
                             * state marker.
                             */
                            const originallyUnread =
                                card?.dataset?.originallyUnread === '1';

                            if (originallyUnread) {
                                decrementUnreadCount();
                                card.dataset.originallyUnread = '0';
                            }

                            /*
                             * If the API returns a group URL, keep the View
                             * link pointing to it.
                             */
                            if (result.group_url && card) {
                                const viewLink =
                                    card.querySelector(
                                        'a[href]'
                                    );

                                if (viewLink) {
                                    viewLink.href =
                                        result.group_url;
                                }
                            }

                        } catch (error) {
                            console.error(
                                'Group invitation error:',
                                error
                            );

                            actionButtons.forEach(function (btn) {
                                btn.disabled = false;
                            });

                            alert(
                                error.message ||
                                'Unable to process the group invitation.'
                            );
                        }
                    });

                });

            /*
            |--------------------------------------------------------------------------
            | Store original unread state for invitation cards.
            |--------------------------------------------------------------------------
            */
            document
                .querySelectorAll('.notification-card')
                .forEach(function (card) {
                    const status = card.querySelector(
                        '.notification-status'
                    );

                    card.dataset.originallyUnread =
                        status &&
                        status.textContent.trim().toLowerCase() === 'unread'
                            ? '1'
                            : '0';
                });

            /*
            |--------------------------------------------------------------------------
            | MARK ALL AS READ
            |--------------------------------------------------------------------------
            */
            if (markAllButton) {
                markAllButton.addEventListener(
                    'click',
                    async function () {

                        if (markAllButton.disabled) {
                            return;
                        }

                        markAllButton.disabled = true;

                        try {
                            const response = await fetch(
                                "{{ route('community.notifications.read-all') }}",
                                {
                                    method: 'PATCH',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                }
                            );

                            if (!response.ok) {
                                throw new Error(
                                    'Unable to mark notifications as read.'
                                );
                            }

                            document
                                .querySelectorAll('.notification-card')
                                .forEach(function (card) {

                                    card.classList.remove(
                                        'bg-blue-50/40'
                                    );

                                    card.classList.add('bg-white');

                                    const unreadDot =
                                        card.querySelector(
                                            '[title="Unread"]'
                                        );

                                    if (unreadDot) {
                                        unreadDot.remove();
                                    }

                                    const status =
                                        card.querySelector(
                                            '.notification-status'
                                        );

                                    if (
                                        status &&
                                        status.textContent
                                            .trim()
                                            .toLowerCase() === 'unread'
                                    ) {
                                        status.textContent = 'Read';

                                        status.className =
                                            'notification-status rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500';
                                    }

                                    const readButton =
                                        card.querySelector(
                                            '[data-mark-read]'
                                        );

                                    if (readButton) {
                                        readButton.remove();
                                    }

                                    card.dataset.originallyUnread = '0';
                                });

                            removeUnreadUi();

                        } catch (error) {
                            console.error(
                                'Mark all notifications error:',
                                error
                            );

                            markAllButton.disabled = false;

                            alert(
                                error.message ||
                                'Unable to mark notifications as read.'
                            );
                        }
                    }
                );
            }
        });
    </script>

</body>
</html>
