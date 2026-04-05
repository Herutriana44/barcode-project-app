<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label barang — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .label-sheet { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body class="font-sans antialiased text-egg-900 bg-egg-50 text-[11px] leading-tight">
    <div class="no-print sticky top-0 z-10 flex flex-wrap gap-2 p-2 bg-egg-100 border-b border-egg-200">
        <button type="button" onclick="window.print()" class="btn-egg-primary py-1 px-3 text-xs min-h-0">Cetak</button>
        <a href="{{ route('item-barcodes.index') }}" class="btn-egg-secondary py-1 px-3 text-xs min-h-0">Kembali</a>
    </div>

    <div class="p-2 max-w-[1200px] mx-auto space-y-2">
        @forelse($rows as $row)
            @php $ib = $row['itemBarcode']; $item = $ib->item; $recv = $ib->itemReceiving; @endphp
            <article class="label-sheet border border-egg-300 bg-white p-2 rounded-sm shadow-sm print:shadow-none print:border-egg-400">
                <div class="flex flex-wrap gap-2 items-start justify-between border-b border-egg-200 pb-1 mb-1">
                    <div class="flex flex-wrap gap-3 items-end min-w-0">
                        <div class="barcode-wrap overflow-x-auto max-w-[min(100%,14rem)] leading-none [&_svg]:max-h-10">
                            {!! $row['barcodeSvg'] !!}
                        </div>
                        <div class="qr-wrap w-[100px] shrink-0 [&_svg]:w-full [&_svg]:h-auto">
                            {!! $row['qrSvg'] !!}
                        </div>
                    </div>
                    <p class="font-mono font-semibold text-egg-900 text-[10px] break-all text-right max-w-[50%]">{{ $ib->barcode_id }}</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-2 gap-y-0.5">
                    <div><span class="font-medium text-egg-700">Perusahaan:</span> {{ $item->company->name ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Customer:</span> {{ $item->customer ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Part name:</span> {{ $item->part_name ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Part number:</span> {{ $item->part_number ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Model:</span> {{ $item->model ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Berat:</span> {{ $item->berat ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Qty:</span> {{ $item->qty ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Inspector:</span> {{ $item->inspector_name ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Tgl produksi:</span> {{ $item->tgl_produksi?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Tgl expired:</span> {{ $item->tgl_expired?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Code:</span> {{ $item->code ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Posisi rak:</span> {{ $item->posisi_rak ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Tingkat:</span> {{ $item->tingkat ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Ukuran material:</span> {{ $item->ukuran_material ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Jenis bahan:</span> {{ $item->jenis_bahan ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Qty material:</span> {{ $item->quantity_material ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">SJ material:</span> {{ $item->no_surat_jalan_material ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Tgl terima material:</span> {{ $item->tanggal_terima_material?->format('d/m/Y') ?? '-' }}</div>
                    <div class="col-span-2 sm:col-span-3 border-t border-egg-100 pt-0.5 mt-0.5"><span class="font-medium text-egg-700">Transfer slip:</span> {{ $recv->transfer_slip_no ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Tgl terima FG:</span> {{ $recv->tanggal_terima_fg?->format('d/m/Y') ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Jumlah box:</span> {{ $recv->jumlah_box ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Op. mobil:</span> {{ $item->operatorMobil->name ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Pengirim:</span> {{ $item->pengirim->name ?? '-' }}</div>
                    <div><span class="font-medium text-egg-700">Op. forklift:</span> {{ $item->operatorForklift->name ?? '-' }}</div>
                </div>
            </article>
        @empty
            <p class="text-sm text-egg-700 p-4 text-center">Belum ada barcode barang.</p>
        @endforelse
    </div>
</body>
</html>
