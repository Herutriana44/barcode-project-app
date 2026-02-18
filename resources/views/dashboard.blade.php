<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Utama') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Modul 1: Barcode Barang -->
                <a href="{{ route('item-barcodes.create') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Barcode Barang</h3>
                            <p class="text-sm text-gray-500">Generate barcode untuk tiap barang individu</p>
                        </div>
                    </div>
                </a>

                <!-- Modul 2: Barcode Perusahaan -->
                <a href="{{ route('company-barcodes.create') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-orange-100 rounded-lg">
                            <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Barcode Perusahaan</h3>
                            <p class="text-sm text-gray-500">Generate barcode untuk melihat semua barang per perusahaan</p>
                        </div>
                    </div>
                </a>

                <!-- Scan -->
                <a href="{{ route('scan.index') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Scan Barcode</h3>
                            <p class="text-sm text-gray-500">Scan barcode untuk menampilkan informasi</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Terbaru</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('item-barcodes.index') }}" class="text-blue-600 hover:underline">Lihat semua Barcode Barang →</a>
                    <a href="{{ route('company-barcodes.index') }}" class="text-blue-600 hover:underline">Lihat semua Barcode Perusahaan →</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
