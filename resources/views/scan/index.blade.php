<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-egg-900 leading-tight">
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

            <div class="bg-white overflow-hidden shadow-sm border border-egg-200 sm:rounded-lg p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Camera Scan -->
                    <div>
                        <h3 class="text-lg font-medium text-egg-900 mb-4">Scan dengan Kamera</h3>
                        <div id="reader" class="w-full border-2 border-egg-200 rounded-lg overflow-hidden" style="min-height: 300px;"></div>
                        <p class="text-sm text-egg-700 mt-2">Arahkan kamera ke barcode</p>
                    </div>

                    <!-- Manual Input -->
                    <div>
                        <h3 class="text-lg font-medium text-egg-900 mb-4">Input Manual</h3>
                        <form id="manualScanForm">
                            <div class="flex flex-col sm:flex-row gap-2 sm:items-stretch">
                                <input type="text" id="barcode_input" placeholder="Masukkan kode barcode (IB-xxx atau CB-xxx)" class="flex-1 rounded-md border-egg-300 shadow-sm focus:border-egg-500 focus:ring-egg-500 min-h-[2.5rem]">
                                <button type="submit" class="btn-egg-primary shrink-0">Cari</button>
                            </div>
                        </form>
                        <p class="text-sm text-egg-700 mt-2">Ketik kode barcode lalu klik Cari</p>
                    </div>
                </div>

                <!-- Result -->
                <div id="scan-result" class="mt-6 hidden">
                    <h3 class="text-lg font-medium text-egg-900 mb-4">Hasil Scan</h3>
                    <div id="result-content" class="p-4 bg-egg-50 border border-egg-200 rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/scan.js'])
</x-app-layout>
