<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">

         <x-community.topbar :notifications-count="$notificationsCount ?? 0" />
        <div class="min-h-screen flex flex-col justify-center items-center py-6 sm:py-10 bg-gray-100">
            <!-- Laravel Logo component hata diya hai, ab forms direct clean container me show honge -->
            <div class="w-full sm:max-w-md px-2">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>