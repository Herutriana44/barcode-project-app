
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ID Card — {{ $employee->name }}</title>

    @php
        function formatText($text, $limit = 18)
        {
            $text = $text ?? '-';

            if (mb_strlen($text) > $limit) {
                $splitPos = mb_strrpos(mb_substr($text, 0, $limit), ' ');

                if ($splitPos !== false) {
                    $text =
                        mb_substr($text, 0, $splitPos) .
                        "\n" .
                        mb_substr($text, $splitPos + 1);
                } else {
                    $text =
                        mb_substr($text, 0, $limit) .
                        "\n" .
                        mb_substr($text, $limit);
                }
            }

            return $text;
        }

        function fontSize($text, $default = '4.5pt')
        {
            return mb_strlen($text) > 18 ? '3.8pt' : $default;
        }
    @endphp

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

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
            body {
                background: #fff;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        .card {
            width: 59mm;
            height: 96mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border: 0;
        }

        .bg-img {
            position: absolute;
            inset: 0;
            width: 102%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
        }

        .photo {
            position: absolute;
            left: 2.1mm;
            top: 30mm;
            width: 22.3mm;
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

        .text-field {
            position: absolute;
            left: 34.5mm;
            width: 20mm;
            height: 4.5mm; /* tinggi tetap */
            overflow: hidden; /* text tidak mendorong element lain */

            font-family: "Canva Sans", ui-sans-serif, system-ui, sans-serif;
            font-weight: 400;
            line-height: 1.05;
            color: #000000;

            white-space: pre-line;
            word-break: break-word;

            display: block;
            z-index: 3;
        }

        .dept {
            top: 32mm;
        }

        .jabatan {
            top: 39mm;
        }

        .status {
            top: 46mm;
        }

        .name {
            top: 53mm;
        }

        .nip {
            top: 60mm;
        }

        .qr {
            position: absolute;
            left: 38mm;
            top: 76mm;
            width: 17mm;
            height: 17mm;
            z-index: 3;
        }

        .qr svg {
            width: 100%;
            height: 100%;
            display: block;
        }

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    function downloadJpg() {
        const card = document.querySelector('.card');
        html2canvas(card, {
            scale: 2, // Meningkatkan resolusi agar hasil tidak pecah
            useCORS: true
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'ID-Card-{{ $employee->nip }}.jpg';
            link.href = canvas.toDataURL('image/jpeg', 1.0);
            link.click();
        });
    }
</script>

<div class="toolbar no-print">
    <button type="button" onclick="window.print()">
        Cetak (9 × 5,3 cm)
    </button>
    <button type="button" onclick="downloadJpg()">
        Download JPG
    </button>
</div>

<div class="card">
    <div class="fallback" aria-hidden="true"></div>

    <img
        class="bg-img"
        src="{{ asset(str_replace(' ', '%20', 'DESAIN 2.png')) }}"
        alt=""
        aria-hidden="true"
    />

    <div class="photo">
        @if ($employee->photoPublicUrl())
            <img src="{{ $employee->photoPublicUrl() }}" alt="" />
        @else
            <div class="no-photo">Foto</div>
        @endif
    </div>

    <div
        class="text-field dept"
        style="font-size: {{ fontSize($employee->departemen ?? '') }};"
    >
        {{ formatText($employee->departemen ?? 'Departemen') }}
    </div>

    <div
        class="text-field jabatan"
        style="font-size: {{ fontSize($employee->jabatan ?? '') }};"
    >
        {{ formatText($employee->jabatan ?? 'Jabatan') }}
    </div>

    <div
        class="text-field status"
        style="font-size: {{ fontSize($employee->status ?? '') }};"
    >
        {{ formatText($employee->status ?? 'Status') }}
    </div>

    <div
        class="text-field name"
        style="font-size: {{ fontSize($employee->name ?? '') }};"
    >
        {{ formatText($employee->name ?? 'Nama') }}
    </div>

    <div
        class="text-field nip"
        style="font-size: {{ fontSize($employee->nip ?? '') }};"
    >
        {{ formatText($employee->nip ?? '-') }}
    </div>

    <div class="qr">
        {!! $qrSvg !!}
    </div>
</div>

</body>
</html>