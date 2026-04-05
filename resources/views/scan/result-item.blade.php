<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Hasil Scan - Barcode Barang') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-4xl mx-auto w-full">
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-base">
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
                    <div><span class="font-medium">Perusahaan:</span> {{ $itemBarcode->item->company->name ?? '-' }}</div>
                    <div class="col-span-2 border-t pt-2 mt-2"><span class="font-medium">Transfer Slip:</span> {{ $itemBarcode->itemReceiving->transfer_slip_no ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Terima FG:</span> {{ $itemBarcode->itemReceiving->tanggal_terima_fg?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium">Jumlah Box:</span> {{ $itemBarcode->itemReceiving->jumlah_box ?? '-' }}</div>
                    <div><span class="font-medium">Op. mobil:</span> {{ $itemBarcode->item->operatorMobil->name ?? '-' }}</div>
                    <div><span class="font-medium">Pengirim:</span> {{ $itemBarcode->item->pengirim->name ?? '-' }}</div>
                    <div><span class="font-medium">Op. forklift:</span> {{ $itemBarcode->item->operatorForklift->name ?? '-' }}</div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="link-egg inline-flex items-center text-base lg:text-lg">← Scan lagi</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
