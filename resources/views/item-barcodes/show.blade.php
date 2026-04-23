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
                    ])
                </div>
            </section>

            <div class="no-print bg-white overflow-hidden shadow-md sm:rounded-xl p-6">
                <div class="border border-egg-200 p-6 rounded-xl">
                    <div class="flex flex-col sm:flex-row flex-wrap items-start justify-center gap-8 mb-6">
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Barcode (Code 128)</span>
                            <p class="text-xs text-egg-600 text-center mb-2 max-w-md">Isi: URL scan — cocok untuk pemindai garis yang mengirim teks penuh.</p>
                            <div class="barcode-container overflow-x-auto max-w-full">
                                {!! $barcodeSvg !!}
                            </div>
                        </div>
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Kode QR</span>
                            <p class="text-xs text-egg-600 text-center mb-2 max-w-md">Isi: URL yang sama — dibuka langsung di browser ponsel.</p>
                            <div class="qr-container w-[220px] max-w-full flex justify-center [&_svg]:max-w-full [&_svg]:h-auto">
                                {!! $qrCodeSvg !!}
                            </div>
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

                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-base">
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
                        <!-- <div><span class="font-medium">Op. mobil:</span> {{ $itemBarcode->item->operatorMobil->name ?? '-' }}</div>
                        <div><span class="font-medium">Pengirim:</span> {{ $itemBarcode->item->pengirim->name ?? '-' }}</div>
                        <div><span class="font-medium">Op. forklift:</span> {{ $itemBarcode->item->operatorForklift->name ?? '-' }}</div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
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
        </script>
    @endpush
</x-app-layout>
