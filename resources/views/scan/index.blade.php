<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Scan Barcode') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-6xl mx-auto w-full">
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- Camera Scan -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-egg-900">Scan dengan Kamera</h3>
                            <button id="switchCameraBtn" class="px-4 py-2 bg-egg-100 text-egg-800 rounded-lg text-sm font-semibold hover:bg-egg-200 transition hidden">
                                🔄 Ganti Kamera
                            </button>
                        </div>
                        <div id="reader" class="w-full border-2 border-egg-200 rounded-xl overflow-hidden" style="min-height: 360px;"></div>
                        <p class="text-base text-egg-700 mt-3">Arahkan kamera ke barcode garis atau QR pada label (keduanya berisi URL). Pemindai lain yang membuka URL tersebut juga sampai ke halaman yang sama.</p>
                    </div>

                    <!-- Manual Input -->
                    <div>
                        <h3 class="text-xl font-bold text-egg-900 mb-4">Input Manual</h3>
                        <form id="manualScanForm">
                            <div class="flex flex-col sm:flex-row gap-2 sm:items-stretch">
                                <input type="text" id="barcode_input" placeholder="IB-… / CB-…, /scan/…, atau tempel URL lengkap" class="flex-1 rounded-lg border-egg-300 py-3 px-4 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 min-h-[3rem]">
                                <button type="submit" class="btn-egg-primary shrink-0">Cari</button>
                            </div>
                        </form>
                        <p class="text-base text-egg-700 mt-3">Ketik ID pada label, path <span class="font-mono text-sm">/scan/IB-…</span>, atau tempel URL penuh dari QR/barcode. Klik Cari.</p>
                    </div>
                </div>

                <!-- Result -->
                <div id="scan-result" class="mt-8 hidden">
                    <h3 class="text-xl font-bold text-egg-900 mb-4">Hasil Scan</h3>
                    <div id="result-content" class="p-6 bg-egg-50 border border-egg-200 rounded-xl text-base"></div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/scan.js'])
</x-app-layout>
