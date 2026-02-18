<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scan Barcode') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Camera Scan -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Scan dengan Kamera</h3>
                        <div id="reader" class="w-full border-2 border-gray-200 rounded-lg overflow-hidden" style="min-height: 300px;"></div>
                        <p class="text-sm text-gray-500 mt-2">Arahkan kamera ke barcode</p>
                    </div>

                    <!-- Manual Input -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Input Manual</h3>
                        <form id="manualScanForm">
                            <div class="flex gap-2">
                                <input type="text" id="barcode_input" placeholder="Masukkan kode barcode (IB-xxx atau CB-xxx)" class="flex-1 rounded-md border-gray-300">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cari</button>
                            </div>
                        </form>
                        <p class="text-sm text-gray-500 mt-2">Ketik kode barcode lalu klik Cari</p>
                    </div>
                </div>

                <!-- Result -->
                <div id="scan-result" class="mt-6 hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Hasil Scan</h3>
                    <div id="result-content" class="p-4 bg-gray-50 rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/scan.js'])
</x-app-layout>
