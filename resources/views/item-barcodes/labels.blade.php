<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label barang — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    @include('item-barcodes.partials.qc-label-styles')
</head>
<body class="labels-print-root font-sans antialiased text-base">
    <div class="labels-toolbar no-print">
        <button type="button" onclick="window.print()" class="btn-egg-primary py-1 px-3 text-xs min-h-0">Cetak / Simpan PDF</button>
        <a href="{{ route('item-barcodes.index') }}" class="btn-egg-secondary py-1 px-3 text-xs min-h-0">Kembali</a>
        <p>Semua label barang dalam satu halaman. Di dialog cetak pilih <strong>Simpan sebagai PDF</strong> dan aktifkan margin default; satu halaman A4 memuat beberapa label kecil.</p>
    </div>

    <div class="label-grid">
        @forelse($rows as $row)
            @include('item-barcodes.partials.qc-label-card', [
                'itemBarcode' => $row['itemBarcode'],
                'qrSvg' => $row['qrSvg'],
                'barcodeSvg' => $row['labelBarcodeSvg'],
                'labelQtyPcs' => $row['labelQtyPcs'] ?? null,
            ])
        @empty
            <p class="labels-empty">Belum ada barcode barang.</p>
        @endforelse
    </div>
</body>
</html>
