<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-favicon />
        <title>{{ config('app.name') }} — {{ config('app.tagline') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased text-egg-900 text-lg leading-relaxed">
        <div class="min-h-screen bg-egg-50 flex flex-col">
            @include('layouts.navigation')
            <div class="h-[4.5rem] print:hidden"></div>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-egg-200 print:hidden shrink-0">
                    <div class="w-full max-w-[1920px] mx-auto py-4 px-4 sm:px-8 lg:px-12">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="print:p-0 flex-1 w-full max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-12">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
