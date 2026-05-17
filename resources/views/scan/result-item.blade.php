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

            @if ($fifoOlderStockWarning ?? false)
                <div class="p-4 rounded-lg border border-amber-400 bg-amber-50 text-amber-950 text-base leading-snug" role="alert">
                    <p class="font-semibold">Peringatan FIFO</p>
                    <p class="mt-1">Barang ini baru masuk gudang, tetapi masih ada stok <span class="font-medium">batch lebih lama</span> untuk part yang sama. Utamakan pengeluaran stok lama terlebih dahulu agar alur FIFO tetap benar.</p>
                </div>
            @endif

            @php
                $isExpired = $itemBarcode->item->tgl_expired?->isPast() ?? false;
            @endphp
            @if ($isExpired)
                <div class="p-4 rounded-lg border border-red-300 bg-red-50 text-red-900 text-base leading-snug" role="alert">
                    <p class="font-semibold">Warning: Expired items should be checked</p>
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
                    <!-- <div><span class="font-medium">Qty total (pcs):</span> {{ $qtyPcs }}</div> -->
                    <div><span class="font-medium">Qty sub pack:</span> {{ $subPackPcs > 0 ? $subPackPcs : '-' }}</div>
                    <!-- <div><span class="font-medium">jumlah box:</span> {{ $boxApprox !== null ? $boxApprox.' box' : '-' }}</div> -->
                     <div><span class="font-medium">Jumlah Box:</span> {{ ($itemBarcode->itemReceiving->jumlah_box) + ($itemBarcode->item->uniqueItems->where('status_keluar', false)->count()) }}</div>
                    <div><span class="font-medium">Inspector:</span> {{ $scanItem->inspector_name ?? '-' }}</div>
                    <div><span class="font-medium">Checker:</span> {{ $scanItem->checker_name ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Produksi:</span> {{ $itemBarcode->item->tgl_produksi?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Expired:</span> {{ $itemBarcode->item->tgl_expired?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium">Code:</span> {{ $itemBarcode->item->code ?? '-' }}</div>
                    <div><span class="font-medium">Posisi Rak:</span> {{ $itemBarcode->item->posisi_rak ?? '-' }}</div>
                    <div><span class="font-medium">Jumlah Box Pecahan:</span>{{($itemBarcode->item->uniqueItems->where('status_keluar', false)->count())}}</div>
                    <!-- <div><span class="font-medium">Tingkat:</span> {{ $itemBarcode->item->tingkat ?? '-' }}</div> -->
                    <div><span class="font-medium">Perusahaan:</span> <!-- {{ $itemBarcode->item->company->name ?? '-' }} --> PT TEKUN ASAS SUMBER MAKMUR</div>
                    <!-- <div class="col-span-2 border-t pt-2 mt-2"><span class="font-medium">Transfer Slip:</span> {{ $itemBarcode->itemReceiving->transfer_slip_no ?? '-' }}</div>
                    <div><span class="font-medium">Tgl Terima FG:</span> {{ $itemBarcode->itemReceiving->tanggal_terima_fg?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium">Jumlah Box:</span> {{ $itemBarcode->itemReceiving->jumlah_box ?? '-' }}</div>
                    <div><span class="font-medium">Op. mobil:</span> {{ $itemBarcode->item->operatorMobil->name ?? '-' }}</div>
                    <div><span class="font-medium">Pengirim:</span> {{ $itemBarcode->item->pengirim->name ?? '-' }}</div>
                    <div><span class="font-medium">Op. forklift:</span> {{ $itemBarcode->item->operatorForklift->name ?? '-' }}</div> -->
                </div>

                <div class="mt-8 border-t border-egg-200 pt-6">
                    <h3 class="text-lg font-bold text-egg-900 mb-3">Mutasi stok (setelah scan)</h3>
                    <p class="text-sm text-egg-600 mb-4">Pilih barang keluar (kurangi stok FIFO per part &amp; perusahaan) atau barang masuk (tambah qty pada baris barang ini), lalu isi jumlah wajib.</p>

                    <form action="{{ route('scan.movement', ['barcode_id' => $itemBarcode->barcode_id]) }}" method="POST" class="space-y-4 max-w-md">
                        @csrf
                        <fieldset>
                            <legend class="text-sm font-medium text-egg-800 mb-2">Arah</legend>
                            <div class="flex flex-wrap gap-4 text-base">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="direction" value="out" class="rounded-full border-egg-300 text-egg-700 focus:ring-egg-500" @checked(old('direction') === 'out') required />
                                    Barang keluar
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="direction" value="in" class="rounded-full border-egg-300 text-egg-700 focus:ring-egg-500" @checked(old('direction') === 'in') />
                                    Barang masuk
                                </label>
                            </div>
                        </fieldset>
                        <div>
                            <label for="scan_qty" class="block text-sm font-medium text-egg-800">Jumlah Box *</label>
                            <input id="scan_qty" type="number" name="qty" value="{{ old('qty', 1) }}" min="1" step="1" required
                                class="mt-1 block w-40 rounded-lg border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500" />
                            @error('qty')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="btn-egg-primary">Simpan mutasi</button>
                    </form>
                </div>

                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="link-egg inline-flex items-center text-base lg:text-lg">← Scan lagi</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
