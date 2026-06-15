<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/svg+xml" href="/logo.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0"
             style="background:radial-gradient(1200px 600px at 50% -10%, #1a1a22 0%, #0a0a0f 60%)">
            <a href="/" class="flex flex-col items-center gap-3">
                <span class="flex items-center justify-center h-14 w-14 rounded-2xl bg-brand-ink border border-gray-700 shadow-lg">
                    <img src="/logo.svg" alt="{{ config('app.name') }}" class="h-6 w-auto">
                </span>
                <span class="text-lg font-semibold text-white">{{ config('app.name') }}</span>
            </a>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 bg-white shadow-xl overflow-hidden sm:rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
