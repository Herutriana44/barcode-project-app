<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl sm:text-3xl text-egg-900 leading-tight">
            {{ __('Menu Utama') }}
        </h2>
    </x-slot>

    <div class="py-10 sm:py-14">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-egg-100 border border-egg-400 text-egg-900 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                <!-- Modul 1: Barcode Barang -->
                <a href="{{ route('item-barcodes.create') }}" class="group block min-h-[11rem] sm:min-h-[12rem] p-8 sm:p-10 lg:p-12 bg-white overflow-hidden shadow-md border border-egg-200 rounded-xl sm:rounded-2xl hover:bg-egg-50 hover:shadow-lg hover:border-egg-300 transition">
                    <div class="flex items-start sm:items-center gap-5 sm:gap-6 lg:gap-8">
                        <div class="flex-shrink-0 p-4 sm:p-5 bg-egg-200 rounded-xl sm:rounded-2xl">
                            <svg class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 text-egg-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-egg-900 leading-snug">Barcode Barang</h3>
                            <p class="mt-2 sm:mt-3 text-base sm:text-lg lg:text-xl text-egg-700 leading-relaxed">Generate barcode untuk tiap barang individu</p>
                        </div>
                    </div>
                </a>

                <!-- Modul 2: Barcode Perusahaan -->
                <a href="{{ route('company-barcodes.create') }}" class="group block min-h-[11rem] sm:min-h-[12rem] p-8 sm:p-10 lg:p-12 bg-white overflow-hidden shadow-md border border-egg-200 rounded-xl sm:rounded-2xl hover:bg-egg-50 hover:shadow-lg hover:border-egg-300 transition">
                    <div class="flex items-start sm:items-center gap-5 sm:gap-6 lg:gap-8">
                        <div class="flex-shrink-0 p-4 sm:p-5 bg-egg-300/60 rounded-xl sm:rounded-2xl">
                            <svg class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 text-egg-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-egg-900 leading-snug">Barcode Perusahaan</h3>
                            <p class="mt-2 sm:mt-3 text-base sm:text-lg lg:text-xl text-egg-700 leading-relaxed">Generate barcode untuk melihat semua barang per perusahaan</p>
                        </div>
                    </div>
                </a>

                <!-- Scan -->
                <a href="{{ route('scan.index') }}" class="group block min-h-[11rem] sm:min-h-[12rem] p-8 sm:p-10 lg:p-12 bg-white overflow-hidden shadow-md border border-egg-200 rounded-xl sm:rounded-2xl hover:bg-egg-50 hover:shadow-lg hover:border-egg-300 transition">
                    <div class="flex items-start sm:items-center gap-5 sm:gap-6 lg:gap-8">
                        <div class="flex-shrink-0 p-4 sm:p-5 bg-egg-100 rounded-xl sm:rounded-2xl">
                            <svg class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 text-egg-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-egg-900 leading-snug">Scan Barcode</h3>
                            <p class="mt-2 sm:mt-3 text-base sm:text-lg lg:text-xl text-egg-700 leading-relaxed">Scan barcode untuk menampilkan informasi</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="mt-12 sm:mt-14">
                <h3 class="text-xl sm:text-2xl font-semibold text-egg-900 mb-5 sm:mb-6">Daftar Terbaru</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <a href="{{ route('item-barcodes.index') }}" class="text-lg sm:text-xl font-medium text-egg-700 hover:text-egg-900 hover:underline">Lihat semua Barcode Barang →</a>
                    <a href="{{ route('company-barcodes.index') }}" class="text-lg sm:text-xl font-medium text-egg-700 hover:text-egg-900 hover:underline">Lihat semua Barcode Perusahaan →</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
