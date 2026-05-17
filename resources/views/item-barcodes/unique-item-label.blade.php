<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label Unique Item — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    @include('item-barcodes.partials.qc-label-styles')
</head>
<body class="labels-print-root font-sans antialiased text-base">
    <div class="labels-toolbar no-print">
        <button type="button" onclick="window.print()" class="btn-egg-primary py-1 px-3 text-xs min-h-0">Cetak / Simpan PDF</button>
        <a href="{{ url()->previous() }}" class="btn-egg-secondary py-1 px-3 text-xs min-h-0">Kembali</a>
    </div>

    <div class="label-grid">
        @forelse($rows as $row)
            {{-- quantityUseStatic = false agar qty dari unique item yang dipakai, bukan static_qty --}}
            @include('item-barcodes.partials.qc-label-card', [
                'itemBarcode' => $row['itemBarcode'],
                'qrSvg' => $row['qrSvg'],
                'barcodeSvg' => $row['labelBarcodeSvg'],
                'labelQtyPcs' => $row['labelQtyPcs'],
                'quantityUseStatic' => false,
                'headerCompanyName' => $labelHeaderCompanyName,
            ])
        @empty
            <p class="labels-empty">Tidak ada label.</p>
        @endforelse
    </div>
</body>
</html>
