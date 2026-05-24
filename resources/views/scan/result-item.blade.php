<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Hasil Scan - Barcode Barang') }}
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

            @if ($fifoOlderStockWarning ?? false)
                <div class="p-4 rounded-lg border border-amber-400 bg-amber-50 text-amber-950 text-base leading-snug" role="alert">
                    <p class="font-semibold">Peringatan FIFO</p>
                    <p class="mt-1">Barang ini baru masuk gudang, tetapi masih ada stok <span class="font-medium">batch lebih lama</span> untuk part yang sama. Utamakan pengeluaran stok lama terlebih dahulu agar alur FIFO tetap benar.</p>
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
                $detailItem = $itemBarcode->item;
                $detailSubPack = (int) ($detailItem->qty_sub_pack ?? 0);
                $detailQtyPcs = (int) ($detailItem->dynamic_qty ?? $detailItem->qty ?? 0);
                $detailQtyPcsStatic = (int) ($detailItem->static_qty ?? $detailItem->qty ?? 0);
                $detailBoxApprox = ($detailSubPack > 0 && $detailQtyPcs > 0) ? (int) ceil($detailQtyPcs / $detailSubPack) : null;
                $staticBoxApprox =($detailSubPack > 0 && $detailQtyPcsStatic > 0 ) ? (int) ceil($detailQtyPcsStatic / $detailSubPack) : 0;
                $nowBoxApprox = max(0, $staticBoxApprox - $detailBoxApprox);
                
                $scanItem = $itemBarcode->item;
                $subPackPcs = (int) ($scanItem->qty_sub_pack ?? 0);
                $qtyPcs = (int) ($scanItem->dynamic_qty ?? $scanItem->qty ?? 0);
                $boxApprox = ($subPackPcs > 0 && $qtyPcs > 0) ? abs((int) ceil($qtyPcs / $subPackPcs)) : null;
            @endphp
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-base">
                    <div><span class="font-medium">Customer:</span> {{ $scanItem->customer ?? '-' }}</div>
                    <div><span class="font-medium">Part Name:</span> {{ $scanItem->part_name ?? '-' }}</div>
                    <div><span class="font-medium">Part Number:</span> {{ $scanItem->part_number ?? '-' }}</div>
                    <div><span class="font-medium">Model:</span> {{ $scanItem->model ?? '-' }}</div>
                    <div><span class="font-medium">Berat total (Kg):</span> {{ $scanItem->berat !== null ? $scanItem->berat : '-' }}</div>
                    <div><span class="font-medium">Berat packaging (gram):</span> {{ $scanItem->berat_packaging_gram !== null ? $scanItem->berat_packaging_gram : '-' }}</div>
                    <div><span class="font-medium">Berat per pcs (gram):</span> {{ $scanItem->berat_per_pcs_gram !== null ? $scanItem->berat_per_pcs_gram : '-' }}</div>
                    <div><span class="font-medium">Qty (label / static pack):</span> {{ $detailQtyPcsStatic }}</div>
                    <div><span class="font-medium">Qty sub pack:</span> {{ $subPackPcs > 0 ? $subPackPcs : '-' }}</div>
                    <!-- <div><span class="font-medium">jumlah box:</span> {{ $boxApprox !== null ? $boxApprox.' box' : '-' }}</div> -->
                    @php
                        $uniqueUmumCount = $itemBarcode->item->uniqueItems->where('status_keluar', false)->where('jenis', 'umum')->count();
                        $uniquePecahanCount = $itemBarcode->item->uniqueItems->where('status_keluar', false)->where('jenis', 'pecahan')->count();
                        $totalBox = ($itemBarcode->itemReceiving->jumlah_box ?? 0) + $uniqueUmumCount;
                    @endphp
                    <div><span class="font-medium">Jumlah Box:</span> {{ $totalBox }}</div>
                    <div><span class="font-medium">Inspector:</span> {{ $scanItem->inspector_name ?? '-' }}</div>
                    <div><span class="font-medium">Checker:</span> {{ $scanItem->checker_name ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Produksi:</span> {{ $itemBarcode->item->tgl_produksi?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Expired:</span> {{ $itemBarcode->item->tgl_expired?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium">Code:</span> {{ $itemBarcode->item->code ?? '-' }}</div>
                    <div><span class="font-medium">Posisi Rak:</span> {{ $itemBarcode->item->posisi_rak ?? '-' }}</div>
                    <div><span class="font-medium">Jumlah Box Pecahan:</span>{{$uniquePecahanCount}}</div>
                    <!-- <div><span class="font-medium">Tingkat:</span> {{ $itemBarcode->item->tingkat ?? '-' }}</div> -->
                    <div><span class="font-medium">Perusahaan:</span> <!-- {{ $itemBarcode->item->company->name ?? '-' }} --> PT TEKUN ASAS SUMBER MAKMUR</div>
                    @if($itemBarcode->item->uniqueItems->where('status_keluar', false)->isNotEmpty())
                        <div class="col-span-2 mt-4">
                            <span class="font-medium block mb-2">Unique Items:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($itemBarcode->item->uniqueItems->where('status_keluar', false) as $unique)
                                    @php
                                        $barcodeId = 'IB-'.$itemBarcode->item->id.'-'.($itemBarcode->item_receiving_id ?? 0).'-'.$unique->id;
                                        $scanUrl = route('scan.show', ['barcode_id' => $barcodeId]);
                                    @endphp
                                    <div class="flex items-center gap-1">
                                        <a href="{{ $scanUrl }}" class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200">
                                            Unique ID: {{ $unique->id }}
                                        </a>
                                        <button type="button" onclick="navigator.clipboard.writeText('{{ $scanUrl }}'); alert('URL berhasil disalin!');" class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                                            Copy
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <!-- <div class="col-span-2 border-t pt-2 mt-2"><span class="font-medium">Transfer Slip:</span> {{ $itemBarcode->itemReceiving->transfer_slip_no ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Terima FG:</span> {{ $itemBarcode->itemReceiving->tanggal_terima_fg?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium">Jumlah Box:</span> {{ $itemBarcode->itemReceiving->jumlah_box ?? '-' }}</div>
                    <div><span class="font-medium">Op. mobil:</span> {{ $itemBarcode->item->operatorMobil->name ?? '-' }}</div>
                    <div><span class="font-medium">Pengirim:</span> {{ $itemBarcode->item->pengirim->name ?? '-' }}</div>
                    <div><span class="font-medium">Op. forklift:</span> {{ $itemBarcode->item->operatorForklift->name ?? '-' }}</div> -->
                </div>

                @if (!($expiredWarning ?? false))
                <div class="mt-8 pt-6 border-t-2 border-egg-200">
                    <h3 class="text-lg font-bold text-egg-900 mb-3 uppercase tracking-wider">Mutasi stok (setelah scan)</h3>
                    <p class="text-sm text-egg-600 mb-4">Pilih arah mutasi (keluar/masuk) dan masukkan jumlah box.</p>

                    <form action="{{ route('scan.movement', ['barcode_id' => $itemBarcode->barcode_id]) }}" method="POST" class="bg-egg-50 p-6 rounded-xl border border-egg-200 space-y-4 max-w-lg">
                        @csrf
                        <fieldset>
                            <legend class="text-sm font-bold text-egg-800 mb-2 uppercase">Arah Mutasi</legend>
                            <div class="flex flex-col sm:flex-row gap-3 sm:gap-6 flex-wrap">
                                <label class="inline-flex items-center gap-2 cursor-pointer p-3 bg-white border border-egg-200 rounded-lg hover:border-egg-400">
                                    <input type="radio" name="direction" value="out" class="text-red-600 focus:ring-red-500" @checked(old('direction') === 'out') required />
                                    <span class="font-medium whitespace-nowrap">Barang Keluar</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer p-3 bg-white border border-egg-200 rounded-lg hover:border-egg-400">
                                    <input type="radio" name="direction" value="in" class="text-green-600 focus:ring-green-500" @checked(old('direction') === 'in') />
                                    <span class="font-medium whitespace-nowrap">Barang Masuk</span>
                                </label>
                            </div>
                        </fieldset>
                        <div>
                            <label for="scan_qty" class="block text-sm font-bold text-egg-800 mb-1">Jumlah Box</label>
                            <input id="scan_qty" type="number" name="qty" value="{{ old('qty', 1) }}" min="1" step="1" required
                                class="w-full rounded-lg border-egg-300 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
                            @error('qty')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
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
            @endif
        </div>
    </div>
</x-app-layout>
