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
    </head>
    <body class="font-sans antialiased text-egg-900 min-h-screen flex flex-col bg-egg-50">
        <header class="border-b border-egg-200 bg-white/95 backdrop-blur-sm shadow-sm">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 h-12 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <x-application-logo class="h-9 w-auto" />
                    <div class="min-w-0 flex flex-col leading-tight">
                        <span class="font-semibold text-egg-900 truncate">{{ config('app.name') }}</span>
                        <span class="text-xs sm:text-sm text-egg-600 truncate">{{ config('app.tagline') }}</span>
                    </div>
                </div>
                <nav class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <a href="{{ route('login') }}" class="text-sm sm:text-base font-medium text-egg-800 hover:text-egg-900 px-2 py-2 rounded-md hover:bg-egg-50 transition-colors">
                        Masuk
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-egg-primary text-sm sm:text-base">
                            Daftar
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <section class="max-w-7xl mx-auto px-2 sm:px-4 pt-6 pb-8 md:pt-8 md:pb-10 text-center">
                <p class="text-xs font-medium text-egg-600 uppercase tracking-wide mb-2">{{ config('app.tagline') }}</p>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-egg-900 leading-tight">
                    {{ config('app.name') }}
                </h1>
                <p class="mt-6 text-lg text-egg-700 max-w-2xl mx-auto leading-relaxed">
                    {{ config('app.description') }}
                </p>
                <p class="mt-4 text-base text-egg-600 max-w-2xl mx-auto leading-relaxed">
                    Modul barcode: generate label, kelompokkan per perusahaan, dan scan untuk melihat detail—setelah masuk ke akun Anda.
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

            <section class="max-w-7xl mx-auto px-2 sm:px-4 pb-10">
                <h2 class="text-center text-lg font-semibold text-egg-900 mb-4">Yang bisa Anda lakukan setelah login</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <article class="bg-white border border-egg-200 rounded-lg p-3 shadow-sm text-left hover:border-egg-300 transition-colors">
                        <div class="inline-flex p-2 rounded bg-egg-100 text-egg-800 mb-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-egg-900">Barcode barang</h3>
                        <p class="mt-2 text-egg-700 text-sm leading-relaxed">
                            Buat dan cetak barcode untuk tiap barang dengan informasi label lengkap.
                        </p>
                    </article>
                    <article class="bg-white border border-egg-200 rounded-lg p-3 shadow-sm text-left hover:border-egg-300 transition-colors">
                        <div class="inline-flex p-2 rounded bg-egg-100 text-egg-800 mb-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-egg-900">Barcode perusahaan</h3>
                        <p class="mt-2 text-egg-700 text-sm leading-relaxed">
                            Satu barcode untuk melihat kumpulan barang menurut perusahaan.
                        </p>
                    </article>
                    <article class="bg-white border border-egg-200 rounded-lg p-3 shadow-sm text-left hover:border-egg-300 transition-colors">
                        <div class="inline-flex p-2 rounded bg-egg-100 text-egg-800 mb-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-egg-900">Scan barcode</h3>
                        <p class="mt-2 text-egg-700 text-sm leading-relaxed">
                            Gunakan kamera atau input manual untuk membuka detail barcode.
                        </p>
                    </article>
                </div>
            </section>
        </main>

        <footer class="border-t border-egg-200 bg-white py-4 mt-auto">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 text-center text-xs text-egg-700">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh fitur memerlukan akun.</p>
            </div>
        </footer>
    </body>
</html>
