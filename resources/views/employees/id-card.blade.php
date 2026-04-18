<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ID Card — {{ $employee->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, sans-serif;
            background: #e8e4dc;
            padding: 12px;
        }
        @page {
            size: 90mm 53mm;
            margin: 0;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
        }
        .card {
            width: 90mm;
            height: 53mm;
            background: linear-gradient(135deg, #faf8f4 0%, #f0ebe3 100%);
            border: 1px solid #c4b8a8;
            border-radius: 3mm;
            display: flex;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .card-photo {
            width: 22mm;
            flex-shrink: 0;
            background: #ddd5c8;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #c4b8a8;
        }
        .card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card-body {
            flex: 1;
            padding: 3mm 3.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }
        .org {
            font-size: 7pt;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #5c4f3d;
            text-transform: uppercase;
        }
        .name {
            font-size: 11pt;
            font-weight: 800;
            color: #2d261c;
            line-height: 1.15;
            margin-top: 1mm;
        }
        .nip {
            font-size: 8pt;
            color: #4a4338;
            margin-top: 0.5mm;
        }
        .jabatan {
            font-size: 8pt;
            color: #6b5d4a;
            margin-top: 1mm;
        }
        .codes {
            display: flex;
            align-items: flex-end;
            gap: 2mm;
            margin-top: 1mm;
        }
        .codes svg { display: block; max-height: 14mm; width: auto; }
        .qr-wrap svg { max-height: 14mm; width: auto; }
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
        <div class="card-photo">
            @if ($employee->photoPublicUrl())
                <img src="{{ $employee->photoPublicUrl() }}" alt="" />
            @else
                <span style="font-size:6pt;color:#7a6f62;text-align:center;padding:2px;">No photo</span>
            @endif
        </div>
        <div class="card-body">
            <div>
                <div class="org">ID Karyawan</div>
                <div class="name">{{ $employee->name }}</div>
                <div class="nip">NIP: {{ $employee->nip }}</div>
                @if ($employee->jabatan)
                    <div class="jabatan">{{ $employee->jabatan }}</div>
                @endif
            </div>
            <div class="codes">
                <div class="qr-wrap">{!! $qrSvg !!}</div>
                <div style="flex:1;min-width:0;overflow:hidden;">{!! $barcodeSvg !!}</div>
            </div>
        </div>
    </div>
</body>
</html>
