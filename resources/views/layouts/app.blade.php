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

        <style>
            /* Sticky navbar - ensure it sticks to top when scrolling */
            nav.sticky-navbar {
                position: -webkit-sticky !important;
                position: sticky !important;
                top: 0 !important;
                z-index: 9999 !important;
                width: 100% !important;
            }

            /* Prevent any parent from breaking sticky */
            body {
                overflow-x: hidden;
            }
        </style>

        <script>
            // Ensure sticky positioning works
            document.addEventListener('DOMContentLoaded', function() {
                var nav = document.querySelector('nav.sticky-navbar');
                if (!nav) return;

                // Force sticky via JS
                nav.style.position = 'sticky';
                nav.style.top = '0';
                nav.style.zIndex = '9999';
                nav.style.width = '100%';

                console.log('Sticky navbar initialized');
            });
        </script>

        @stack('styles')
    </head>
    <body class="font-sans antialiased text-egg-900 text-lg leading-relaxed">
        <div class="min-h-screen bg-egg-50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-egg-200 print:hidden">
                    <div class="w-full max-w-[1920px] mx-auto py-4 px-4 sm:px-8 lg:px-12">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="print:p-0 w-full max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-12">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
