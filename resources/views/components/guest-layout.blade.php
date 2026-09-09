<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'REIAC Community') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased text-gray-900">

      <div class="bg-red-500 text-white text-center p-5 font-bold">
        TOPBAR TEST
    </div>


    {{-- Top Navigation --}}
    <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- Page --}}
    <main class="min-h-[calc(100vh-4rem)] flex flex-col justify-center bg-gradient-to-br from-slate-50 to-blue-50 py-12 sm:px-6 lg:px-8">

        <a href="{{ route('home') }}"
            class="mx-auto text-center text-3xl font-extrabold tracking-tight text-[#031b43]">
            <span>REIAC</span>
            <span class="font-semibold text-slate-700">Community</span>
        </a>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">

            <div class="border border-gray-100 bg-white px-4 py-8 shadow-xl shadow-slate-200/60 sm:rounded-xl sm:px-10">

                {{ $slot }}

            </div>

        </div>

    </main>

</body>

</html>