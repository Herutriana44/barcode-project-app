<style>
    /* Label QC kecil (mm) — dipakai halaman cetak massal & detail barang */
    :root {
        --label-w: 95mm;
        --label-min-h: 48mm;
        --label-border: 0.25mm solid #000;
        --label-pad: 0.5mm;
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
        gap: 1.5mm 2mm;
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
        font-size: 5pt;
        line-height: 1.1;
    }

    .label-table col.c-logo { width: 16%; }
    .label-table col.c-mid { width: 52%; }
    .label-table col.c-side { width: 32%; }

    .label-table td {
        border: var(--label-border);
        vertical-align: top;
        padding: 0.3mm 0.5mm;
        word-wrap: break-word;
    }

    .label-logo-cell {
        text-align: center;
        vertical-align: middle;
        padding: 0.2mm !important;
    }

    .label-logo-img {
        display: block;
        margin: 0 auto;
        max-width: 12mm;
        max-height: 11mm;
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
        padding: 0.5mm 1mm !important;
    }

    .label-company {
        padding: 0 !important; /* Hapus padding td agar tidak tabrakan */
        height: 40px; /* Sesuai kebutuhan, atau biarkan mengikuti tinggi baris */
    }

    .inner-label {
        display: flex;
        justify-content: center; /* Center Horizontal */
        align-items: center;     /* Center Vertikal */
        height: 100%;            /* Penuhi seluruh tinggi td */
        width: 100%;             /* Penuhi seluruh lebar td */
        
        /* Pindahkan styling teks ke sini */
        font-weight: 700;
        font-size: 6pt;
        text-transform: uppercase;
        padding: 0.5mm 1mm !important; 
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
        padding: 0.3mm 0.5mm;
        font-size: 5pt;
    }

    .label-fields-inner tr:last-child td {
        border-bottom: none;
    }

    .side-stack td {
        border: none;
        border-bottom: var(--label-border);
        vertical-align: top;
        padding: 0.3mm 0.5mm;
    }

    .side-stack tr:last-child td {
        border-bottom: none;
    }

    .side-hdr {
        font-size: 4pt;
        font-weight: 600;
    }

    .code-big {
        display: block;
        text-align: center;
        font-size: 10pt;
        font-weight: 800;
        line-height: 1;
        padding: 0.2mm 0;
    }

    .qr-slot {
        text-align: center;
        vertical-align: middle !important;
    }

    .qr-slot svg {
        display: block;
        margin: 0 auto;
        max-width: 18mm;
        max-height: 18mm;
        width: 100%;
        height: auto;
    }

    .label-footer td {
        text-align: center;
        vertical-align: middle;
        font-size: 5pt;
        padding: 0.5mm 0.5mm !important;
    }

    .label-footer .f-h {
        font-weight: 600;
        display: block;
        margin-bottom: 0.1mm;
    }

    .label-footer .status-ok {
        font-size: 8pt;
        font-weight: 800;
        line-height: 1;
    }

    .label-doc-rev {
        font-size: 4pt;
        color: #222;
        margin-top: 0.3mm;
        padding-left: 0.3mm;
        padding-bottom: 0.1mm;
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
            gap: 1.5mm 2mm;
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
