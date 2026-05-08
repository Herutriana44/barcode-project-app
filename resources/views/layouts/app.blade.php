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
            /* Method 1: CSS sticky with multiple selectors for maximum compatibility */
            nav.sticky-nav {
                position: -webkit-sticky !important;
                position: sticky !important;
                top: 0 !important;
                z-index: 9999 !important;
                width: 100% !important;
            }

            /* Method 2: Fixed positioning fallback via class */
            nav.fixed-nav {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 9999 !important;
                width: 100% !important;
            }

            /* Ensure body doesn't have overflow that breaks sticky */
            html, body {
                overflow-x: hidden;
            }

            /* Spacer to prevent content jump when using fixed nav */
            .nav-spacer {
                height: 0;
                transition: height 0.2s ease;
            }
        </style>

        <script>
            // Method 3: JavaScript fallback to force sticky/fixed positioning
            document.addEventListener('DOMContentLoaded', function() {
                var nav = document.querySelector('nav.sticky-nav');
                if (!nav) return;

                // Force sticky via JavaScript
                nav.style.position = 'sticky';
                nav.style.top = '0';
                nav.style.zIndex = '9999';
                nav.style.width = '100%';

                // Method 4: Intersection Observer for robust sticky detection
                if ('IntersectionObserver' in window) {
                    var spacer = document.querySelector('.nav-spacer');
                    var observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            // If nav is not intersecting, it's scrolled past
                            if (!entry.isIntersecting) {
                                // Sticky is working, ensure it stays on top
                                nav.style.position = 'sticky';
                            }
                        });
                    }, {
                        root: null,
                        threshold: 1.0,
                        rootMargin: '-1px 0px 0px 0px'
                    });
                    observer.observe(nav);
                }

                // Method 5: Fallback - if sticky doesn't work after 1 second, use fixed
                setTimeout(function() {
                    var rect = nav.getBoundingClientRect();
                    // If nav is at top but page is scrolled, sticky might not be working
                    if (window.scrollY > 0 && rect.top > 0) {
                        console.log('Sticky not working, switching to fixed');
                        nav.classList.remove('sticky-nav');
                        nav.classList.add('fixed-nav');
                        if (spacer) {
                            spacer.style.height = nav.offsetHeight + 'px';
                        }
                    }
                }, 1000);

                // Update spacer height on resize
                window.addEventListener('resize', function() {
                    var spacer = document.querySelector('.nav-spacer');
                    var nav = document.querySelector('nav.fixed-nav');
                    if (nav && spacer) {
                        spacer.style.height = nav.offsetHeight + 'px';
                    }
                });
            });

            // Method 6: Handle scroll events to ensure nav stays on top
            window.addEventListener('scroll', function() {
                var nav = document.querySelector('nav.sticky-nav, nav.fixed-nav');
                if (nav) {
                    nav.style.zIndex = '9999';
                }
            }, { passive: true });
        </script>

        @stack('styles')
    </head>
    <body class="font-sans antialiased text-egg-900 text-lg leading-relaxed">
        <div class="min-h-screen bg-egg-50">
            @include('layouts.navigation')
            <div class="nav-spacer"></div>

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
