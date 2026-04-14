<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label barang — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Label kecil: dimensi dalam mm agar konsisten saat Print → Simpan sebagai PDF */
        :root {
            --label-w: 96mm;
            --label-h: 56mm;
            --label-border: 0.35mm solid #000;
            --label-pad: 1.2mm;
            --font-label: Arial, Helvetica, "Liberation Sans", sans-serif;
        }

        body.labels-print-root {
            font-family: var(--font-label);
            color: #000;
            background: #e8e8e8;
            margin: 0;
            padding: 0.5rem;
        }

        .labels-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
            margin: -0.5rem -0.5rem 0.75rem -0.5rem;
        }

        .labels-toolbar p {
            margin: 0;
            font-size: 0.8125rem;
            color: #374151;
            max-width: 36rem;
        }

        .label-grid {
            display: grid;
            grid-template-columns: repeat(2, var(--label-w));
            grid-auto-rows: var(--label-h);
            gap: 3mm 4mm;
            justify-content: center;
            padding-bottom: 1rem;
        }

        .label-card {
            box-sizing: border-box;
            width: var(--label-w);
            height: var(--label-h);
            background: #fff;
            border: var(--label-border);
            padding: var(--label-pad);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .label-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            flex: 1;
            font-size: 5.5pt;
            line-height: 1.2;
        }

        .label-table col.c-logo { width: 18%; }
        .label-table col.c-mid { width: 50%; }
        .label-table col.c-side { width: 32%; }

        .label-table td {
            border: var(--label-border);
            vertical-align: top;
            padding: 0.8mm 1mm;
            word-wrap: break-word;
        }

        .label-logo-cell {
            text-align: center;
            vertical-align: middle;
        }

        .logo-tasm {
            font-weight: 800;
            font-size: 8pt;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .logo-tasm .r { color: #c00; }
        .logo-tasm .b { color: #039; }

        .logo-sub {
            font-size: 4pt;
            color: #333;
            margin-top: 0.5mm;
            line-height: 1.1;
        }

        .label-company {
            text-align: center;
            vertical-align: middle;
            font-weight: 700;
            font-size: 7pt;
            text-transform: uppercase;
            padding: 1.5mm 2mm !important;
        }

        .label-fields-cell {
            padding: 0 !important;
        }

        .label-fields-inner {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .label-fields-inner td {
            border: none;
            border-bottom: var(--label-border);
            padding: 0.6mm 1mm;
            font-size: 5.5pt;
        }

        .label-fields-inner tr:last-child td {
            border-bottom: none;
        }

        .fld-lbl {
            width: 34%;
            font-weight: 600;
            white-space: nowrap;
        }

        .label-side-cell {
            padding: 0 !important;
        }

        .side-stack {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .side-stack td {
            border: none;
            border-bottom: var(--label-border);
            vertical-align: top;
            padding: 0.6mm 1mm;
        }

        .side-stack tr:last-child td {
            border-bottom: none;
        }

        .side-hdr {
            font-size: 5pt;
            font-weight: 600;
        }

        .code-big {
            display: block;
            text-align: center;
            font-size: 16pt;
            font-weight: 800;
            line-height: 1.05;
            padding: 0.5mm 0;
        }

        .qr-slot {
            text-align: center;
            vertical-align: middle !important;
        }

        .qr-slot svg {
            display: block;
            margin: 0 auto;
            max-width: 22mm;
            max-height: 22mm;
            width: 100%;
            height: auto;
        }

        .label-footer td {
            text-align: center;
            vertical-align: middle;
            font-size: 5.5pt;
            padding: 0.8mm 1mm !important;
        }

        .label-footer .f-h {
            font-weight: 600;
            display: block;
            margin-bottom: 0.3mm;
        }

        .label-footer .status-ok {
            font-size: 12pt;
            font-weight: 800;
        }

        .label-doc-rev {
            font-size: 4.5pt;
            color: #222;
            margin-top: 0.6mm;
            padding-left: 0.5mm;
            flex-shrink: 0;
        }

        .labels-empty {
            text-align: center;
            padding: 2rem;
            color: #555;
        }

        @media print {
            body.labels-print-root {
                background: #fff !important;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 5mm;
            }

            .label-grid {
                gap: 2.5mm 3mm;
                padding-bottom: 0;
            }

            .label-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        @media screen and (max-width: 900px) {
            .label-grid {
                grid-template-columns: 1fr;
                justify-items: center;
            }
        }
    </style>
</head>
<body class="labels-print-root font-sans antialiased text-base">
    <div class="labels-toolbar no-print">
        <button type="button" onclick="window.print()" class="btn-egg-primary py-1 px-3 text-xs min-h-0">Cetak / Simpan PDF</button>
        <a href="{{ route('item-barcodes.index') }}" class="btn-egg-secondary py-1 px-3 text-xs min-h-0">Kembali</a>
        <p>Semua label barang dalam satu halaman. Di dialog cetak pilih <strong>Simpan sebagai PDF</strong> dan aktifkan margin default; satu halaman A4 memuat beberapa label kecil.</p>
    </div>

    <div class="label-grid">
        @forelse($rows as $row)
            @php
                $ib = $row['itemBarcode'];
                $item = $ib->item;
                $companyName = $item->company->name ?? '—';
                $beratStr = $item->berat !== null ? number_format((float) $item->berat, 2, '.', '') : '';
                $qtyStr = $item->qty !== null ? (string) $item->qty : '';
            @endphp
            <article class="label-card">
                <table class="label-table">
                    <colgroup>
                        <col class="c-logo" />
                        <col class="c-mid" />
                        <col class="c-side" />
                    </colgroup>
                    <tr>
                        <td class="label-logo-cell">
                            <div class="logo-tasm"><span class="r">T</span><span class="b">ASM</span></div>
                            <div class="logo-sub">a Noguchi, Ltd. Group</div>
                        </td>
                        <td class="label-company" colspan="2">{{ $companyName }}</td>
                    </tr>
                    <tr>
                        <td class="label-fields-cell" colspan="2">
                            <table class="label-fields-inner">
                                <tr>
                                    <td class="fld-lbl">Customer name</td>
                                    <td>: {{ $item->customer ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="fld-lbl">Part no</td>
                                    <td>: {{ $item->part_number ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="fld-lbl">Part name</td>
                                    <td>: {{ $item->part_name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="fld-lbl">Model</td>
                                    <td>: {{ $item->model !== null && $item->model !== '' ? $item->model : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fld-lbl">Delivery date</td>
                                    <td>: </td>
                                </tr>
                                <tr>
                                    <td class="fld-lbl">Berat per pack</td>
                                    <td>: {{ $beratStr }}{{ $beratStr !== '' ? ' ' : '' }}Kg</td>
                                </tr>
                                <tr>
                                    <td class="fld-lbl">Quantity</td>
                                    <td>: {{ $qtyStr }}{{ $qtyStr !== '' ? ' Pcs' : '' }}</td>
                                </tr>
                                <tr>
                                    <td class="fld-lbl">Inspector name</td>
                                    <td>: {{ $item->inspector_name ?? '' }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="label-side-cell">
                            <table class="side-stack">
                                <tr>
                                    <td>
                                        <span class="side-hdr">Code :</span>
                                        <span class="code-big">{{ $item->code ?? '' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="qr-slot">
                                        <div class="side-hdr" style="text-align:left;margin-bottom:0.5mm;">QR code</div>
                                        {!! $row['qrSvg'] !!}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="label-footer">
                        <td>
                            <span class="f-h">Prod. date</span>
                            {{ $item->tgl_produksi?->format('d/m/Y') ?? '' }}
                        </td>
                        <td>
                            <span class="f-h">Exp. date</span>
                            {{ $item->tgl_expired?->format('d/m/Y') ?? '' }}
                        </td>
                        <td>
                            <span class="f-h">Status part</span>
                            <span class="status-ok">OK</span>
                        </td>
                    </tr>
                </table>
                <div class="label-doc-rev">F-QC-038. Date/Rev : 22.04.24/01</div>
            </article>
        @empty
            <p class="labels-empty">Belum ada barcode barang.</p>
        @endforelse
    </div>
</body>
</html>
