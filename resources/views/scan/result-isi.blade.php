<x-app-layout>
    <div class="py-8 w-full max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow-md border border-egg-200">
            <h2 class="text-2xl font-bold mb-6">Detail Barcode Isi</h2>
            <div class="flex flex-col items-center">
                <div class="mb-4">
                    {!! App\Support\BarcodeQrCodes::qrSvgForIsiItem($itemBarcode->item_id, $itemBarcode->item_receiving_id) !!}
                </div>
                <div class="text-center">
                    <p class="font-mono text-lg font-bold">{{ $itemBarcode->barcode_id }}-ISI</p>
                    <p class="text-sm text-gray-600 mt-2">Part: {{ $itemBarcode->item->part_name }}</p>
                </div>
            </div>
            
            <div class="mt-8 border-t pt-6">
                <h3 class="font-bold text-lg mb-4">Informasi</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium">Customer:</span> {{ $itemBarcode->item->customer ?? '-' }}</div>
                    <div><span class="font-medium">Part Number:</span> {{ $itemBarcode->item->part_number ?? '-' }}</div>
                    <div><span class="font-medium">Model:</span> {{ $itemBarcode->item->model ?? '-' }}</div>
                    <div><span class="font-medium">Code:</span> {{ $itemBarcode->item->code ?? '-' }}</div>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('scan.index') }}" class="btn-egg-secondary">Kembali ke Scan</a>
            </div>
        </div>
    </div>
</x-app-layout>
