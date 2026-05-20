<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Hasil Scan - Barcode Unique Item') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-4xl mx-auto w-full space-y-6">
            @if (session('success'))
                <p class="p-3 text-sm bg-green-50 border border-green-200 rounded text-green-900">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p class="p-3 text-sm bg-red-50 border border-red-200 rounded text-red-900">{{ session('error') }}</p>
            @endif

            @if(isset($expiringList) && $expiringList['totalCount'] > 0)
                <div class="p-4 rounded-lg border border-orange-400 bg-orange-100 text-orange-950 text-base leading-snug" role="alert">
                    <p class="font-semibold text-red-600">Peringatan: Stok Mendekati Expired</p>
                    <p class="mt-1 text-red-600 font-bold">warning : ada {{ $expiringList['totalCount'] }} barang yang mendekati masa expired tanggal {{ $expiringList['earliestDate'] ? $expiringList['earliestDate']->locale('id')->translatedFormat('d F') : '-' }}. tolong mendahulukan barang lama</p>
                </div>
            @endif

            @if ($expiredWarning ?? false)
                <div class="p-4 rounded-lg border border-red-300 bg-red-50 text-red-900 text-base leading-snug" role="alert">
                    <p class="font-semibold">Peringatan: Barang Expired!</p>
                    <p class="mt-1">Barang ini sudah melewati masa expired. Harap lakukan pemeriksaan lebih lanjut.</p>
                </div>
            @elseif ($approachingExpiry ?? false)
                <div class="p-4 rounded-lg border border-orange-300 bg-orange-50 text-orange-900 text-base leading-snug" role="alert">
                    <p class="font-semibold">Peringatan: Barang Hampir Expired!</p>
                    <p class="mt-1">Barang ini akan expired dalam 30 hari ke depan. Disarankan untuk segera dikeluarkan (FIFO).</p>
                </div>
            @endif

            @php
                $item = $uniqueItem->item;
            @endphp
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-base">
                    <div><span class="font-medium">Customer:</span> {{ $item->customer ?? '-' }}</div>
                    <div><span class="font-medium">Part Name:</span> {{ $item->part_name ?? '-' }}</div>
                    <div><span class="font-medium">Part Number:</span> {{ $item->part_number ?? '-' }}</div>
                    <div><span class="font-medium">Model:</span> {{ $item->model ?? '-' }}</div>
                    <div><span class="font-medium">Berat total (Kg):</span> {{ $item->berat !== null ? $item->berat : '-' }}</div>
                    <div><span class="font-medium">Berat packaging (gram):</span> {{ $item->berat_packaging_gram !== null ? $item->berat_packaging_gram : '-' }}</div>
                    <div><span class="font-medium">Berat per pcs (gram):</span> {{ $item->berat_per_pcs_gram !== null ? $item->berat_per_pcs_gram : '-' }}</div>
                    <div><span class="font-medium">Qty:</span> {{ $uniqueItem->qty ?? 0 }} Pcs</div>
                    <div><span class="font-medium">Inspector:</span> {{ $item->inspector_name ?? '-' }}</div>
                    <div><span class="font-medium">Checker:</span> {{ $item->checker_name ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Produksi:</span> {{ $uniqueItem->production_date?->format('d/m/Y') ?? ($item->tgl_produksi?->format('d/m/Y') ?? '-') }}</div>
                    <div><span class="font-medium">Tgl Expired:</span> {{ $uniqueItem->expired_date?->format('d/m/Y') ?? ($item->tgl_expired?->format('d/m/Y') ?? '-') }}</div>
                    <div><span class="font-medium">Code:</span> {{ $item->code ?? '-' }}</div>
                    <div><span class="font-medium">Posisi Rak:</span> {{ $item->posisi_rak ?? '-' }}</div>
                    <div><span class="font-medium">Perusahaan:</span> PT TEKUN ASAS SUMBER MAKMUR</div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="link-egg inline-flex items-center text-base lg:text-lg">← Scan lagi</a>
                </div>
            </div>

            @if(isset($expiringList) && ($expiringList['items']->isNotEmpty() || $expiringList['uniqueItems']->isNotEmpty()))
                <div class="bg-white overflow-hidden shadow-md border border-orange-200 sm:rounded-xl p-8">
                    <h3 class="text-lg font-bold text-orange-900 mb-4">Peringatan Expired Lainnya:</h3>
                    <ul class="list-disc list-inside space-y-2">
                        @foreach($expiringList['items'] as $expItem)
                            <li>
                                <a href="{{ route('scan.show', ['barcode_id' => $expItem->itemBarcodes->first()?->barcode_id ?? '']) }}" class="text-blue-600 hover:underline">
                                    <span class="font-medium">{{ $expItem->part_name }}</span> ({{ $expItem->tgl_expired->format('d/m/Y') }})
                                </a>
                            </li>
                        @endforeach
                        @foreach($expiringList['uniqueItems'] as $expUnique)
                            @php
                                $barcodeId = 'IB-'.$expUnique->item->id.'-'.($expUnique->item->itemReceivings()->first()?->id ?? 0).'-'.$expUnique->id;
                            @endphp
                            <li>
                                <a href="{{ route('scan.show', ['barcode_id' => $barcodeId]) }}" class="text-blue-600 hover:underline">
                                    <span class="font-medium">{{ $expUnique->item->part_name ?? 'Unique Item' }}</span> ({{ $expUnique->expired_date?->format('d/m/Y') ?? '-' }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
