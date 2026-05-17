<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label barang A4 — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    @include('item-barcodes.partials.qc-label-styles')
    <style>
        @media print {
            .page-break { page-break-after: always; }
            .label-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5mm; }
        }
        .label-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5mm; }
    </style>
</head>
<body class="labels-print-root font-sans antialiased text-base">
    <div class="labels-toolbar no-print">
        <button type="button" onclick="window.print()" class="btn-egg-primary py-1 px-3 text-xs min-h-0">Cetak / Simpan PDF</button>
        <a href="{{ url()->previous() }}" class="btn-egg-secondary py-1 px-3 text-xs min-h-0">Kembali</a>
    </div>

    <div class="label-grid">
        @forelse($rows as $row)
            @include('item-barcodes.partials.qc-label-card', [
                'itemBarcode' => $row['itemBarcode'],
                'qrSvg' => $row['qrSvg'],
                'barcodeSvg' => $row['labelBarcodeSvg'],
                'labelQtyPcs' => $row['labelQtyPcs'] ?? null,
                'quantityUseStatic' => false,
                'headerCompanyName' => $labelHeaderCompanyName,
            ])
            @if(($loop->iteration % 10) == 0 && !$loop->last)
                <div class="page-break"></div>
            @endif
        @empty
            <p class="labels-empty">Belum ada barcode barang.</p>
        @endforelse
    </div>
</body>
</html>
