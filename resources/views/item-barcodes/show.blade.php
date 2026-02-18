<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Barcode Barang') }}
            </h2>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Print</button>
                <a href="{{ route('item-barcodes.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Buat Baru</a>
                <a href="{{ route('item-barcodes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 print:shadow-none">
                <div class="border-2 border-gray-200 p-6 rounded-lg">
                    <div class="flex justify-center mb-4">
                        <div class="barcode-container">
                            {!! $barcodeSvg !!}
                        </div>
                    </div>
                    <p class="text-center text-sm font-mono mb-6">{{ $itemBarcode->barcode_id }}</p>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="font-medium">Customer:</span> {{ $itemBarcode->item->customer ?? '-' }}</div>
                        <div><span class="font-medium">Part Name:</span> {{ $itemBarcode->item->part_name ?? '-' }}</div>
                        <div><span class="font-medium">Part Number:</span> {{ $itemBarcode->item->part_number ?? '-' }}</div>
                        <div><span class="font-medium">Model:</span> {{ $itemBarcode->item->model ?? '-' }}</div>
                        <div><span class="font-medium">Berat:</span> {{ $itemBarcode->item->berat ?? '-' }}</div>
                        <div><span class="font-medium">Qty:</span> {{ $itemBarcode->item->qty ?? '-' }}</div>
                        <div><span class="font-medium">Inspector:</span> {{ $itemBarcode->item->inspector_name ?? '-' }}</div>
                        <div><span class="font-medium">Tgl Produksi:</span> {{ $itemBarcode->item->tgl_produksi?->format('d/m/Y') ?? '-' }}</div>
                        <div><span class="font-medium">Tgl Expired:</span> {{ $itemBarcode->item->tgl_expired?->format('d/m/Y') ?? '-' }}</div>
                        <div><span class="font-medium">Code:</span> {{ $itemBarcode->item->code ?? '-' }}</div>
                        <div><span class="font-medium">Posisi Rak:</span> {{ $itemBarcode->item->posisi_rak ?? '-' }}</div>
                        <div><span class="font-medium">Tingkat:</span> {{ $itemBarcode->item->tingkat ?? '-' }}</div>
                        <div class="col-span-2 border-t pt-2 mt-2"><span class="font-medium">Transfer Slip:</span> {{ $itemBarcode->itemReceiving->transfer_slip_no ?? '-' }}</div>
                        <div><span class="font-medium">Tgl Terima FG:</span> {{ $itemBarcode->itemReceiving->tanggal_terima_fg?->format('d/m/Y') ?? '-' }}</div>
                        <div><span class="font-medium">Jumlah Box:</span> {{ $itemBarcode->itemReceiving->jumlah_box ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
