<style>
    /* Label QC kecil (mm) — dipakai halaman cetak massal & detail barang */
    :root {
        --label-w: 84mm;
        --label-min-h: 57.6mm;
        --label-border: 0.25mm solid #000;
        --label-pad: 0.6mm;
        --font-label: Arial, Helvetica, "Liberation Sans", sans-serif;
    }

    .qc-label-font-root {
        font-family: var(--font-label);
        color: #000;
    }

    .qc-label-detail-wrap {
        display: flex;
        justify-content: center;
        width: 100%;
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
        padding: 0.75rem;
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
        grid-auto-rows: auto;
        align-items: start;
        gap: 1.8mm 2.4mm;
        justify-content: center;
        padding-bottom: 1rem;
    }

    .label-card {
        box-sizing: border-box;
        width: var(--label-w);
        min-height: var(--label-min-h);
        height: auto;
        background: #fff;
        border: var(--label-border);
        padding: var(--label-pad);
        display: flex;
        flex-direction: column;
        overflow: visible;
    }

    .label-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        flex: 0 0 auto;
        font-size: 6pt;
        line-height: 1.1;
    }

    .label-table col.c-logo { width: 16%; }
    .label-table col.c-mid { width: 52%; }
    .label-table col.c-side { width: 32%; }

    .label-table td {
        border: var(--label-border);
        vertical-align: top;
        padding: 0.36mm 0.6mm;
        word-wrap: break-word;
    }

    .label-logo-cell {
        text-align: center;
        vertical-align: middle;
        padding: 0.24mm !important;
    }

    .label-logo-img {
        display: block;
        margin: 0 auto;
        max-width: 14.4mm;
        max-height: 13.2mm;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .label-company {
        text-align: center;
        vertical-align: middle;
        font-weight: 700;
        font-size: 6pt;
        text-transform: uppercase;
        padding: 0.6mm 1.2mm !important;
    }

    .label-company {
        padding: 0 !important; /* Hapus padding td agar tidak tabrakan */
        height: 48px; /* Sesuai kebutuhan, atau biarkan mengikuti tinggi baris */
    }

    .inner-label {
        display: flex;
        justify-content: center; /* Center Horizontal */
        align-items: center;     /* Center Vertikal */
        height: 100%;            /* Penuhi seluruh tinggi td */
        width: 100%;             /* Penuhi seluruh lebar td */
        
        /* Pindahkan styling teks ke sini */
        font-weight: 700;
        font-size: 9.6pt;
        text-transform: uppercase;
        padding: 0.6mm 1.2mm !important; 
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
        padding: 0.36mm 0.6mm;
        font-size: 6pt;
    }

    .label-fields-inner tr:last-child td {
        border-bottom: none;
    }

    .side-stack td {
        border: none;
        border-bottom: var(--label-border);
        vertical-align: top;
        padding: 0.36mm 0.6mm;
    }

    .side-stack tr:last-child td {
        border-bottom: none;
    }

    .side-hdr {
        font-size: 4.8pt;
        font-weight: 600;
    }

    .code-big {
        display: block;
        text-align: center;
        font-size: 12pt;
        font-weight: 800;
        line-height: 1;
        padding: 0.24mm 0;
    }

    .qr-slot {
        text-align: center;
        vertical-align: middle !important;
    }

    .qr-slot svg {
        display: block;
        margin: 0 auto;
        max-width: 21.6mm;
        max-height: 21.6mm;
        width: 100%;
        height: auto;
    }

    .label-footer td {
        text-align: center;
        vertical-align: middle;
        font-size: 6pt;
        padding: 0.6mm 0.6mm !important;
    }

    .label-footer .f-h {
        font-weight: 600;
        display: block;
        margin-bottom: 0.12mm;
    }

    .label-footer .status-ok {
        font-size: 9.6pt;
        font-weight: 800;
        line-height: 1;
    }

    .label-doc-rev {
        font-size: 4.8pt;
        color: #222;
        margin-top: 0.36mm;
        padding-left: 0.36mm;
        padding-bottom: 0.12mm;
        flex-shrink: 0;
        line-height: 1;
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
            gap: 1.8mm 2.4mm;
            padding-bottom: 0;
        }

        .label-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .qc-label-detail-wrap {
            justify-content: center;
            padding: 0;
        }

        .qc-label-detail-outer {
            max-width: none !important;
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .label-logo-img {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    @media screen and (max-width: 900px) {
        .label-grid {
            grid-template-columns: 1fr;
            justify-items: center;
        }
    }
</style>
