@push('styles')
    @include('item-barcodes.partials.qc-label-styles')
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Detail Barcode Barang') }}
            </h2>
            <div class="flex flex-wrap gap-2 justify-end">
                <button type="button" onclick="window.print()" class="btn-egg-primary">Print</button>
                <a href="{{ route('item-barcodes.edit', $itemBarcode) }}" class="btn-egg-primary">Ubah</a>
                <form action="{{ route('item-barcodes.destroy', $itemBarcode) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Hapus barcode barang ini? Data penerimaan terkait juga akan dihapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-egg-secondary text-red-700 border-red-200 hover:bg-red-50">Hapus</button>
                </form>
                <a href="{{ route('item-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
                <a href="{{ route('item-barcodes.index') }}" class="btn-egg-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full qc-label-detail-outer max-w-4xl mx-auto print:max-w-none">
        <div class="space-y-8 w-full">
            @if (session('success'))
                <p class="no-print p-2 text-sm bg-egg-100 border border-egg-300 rounded text-egg-900">{{ session('success') }}</p>
            @endif
            <section class="qc-label-font-root" aria-label="Label cetak QC">
                <h3 class="text-base font-semibold text-egg-800 mb-2 no-print">Label cetak</h3>
                <p class="text-sm text-egg-600 mb-3 no-print">Tampilan ini sama dengan label pada <strong>Cetak semua label (PDF)</strong>. Saat Print, hanya bagian label yang dicetak.</p>
                <div class="qc-label-detail-wrap">
                    @include('item-barcodes.partials.qc-label-card', [
                        'itemBarcode' => $itemBarcode,
                        'qrSvg' => $qcLabelQrSvg,
                        'barcodeSvg' => $qcLabelBarcodeSvg,
                        'labelQtyPcs' => null,
                        'quantityUseStatic' => true,
                        'headerCompanyName' => $labelHeaderCompanyName,
                    ])
                </div>
                    <div class="mt-4 no-print flex flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <label for="isi-pages" class="text-sm font-medium text-egg-700">Jumlah Label:</label>
                            <input type="number" id="isi-pages" value="1" min="1" class="w-16 rounded border-egg-300 py-1 px-2 text-sm">
                            <a href="#" onclick="this.href='{{ route('item-barcodes.label-isi', $itemBarcode) }}?box_count=' + document.getElementById('isi-pages').value;" target="_blank" rel="noopener" class="btn-egg-secondary">Cetak Label Per Isi</a>
                        </div>
                        <!-- <a href="{{ route('item-barcodes.label-per-box', $itemBarcode) }}" target="_blank" rel="noopener" class="btn-egg-primary">Cetak Label Per Box</a> -->
                        <div class="flex items-center gap-2">
                            <label for="a4-pages" class="text-sm font-medium text-egg-700">Jumlah Halaman (x10 label):</label>
                            <input type="number" id="a4-pages" value="1" min="1" class="w-16 rounded border-egg-300 py-1 px-2 text-sm">
                            <a href="#" onclick="this.href='{{ route('item-barcodes.label-print-a4', $itemBarcode) }}?pages=' + document.getElementById('a4-pages').value;" target="_blank" rel="noopener" class="btn-egg-primary">Cetak A4</a>
                        </div>
                    </div>
            </section>

            <div class="no-print bg-white overflow-hidden shadow-md sm:rounded-xl p-6">
                <div class="border border-egg-200 p-6 rounded-xl">
                    <div class="flex flex-col sm:flex-row flex-wrap items-start justify-center gap-8 mb-6">
                        <!-- <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Barcode (Code 128)</span>
                            <p class="text-xs text-egg-600 text-center mb-2 max-w-md">Isi: URL scan — cocok untuk pemindai garis yang mengirim teks penuh.</p>
                            <div class="barcode-container overflow-x-auto max-w-full">
                                {!! $barcodeSvg !!}
                            </div>
                        </div> -->
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Kode QR</span>
                            <p class="text-xs text-egg-600 text-center mb-2 max-w-md">Isi: URL yang sama — dibuka langsung di browser ponsel.</p>
                            <div class="qr-container w-[220px] max-w-full flex justify-center [&_svg]:max-w-full [&_svg]:h-auto">
                                {!! $qrCodeSvg !!}
                            </div>
                            <a href="{{ route('item-barcodes.download-qr', $itemBarcode) }}" class="btn-egg-secondary mt-2 text-sm">Download QR (PNG)</a>
                        </div>
                    </div>
                    <div class="no-print mb-6 max-w-2xl mx-auto">
                        <p class="text-xs font-medium text-egg-700 mb-1">Tautan scan (salin untuk dibagikan)</p>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" readonly id="item-scan-url" value="{{ $scanUrl }}" class="flex-1 rounded-lg border-egg-300 py-2 px-3 text-sm font-mono bg-egg-50 text-egg-900">
                            <button type="button" class="btn-egg-secondary shrink-0 text-sm" data-copy-target="item-scan-url">Salin URL</button>
                        </div>
                    </div>
                    <p class="text-center text-lg font-mono mb-6 font-medium text-egg-900">{{ $itemBarcode->barcode_id }}</p>

                    @php
                        $detailItem = $itemBarcode->item;
                        $detailSubPack = (int) ($detailItem->qty_sub_pack ?? 0);
                        $detailQtyPcs = (int) ($detailItem->dynamic_qty ?? $detailItem->qty ?? 0);
                        $detailQtyPcsStatic = (int) ($detailItem->static_qty ?? $detailItem->qty ?? 0);
                        $detailBoxApprox = ($detailSubPack > 0 && $detailQtyPcs > 0) ? (int) ceil($detailQtyPcs / $detailSubPack) : null;
                        $staticBoxApprox =($detailSubPack > 0 && $detailQtyPcsStatic > 0 ) ? (int) ceil($detailQtyPcsStatic / $detailSubPack) : 0;
                        $nowBoxApprox = max(0, $staticBoxApprox - $detailBoxApprox);
                        $detailQtyPcs = abs($detailQtyPcsStatic - $detailQtyPcs);
                    @endphp
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-base">
                        <div><span class="font-medium">Customer:</span> {{ $detailItem->customer ?? '-' }}</div>
                        <div><span class="font-medium">Part Name:</span> {{ $detailItem->part_name ?? '-' }}</div>
                        <div><span class="font-medium">Part Number:</span> {{ $detailItem->part_number ?? '-' }}</div>
                        <div><span class="font-medium">Model:</span> {{ $detailItem->model ?? '-' }}</div>
                        <!-- <div><span class="font-medium">Qty total (pcs, stok):</span> {{ $detailQtyPcs }}</div> -->
                        <div><span class="font-medium">Qty sub pack (pcs):</span> {{ $detailSubPack > 0 ? $detailSubPack : '-' }}</div>
                        <!-- <div><span class="font-medium">Jumlah Box:</span> {{ $itemBarcode->itemReceiving->jumlah_box ?? '-' }}</div> -->
                        @php
                            $uniqueUmumCount = $itemBarcode->item->uniqueItems->where('status_keluar', false)->where('jenis', 'umum')->count();
                            $uniquePecahanCount = $itemBarcode->item->uniqueItems->where('status_keluar', false)->where('jenis', 'pecahan')->count();
                            $totalBox = ($itemBarcode->itemReceiving->jumlah_box ?? 0) + $uniqueUmumCount;
                        @endphp
                        <div><span class="font-medium">Jumlah Box:</span> {{ $totalBox }}</div>
                        <div><span class="font-medium">Qty (label / static pack):</span> {{ $detailItem->static_qty ?? '-' }}</div>
                        <div><span class="font-medium">Berat total (Kg):</span> {{ $detailItem->berat !== null ? $detailItem->berat : '-' }}</div>
                        <div><span class="font-medium">Berat packaging (gram):</span> {{ $detailItem->berat_packaging_gram !== null ? $detailItem->berat_packaging_gram : '-' }}</div>
                        <div><span class="font-medium">Berat per pcs (gram):</span> {{ $detailItem->berat_per_pcs_gram !== null ? number_format((float)$detailItem->berat_per_pcs_gram, 2, '.', '') : '-' }}</div>
                        <div><span class="font-medium">Inspector:</span> {{ $detailItem->inspector_name ?? '-' }}</div>
                        <div><span class="font-medium">Checker:</span> {{ $detailItem->checker_name ?? '-' }}</div>
                        <div><span class="font-medium">Jumlah Box Pecahan:</span>{{ $uniquePecahanCount }}</div>
                        <div class="col-span-2">
                            <form action="{{ route('item-barcodes.checker', $itemBarcode) }}" method="POST" class="flex flex-col sm:flex-row gap-2 items-start sm:items-end">
                                @csrf
                                @method('PATCH')
                                <div class="flex-1 w-full">
                                    <label class="block text-sm font-medium text-egg-800">Checker (manual)</label>
                                    <input type="text" name="checker" value="{{ old('checker', $detailItem->checker_name) }}" maxlength="255"
                                        class="mt-1 block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                                    @error('checker')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
                                </div>
                                <button type="submit" class="btn-egg-secondary text-sm">Simpan checker</button>
                            </form>
                        </div>
                        <div><span class="font-medium">Tgl Produksi:</span> {{ $detailItem->tgl_produksi?->format('d/m/Y') ?? '-' }}</div>
                        <div><span class="font-medium">Tgl Expired:</span> {{ $detailItem->tgl_expired?->format('d/m/Y') ?? '-' }}</div>
                        <div><span class="font-medium">Code:</span> {{ $detailItem->code ?? '-' }}</div>
                        <div>
                            <span class="font-medium">Posisi Rak:</span> 
                            <span class="break-words">{!! $detailItem->posisi_rak ? nl2br(e($detailItem->posisi_rak)) : '-' !!}</span>
                        </div>
                        <!-- <div><span class="font-medium">Posisi Rak:</span> {{ $detailItem->posisi_rak ?? '-' }}</div> -->
                        <!-- <div><span class="font-medium">Tingkat:</span> {{ $detailItem->tingkat ?? '-' }}</div> -->
                        <!-- <div class="col-span-2 border-t pt-2 mt-2"><span class="font-medium">Transfer Slip:</span> {{ $itemBarcode->itemReceiving->transfer_slip_no ?? '-' }}</div>
                        <div><span class="font-medium">Tgl Terima FG:</span> {{ $itemBarcode->itemReceiving->tanggal_terima_fg?->format('d/m/Y') ?? '-' }}</div>
                        <div><span class="font-medium">Jumlah Box:</span> {{ $itemBarcode->itemReceiving->jumlah_box ?? '-' }}</div> -->
                        <!-- <div><span class="font-medium">Op. mobil:</span> {{ $itemBarcode->item->operatorMobil->name ?? '-' }}</div>
                        <div><span class="font-medium">Pengirim:</span> {{ $itemBarcode->item->pengirim->name ?? '-' }}</div>
                        <div><span class="font-medium">Op. forklift:</span> {{ $itemBarcode->item->operatorForklift->name ?? '-' }}</div> -->
                    </div>
                </div>
            </div>

            <!-- Unique Items Section -->
            <div class="no-print bg-white overflow-hidden shadow-md sm:rounded-xl p-6">
                <div class="border border-egg-200 p-6 rounded-xl">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <h3 class="text-xl font-bold text-egg-900">Unique Items</h3>
                        <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">
                            <a href="{{ route('item-barcodes.unique-items.print-all', $itemBarcode) }}" target="_blank" rel="noopener" class="btn-egg-secondary text-center">Cetak Semua Label</a>
                            <button type="button" onclick="document.getElementById('add-unique-item-form').classList.toggle('hidden')" class="btn-egg-primary text-sm text-center">
                                + Tambah Unique Item
                            </button>
                        </div>
                    </div>

                    @if ($expiredWarning ?? false)
                        <div class="mb-4 p-4 rounded-lg border border-red-300 bg-red-50 text-red-900 text-base leading-snug" role="alert">
                            <p class="font-semibold">Peringatan: Barang Expired!</p>
                            <p class="mt-1">Barang ini sudah melewati masa expired. Harap lakukan pemeriksaan lebih lanjut.</p>
                        </div>
                    @elseif ($approachingExpiry ?? false)
                        <div class="mb-4 p-4 rounded-lg border border-orange-300 bg-orange-50 text-orange-900 text-base leading-snug" role="alert">
                            <p class="font-semibold">Peringatan: Barang Hampir Expired!</p>
                            <p class="mt-1">Barang ini akan expired pada tanggal {{ $itemBarcode->item->tgl_expired?->format('d/m/Y') }}. Disarankan untuk segera dikeluarkan (FIFO).</p>
                        </div>
                    @endif

                    <!-- Generate Bulk Section -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <form action="{{ route('item-barcodes.unique-items.generate-bulk', $itemBarcode) }}" method="POST" class="flex flex-wrap items-end gap-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-egg-800 mb-1">Jumlah Item</label>
                                <input type="number" name="n" min="1" required
                                    class="block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900"
                                    placeholder="Masukkan jumlah" />
                            </div>                            <button type="submit" class="btn-egg-primary text-sm">Generate Bulk</button>
                        </form>
                    </div>

                    <p class="text-sm text-egg-600 mb-4">
                        Unique items digunakan untuk item yang sama namun dengan nilai berbeda (qty, berat, dll) yang memerlukan label terpisah.
                    </p>

                    <!-- Form Tambah Unique Item -->
                    <div id="add-unique-item-form" class="hidden mb-6 p-4 bg-egg-50 rounded-lg border border-egg-200">
                        <form action="{{ route('item-barcodes.unique-items.store', $itemBarcode) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-egg-800 mb-1">Qty (pcs)</label>
                                <input type="number" name="qty" min="1" required
                                    class="block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" 
                                    placeholder="Qty" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-egg-800 mb-1">Tgl Produksi</label>
                                <input type="date" name="production_date"
                                    class="block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-egg-800 mb-1">Tgl Expired</label>
                                <input type="date" name="expired_date"
                                    class="block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                            </div>
                            <div class="col-span-1 sm:col-span-3 flex gap-2 justify-end">
                                <button type="submit" class="btn-egg-primary text-sm">Simpan</button>
                                <button type="button" onclick="document.getElementById('add-unique-item-form').classList.add('hidden')" class="btn-egg-secondary text-sm">Batal</button>
                            </div>
                        </form>
                    </div>

                    <!-- List Unique Items -->
                    @if($uniqueItems->count() > 0)
                        <form id="bulk-action-form" method="POST">
                            @csrf
                            <div class="flex flex-wrap gap-2 mb-4">
                                <button type="button" onclick="submitBulkAction('{{ route("item-barcodes.unique-items.bulk-print", $itemBarcode) }}', '_blank')" class="btn-egg-primary text-sm">Print Terpilih</button>
                                <button type="button" onclick="confirmBulkAction('{{ route("item-barcodes.unique-items.bulk-duplicate", $itemBarcode) }}')" class="btn-egg-secondary text-sm bg-green-50 text-green-800 border-green-200 hover:bg-green-100">Barang Masuk (Duplikasi)</button>
                                <button type="button" onclick="confirmBulkAction('{{ route("item-barcodes.unique-items.bulk-keluar", $itemBarcode) }}')" class="btn-egg-secondary text-sm bg-orange-50 text-orange-800 border-orange-200 hover:bg-orange-100">Barang Keluar</button>
                                <button type="button" onclick="confirmBulkAction('{{ route("item-barcodes.unique-items.bulk-destroy", $itemBarcode) }}')" class="btn-egg-secondary text-sm text-red-700 border-red-200 hover:bg-red-50">Hapus Terpilih</button>
                            </div>
                            <div class="space-y-3">
                                @foreach($uniqueItems as $uniqueItem)
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 bg-white border border-egg-200 rounded-lg">
                                        <div class="flex items-start gap-3 w-full sm:w-auto">
                                            <input type="checkbox" name="unique_item_ids[]" value="{{ $uniqueItem->id }}" class="mt-1 unique-item-checkbox rounded border-egg-300 text-egg-600 focus:ring-egg-500">
                                            <div class="flex-1 min-w-0">
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-1">
                                                    <div><span class="text-xs sm:text-sm font-medium text-egg-700">Qty:</span> <span class="font-semibold text-egg-900">{{ $uniqueItem->qty }}</span></div>
                                                    <div><span class="text-xs sm:text-sm font-medium text-egg-700">Prod:</span> <span class="font-semibold text-egg-900">{{ $uniqueItem->production_date?->format('d/m/Y') ?? '-' }}</span></div>
                                                    <div><span class="text-xs sm:text-sm font-medium text-egg-700">Exp:</span> <span class="font-semibold text-egg-900">{{ $uniqueItem->expired_date?->format('d/m/Y') ?? '-' }}</span></div>
                                                </div>
                                                <div class="text-xs text-egg-500 mt-1 truncate">
                                                    Dibuat: {{ $uniqueItem->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 w-full sm:w-auto justify-end">
                                            <a href="{{ route('item-barcodes.unique-items.print', [$itemBarcode, $uniqueItem]) }}" 
                                               target="_blank" 
                                               class="btn-egg-primary text-xs sm:text-sm">
                                                Cetak
                                            </a>
                                            @php
                                                $barcodeId = 'IB-'.$itemBarcode->item->id.'-'.($itemBarcode->item_receiving_id ?? 0).'-'.$uniqueItem->id;
                                                $scanUrl = route('scan.show', ['barcode_id' => $barcodeId]);
                                            @endphp
                                            <button type="button" 
                                                    onclick="navigator.clipboard.writeText('{{ $scanUrl }}'); alert('URL berhasil disalin!');" 
                                                    class="btn-egg-secondary text-xs sm:text-sm">
                                                Copy
                                            </button>
                                            <button type="button" 
                                                    onclick="toggleEditForm({{ $uniqueItem->id }})" 
                                                    class="btn-egg-secondary text-xs sm:text-sm">
                                                Edit
                                            </button>
                                            <form action="{{ route('item-barcodes.unique-items.destroy', [$itemBarcode, $uniqueItem]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn-egg-secondary text-xs sm:text-sm text-red-700 border-red-200 hover:bg-red-50"
                                                        onclick="return confirm('Hapus unique item ini?');">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Form Edit (Hidden by default) -->
                                    <div id="edit-form-{{ $uniqueItem->id }}" class="hidden p-4 bg-egg-50 rounded-lg border border-egg-200 -mt-3">
                                        <form action="{{ route('item-barcodes.unique-items.update', [$itemBarcode, $uniqueItem]) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label class="block text-sm font-medium text-egg-800 mb-1">Qty (pcs)</label>
                                                <input type="number" name="qty" min="1" value="{{ $uniqueItem->qty }}" required
                                                    class="block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-egg-800 mb-1">Tgl Produksi</label>
                                                <input type="date" name="production_date" value="{{ $uniqueItem->production_date?->format('Y-m-d') }}"
                                                    class="block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-egg-800 mb-1">Tgl Expired</label>
                                                <input type="date" name="expired_date" value="{{ $uniqueItem->expired_date?->format('Y-m-d') }}"
                                                    class="block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                                            </div>
                                            <div class="col-span-1 sm:col-span-3 flex gap-2 justify-end">
                                                <button type="submit" class="btn-egg-primary text-sm">Update</button>
                                                <button type="button" onclick="toggleEditForm({{ $uniqueItem->id }})" class="btn-egg-secondary text-sm">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                        <div class="mt-4">
                            {{ $uniqueItems->links() }}
                        </div>
                    @else

                        <div class="text-center py-8 text-egg-500">
                            <p>Belum ada unique item. Klik tombol "Tambah Unique Item" untuk menambahkan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function submitBulkAction(url, target) {
                const checked = document.querySelectorAll('.unique-item-checkbox:checked');
                if (checked.length === 0) { alert('Pilih minimal satu item'); return; }
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.target = target;
                
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                const hiddenToken = document.createElement('input');
                hiddenToken.type = 'hidden';
                hiddenToken.name = '_token';
                hiddenToken.value = csrf;
                form.appendChild(hiddenToken);

                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'unique_item_ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });
                
                console.log('Submitting to:', url, 'with', checked.length, 'items');
                document.body.appendChild(form);
                form.submit();
                form.remove();
            }

            function confirmBulkAction(url) {
                if (confirm('Hapus item terpilih?')) {
                    submitBulkAction(url, '_self');
                }
            }

            document.querySelectorAll('[data-copy-target]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var id = btn.getAttribute('data-copy-target');
                    var el = document.getElementById(id);
                    if (!el || !navigator.clipboard) return;
                    navigator.clipboard.writeText(el.value).then(function () {
                        var t = btn.textContent;
                        btn.textContent = 'Disalin';
                        setTimeout(function () { btn.textContent = t; }, 1500);
                    });
                });
            });

            function toggleEditForm(id) {
                var form = document.getElementById('edit-form-' + id);
                if (form) {
                    form.classList.toggle('hidden');
                }
            }
        </script>
    @endpush
</x-app-layout>
