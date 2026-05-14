<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ config('app.description') }}">

        <x-favicon />
        <title>{{ config('app.name') }} — {{ config('app.tagline') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Sticky navbar for guest dashboard */
            header.sticky-navbar {
                position: -webkit-sticky !important;
                position: sticky !important;
                top: 0 !important;
                z-index: 9999 !important;
                width: 100% !important;
            }
        </style>

        <script>
            // Ensure sticky positioning works
            document.addEventListener('DOMContentLoaded', function() {
                var nav = document.querySelector('header.sticky-navbar');
                if (!nav) return;

                nav.style.position = 'sticky';
                nav.style.top = '0';
                nav.style.zIndex = '9999';
                nav.style.width = '100%';
            });
        </script>
    </head>
    <body class="font-sans antialiased text-egg-900 text-lg leading-relaxed min-h-screen flex flex-col bg-egg-50">
        <header class="sticky-navbar sticky top-0 z-[9999] bg-gradient-to-r from-egg-700 via-egg-600 to-egg-500 shadow-lg border-b border-egg-800/40">
            <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-12 min-h-[4.25rem] flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <x-application-logo class="h-11 w-auto rounded-md ring-2 ring-white/25 shadow-md shrink-0" />
                    <div class="min-w-0 flex flex-col leading-tight">
                        <span class="font-semibold text-white text-lg truncate drop-shadow-sm">{{ config('app.name') }}</span>
                        <span class="text-sm sm:text-base text-egg-100 truncate">{{ config('app.tagline') }}</span>
                    </div>
                </div>
                <nav class="flex items-center gap-3 sm:gap-4 shrink-0">
                    <a href="{{ route('login') }}" class="text-base sm:text-lg font-medium text-white hover:text-egg-50 px-3 py-2 rounded-lg hover:bg-white/15 transition-colors">
                        Masuk
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-base font-semibold rounded-lg bg-white text-egg-700 shadow-md border border-white/40 hover:bg-egg-100 transition-colors">
                            Daftar
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <main class="flex-1 w-full max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-12">
            <section class="w-full pt-10 pb-12 md:pt-14 md:pb-16 text-center">
                <p class="text-sm font-medium text-egg-600 uppercase tracking-wide mb-3">{{ config('app.tagline') }}</p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-egg-900 leading-tight">
                    {{ config('app.name') }}
                </h1>
                <p class="mt-6 text-xl text-egg-700 max-w-3xl mx-auto leading-relaxed">
                    {{ config('app.description') }}
                </p>
                <p class="mt-4 text-lg text-egg-600 max-w-3xl mx-auto leading-relaxed">
                    Modul barcode: generate label, kelompokkan per perusahaan, dan scan untuk melihat detail setelah masuk ke akun Anda.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('login') }}" class="btn-egg-primary min-w-[10rem]">
                        Masuk ke dashboard
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-egg-secondary min-w-[10rem]">
                            Buat akun baru
                        </a>
                    @endif
                </div>
            </section>

            <section class="w-full pb-16">
                <h2 class="text-center text-2xl font-bold text-egg-900 mb-8">Yang bisa Anda lakukan setelah login</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <article class="bg-white border border-egg-200 rounded-lg p-3 shadow-sm text-left hover:border-egg-300 transition-colors">
                        <div class="inline-flex p-2 rounded bg-egg-100 text-egg-800 mb-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-egg-900">Barcode barang</h3>
                        <p class="mt-2 text-egg-700 text-base leading-relaxed">
                            Buat dan cetak barcode untuk tiap barang dengan informasi label lengkap.
                        </p>
                    </article>
                    <article class="bg-white border border-egg-200 rounded-lg p-3 shadow-sm text-left hover:border-egg-300 transition-colors">
                        <div class="inline-flex p-2 rounded bg-egg-100 text-egg-800 mb-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-egg-900">Barcode perusahaan</h3>
                        <p class="mt-2 text-egg-700 text-base leading-relaxed">
                            Satu barcode untuk melihat kumpulan barang menurut perusahaan.
                        </p>
                    </article>
                    <article class="bg-white border border-egg-200 rounded-lg p-3 shadow-sm text-left hover:border-egg-300 transition-colors">
                        <div class="inline-flex p-2 rounded bg-egg-100 text-egg-800 mb-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-egg-900">Scan barcode</h3>
                        <p class="mt-2 text-egg-700 text-base leading-relaxed">
                            Gunakan kamera atau input manual untuk membuka detail barcode.
                        </p>
                    </article>
                </div>
            </section>
        </main>

        <footer class="border-t border-egg-200 bg-white py-6 mt-auto">
            <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-8 text-center text-base text-egg-700">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh fitur memerlukan akun.</p>
            </div>
        </footer>
    </body>
</html>
