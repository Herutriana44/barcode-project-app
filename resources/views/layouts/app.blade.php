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
            /* Fixed navbar - most reliable positioning method */
            nav.fixed-navbar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 9999 !important;
                width: 100% !important;
            }

            /* Spacer to prevent content from hiding under fixed navbar */
            .navbar-spacer {
                width: 100%;
                height: 0;
            }

            /* Reset any conflicting styles */
            html {
                overflow-x: hidden;
            }

            body {
                margin: 0;
                padding: 0;
            }
        </style>

        <script>
            // Simple reliable fixed navbar with dynamic height adjustment
            function initFixedNavbar() {
                var nav = document.querySelector('nav.fixed-navbar');
                if (!nav) return;

                // Force fixed positioning via JavaScript as backup
                nav.style.position = 'fixed';
                nav.style.top = '0';
                nav.style.left = '0';
                nav.style.right = '0';
                nav.style.width = '100%';
                nav.style.zIndex = '9999';

                // Set spacer height to match navbar height
                var spacer = document.querySelector('.navbar-spacer');
                if (spacer) {
                    spacer.style.height = nav.offsetHeight + 'px';
                }
            }

            // Run on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initFixedNavbar);
            } else {
                initFixedNavbar();
            }

            // Run again on window load (after all resources)
            window.addEventListener('load', initFixedNavbar);

            // Update on resize
            window.addEventListener('resize', initFixedNavbar);
        </script>

        @stack('styles')
    </head>
    <body class="font-sans antialiased text-egg-900 text-lg leading-relaxed">
        @include('layouts.navigation')
        <div class="navbar-spacer"></div>

        <div class="min-h-screen bg-egg-50">
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
