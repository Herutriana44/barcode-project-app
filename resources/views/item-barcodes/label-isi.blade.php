<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label per isi — {{ $itemBarcode->barcode_id }}</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: ui-sans-serif, system-ui, sans-serif; background: #f6f5f2; margin: 0; }
        .toolbar { padding: 10px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .btn { border: 1px solid #c9c0b3; background: #fff; border-radius: 8px; padding: 8px 12px; cursor: pointer; font-size: 14px; }
        .hint { color: #4d453a; font-size: 13px; }
        .grid { display: grid; gap: 8mm; padding: 10mm; grid-template-columns: repeat(2, 1fr); }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .grid { padding: 0; gap: 6mm; }
        }

        /* Card style: mengikuti tampilan contoh (table kiri + kotak barcode kanan) */
        .isi-card {
            width: 50mm;
            height: 40mm;
            border: 2px solid #222;
            border-radius: 2mm;
            background: #fff;
            overflow: hidden;
        }
        .isi-top {
            display: grid;
            grid-template-columns: 18mm 1fr;
            border-bottom: 2px solid #222;
            min-height: 8mm;
        }
        .isi-no {
            border-right: 2px solid #222;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 10pt;
        }
        .isi-company {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 10pt;
            letter-spacing: 0.03em;
        }
        .isi-body {
            display: grid;
            /* Baris 1: part no + part name full width (di atas QR)
               Baris 2: field lain kiri + QR kanan */
            grid-template-columns: 1fr 18mm;
            grid-template-rows: auto 1fr;
            min-height: 32mm;
        }

        .isi-fields-top {
            grid-column: 1 / -1;
            padding: 1mm 2mm;
            border-bottom: 2px solid #222;
            display: grid;
            gap: 1mm;
            font-size: 7pt;
        }

        .isi-fields {
            grid-column: 1 / 2;
            padding: 1mm 2mm;
            display: grid;
            gap: 0.5mm;
            font-size: 7pt;
        }
        .row { display: grid; grid-template-columns: 14mm 3mm 1fr; align-items: baseline; }
        .k { font-weight: 500; }
        .sep { text-align: center; }
        .v { font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .isi-barcode-box {
            grid-column: 2 / 3;
            grid-row: 2 / 3;
            display: flex;
            /* QR kecil di pojok kanan bawah */
            align-items: flex-end;
            justify-content: flex-end;
            padding: 1mm;
        }
        .isi-barcode-wrap {
            /* Buat kotak (square) dan lebih kecil seperti referensi */
            width: 18mm;
            height: 18mm;
            border: 2px solid #222;
            border-radius: 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1mm;
            overflow: hidden;
        }
        /* QR harus kotak dan memenuhi area. */
        .isi-barcode-wrap svg { width: 100%; height: 100%; display: block; }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button type="button" onclick="window.print()" class="btn">Cetak / Simpan PDF</button>
        <a href="{{ route('item-barcodes.show', $itemBarcode) }}" class="btn" style="text-decoration:none;color:inherit;">Kembali</a>
        <span class="hint">Satu label = satu box. Isi per box = qty sub pack (pcs). Jumlah label = total qty (pcs) ÷ qty sub pack (sisa pcs pada label terakhir). Tanpa sub pack: satu label untuk seluruh qty.</span>
    </div>

    <div class="grid">
        @foreach($labels as $lbl)
            @include('item-barcodes.partials.qc-label-isi-card', [
                'itemBarcode' => $itemBarcode,
                'qrSvg' => $lbl['qrSvg'],
                'qtyInPack' => $lbl['qtyInPack'],
            ])
        @endforeach
    </div>
</body>
</html>

