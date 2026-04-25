<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ID Card — {{ $employee->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        @font-face {
            font-family: "DM Sans";
            src: url("/Fonts/DM_Sans/static/DMSans_18pt-Regular.ttf") format("truetype");
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: "Atkinson Hyperlegible";
            src: url("/Fonts/Atkinson_Hyperlegible/AtkinsonHyperlegible-Bold.ttf") format("truetype");
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        body {
            font-family: "DM Sans", ui-sans-serif, system-ui, sans-serif;
            background: #e8e4dc;
            padding: 12px;
        }
        @page {
            /* Mengikuti koordinat mm dari template: orientasi portrait (lebar 53mm, tinggi 90mm) */
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

        /* Background template image (opsional). Jika file ada di public/, akan ter-render. */
        .bg {
            position: absolute;
            inset: 0;
            background-image: url("/Tosca Modern Professional Store Manager ID Card_preview_rev_1.png");
            background-size: cover;
            background-position: center;
        }

        /* Foto lingkaran: 39.43mm, x 7.06mm, y 10.66mm */
        .photo {
            position: absolute;
            left: 7.06mm;
            top: 10.66mm;
            width: 39.43mm;
            height: 39.43mm;
            border-radius: 9999px;
            overflow: hidden;
            background: #d9d9d9;
        }
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        /* Text: Departemen label (DM Sans Reg) - x 5.28mm, y 61.95mm, w 21.49mm, h 2.57mm */
        .dept {
            position: absolute;
            left: 5.28mm;
            top: 61.95mm;
            width: 21.49mm;
            height: 2.57mm;
            font-family: "DM Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 7.5pt;
            line-height: 1;
            color: #0b3a3a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Text: Jabatan value (DM Sans Reg) - x 5mm, y 65.84mm, w 21.49mm, h 2.57mm */
        .jabatan {
            position: absolute;
            left: 5mm;
            top: 65.84mm;
            width: 21.49mm;
            height: 2.57mm;
            font-family: "DM Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 7.5pt;
            line-height: 1;
            color: #0b3a3a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Text: Nama Karyawan (Atkinson Bold) - x 5mm, y 69.6mm, w 28.36mm, h 11.19mm */
        .name {
            position: absolute;
            left: 5mm;
            top: 69.6mm;
            width: 28.36mm;
            height: 11.19mm;
            font-family: "Atkinson Hyperlegible", ui-sans-serif, system-ui, sans-serif;
            font-weight: 700;
            font-size: 12pt;
            line-height: 1.05;
            color: #0a2f2f;
            overflow: hidden;
        }

        /* QR / barcode square - x 35.13mm, y 68.98mm, w 11.02mm, h 11.02mm */
        .qr {
            position: absolute;
            left: 35.13mm;
            top: 68.98mm;
            width: 11.02mm;
            height: 11.02mm;
        }
        .qr svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Text: NIP - x 33.63mm, y 81.06mm, w 12.52mm, h 1.71mm */
        .nip {
            position: absolute;
            left: 33.63mm;
            top: 81.06mm;
            width: 12.52mm;
            height: 1.71mm;
            font-family: "DM Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            font-size: 6.5pt;
            line-height: 1;
            color: #0b3a3a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Fallback jika background template tidak tersedia */
        .fallback {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, #e2f6f4 0%, #f7fffe 55%, #ffffff 100%);
            opacity: 0.9;
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
        <div class="bg" aria-hidden="true"></div>
        <div class="fallback" aria-hidden="true"></div>

        <div class="photo">
            @if ($employee->photoPublicUrl())
                <img src="{{ $employee->photoPublicUrl() }}" alt="" />
            @else
                <div class="no-photo">Foto</div>
            @endif
        </div>

        <div class="dept">Departemen</div>
        <div class="jabatan">{{ $employee->jabatan ?? 'Jabatan' }}</div>
        <div class="name">{{ $employee->name }}</div>
        <div class="qr">{!! $qrSvg !!}</div>
        <div class="nip">NIP : {{ $employee->nip }}</div>
    </div>
</body>
</html>
