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
                    <div><span class="font-medium">Status:</span> {{ $uniqueItem->status_keluar ? 'Sudah Keluar' : 'Tersedia' }}</div>
                </div>

                @if (!($expiredWarning ?? false))
                <div class="mt-8 pt-6 border-t-2 border-egg-200">
                    <h3 class="text-lg font-bold text-egg-900 mb-3 uppercase tracking-wider">Mutasi stok (setelah scan)</h3>
                    <p class="text-sm text-egg-600 mb-4">Pilih arah mutasi (keluar/masuk).</p>

                    <form action="{{ route('scan.movement', ['barcode_id' => $barcodeId]) }}" method="POST" class="bg-egg-50 p-6 rounded-xl border border-egg-200 space-y-4 max-w-lg">
                        @csrf
                        <fieldset>
                            <legend class="text-sm font-bold text-egg-800 mb-2 uppercase">Arah Mutasi</legend>
                            <div class="flex flex-col sm:flex-row gap-3 sm:gap-6 flex-wrap">
                                <label class="inline-flex items-center gap-2 cursor-pointer p-3 bg-white border border-egg-200 rounded-lg hover:border-egg-400">
                                    <input type="radio" name="direction" value="out" class="text-red-600 focus:ring-red-500" @checked(old('direction') === 'out' || !$uniqueItem->status_keluar) required />
                                    <span class="font-medium whitespace-nowrap">Barang Keluar</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer p-3 bg-white border border-egg-200 rounded-lg hover:border-egg-400">
                                    <input type="radio" name="direction" value="in" class="text-green-600 focus:ring-green-500" @checked(old('direction') === 'in' || $uniqueItem->status_keluar) />
                                    <span class="font-medium whitespace-nowrap">Barang Masuk</span>
                                </label>
                            </div>
                        </fieldset>
                        <button type="submit" class="w-full py-3 px-6 bg-egg-900 text-white font-bold rounded-lg hover:bg-egg-800 transition shadow">Simpan mutasi</button>
                    </form>
                </div>
                @else
                    <div class="mt-8 pt-6 border-t-2 border-egg-200">
                        <p class="text-red-600 font-bold p-4 bg-red-50 border border-red-200 rounded text-center">
                            Barang sudah expired, mutasi dinonaktifkan.
                        </p>
                    </div>
                @endif

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
