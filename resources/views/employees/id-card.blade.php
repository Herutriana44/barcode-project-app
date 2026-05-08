<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ID Card — {{ $employee->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @font-face {
            font-family: "Canva Sans";
            src: url("/Fonts/Canva-Sans-Regular/Typeface/Canva Sans/Desktop/CanvaSans-Regular.otf") format("opentype");
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        body {
            font-family: "Canva Sans", ui-sans-serif, system-ui, sans-serif;
            background: #e8e4dc;
            padding: 12px;
        }
        @page {
            size: 53mm 90mm;
            margin: 0;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
        }
        .card {
            width: 53mm;
            height: 90mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border: 0;
        }

        /* Template image - layer paling belakang */
        .bg-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
        }

        /* Foto persegi panjang: 2.17cm x 3.27cm, x 0.15cm, y 2.64cm */
        .photo {
            position: absolute;
            left: 1.5mm;
            top: 26.4mm;
            width: 21.7mm;
            height: 32.7mm;
            overflow: hidden;
            background: #d9d9d9;
            z-index: 1;
        }
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .photo .no-photo {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            color: rgba(0,0,0,0.55);
            text-align: center;
            padding: 2mm;
        }

        /* Text: Departemen - w 1.92cm, h 0.18cm, x 3.06cm, y 3.20cm (css top: 31.5mm) */
        .dept {
            position: absolute;
            left: 30.6mm;
            top: 31.5mm;
            width: 30mm;
            height: 1.8mm;
            font-family: "Canva Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 4.5pt;
            line-height: 1;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 3;
        }

        /* Text: Jabatan - w 1.35cm, h 0.18cm, x 3.06cm, y 3.69cm (css top: 38.5mm) */
        .jabatan {
            position: absolute;
            left: 30.6mm;
            top: 38.5mm;
            width: 30mm;
            height: 1.8mm;
            font-family: "Canva Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 4.5pt;
            line-height: 1;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 3;
        }

        /* Text: Status Kerja - w 0.82cm, h 0.18cm, x 3.06cm, y 4.29cm (css top: 44.5mm) */
        .status {
            position: absolute;
            left: 30.6mm;
            top: 44.5mm;
            width: 30mm;
            height: 1.8mm;
            font-family: "Canva Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 4.5pt;
            line-height: 1;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 3;
        }

        /* Text: Nama Karyawan - w 1.45cm, h 0.18cm, x 3.06cm, y 4.93cm (css top: 51.5mm) */
        .name {
            position: absolute;
            left: 30.6mm;
            top: 51.5mm;
            width: 30mm;
            height: 1.8mm;
            font-family: "Canva Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 4.5pt;
            line-height: 1;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 3;
        }

        /* Kotak Barcode - w 1.53cm, h 1.53cm, x 3.5cm, y 7.82cm (css top: 72mm) */
        .qr {
            position: absolute;
            left: 35mm;
            top: 72mm;
            width: 15.3mm;
            height: 15.3mm;
            z-index: 3;
        }
        .qr svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Text: NIP - w 1.03cm, h 0.18cm, x 3.06cm, y 5.58cm (css top: 58.1mm) */
        .nip {
            position: absolute;
            left: 30.6mm;
            top: 58.1mm;
            width: 30mm;
            height: 1.8mm;
            font-family: "Canva Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 4.5pt;
            line-height: 1;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 3;
        }

        /* Fallback jika template image tidak tersedia */
        .fallback {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, #e2f6f4 0%, #f7fffe 55%, #ffffff 100%);
            opacity: 1;
            z-index: -1;
        }

        .toolbar {
            margin-bottom: 10px;
        }
        .toolbar button {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #a89880;
            background: #fff;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button type="button" onclick="window.print()">Cetak (9 × 5,3 cm)</button>
    </div>

    <div class="card">
        <div class="fallback" aria-hidden="true"></div>
        <img class="bg-img" src="{{ asset(str_replace(' ', '%20', 'DESAIN 2.png')) }}" alt="" aria-hidden="true" />

        <div class="photo">
            @if ($employee->photoPublicUrl())
                <img src="{{ $employee->photoPublicUrl() }}" alt="" />
            @else
                <div class="no-photo">Foto</div>
            @endif
        </div>

        <div class="dept">{{ $employee->departemen ?? 'Departemen' }}</div>
        <div class="jabatan">{{ $employee->jabatan ?? 'Jabatan' }}</div>
        <div class="status">{{ $employee->status ?? 'Status' }}</div>
        <div class="name">{{ $employee->name }}</div>
        <div class="qr">{!! $qrSvg !!}</div>
        <div class="nip">{{ $employee->nip }}</div>
    </div>
</body>
</html>
