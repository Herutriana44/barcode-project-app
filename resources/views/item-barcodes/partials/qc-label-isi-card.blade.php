@php
    $item = $itemBarcode->item;
    $companyName = $item->company->name ?? '—';
    $partNo = $item->part_number ?? '';
    $partName = $item->part_name ?? '';
    $checker = $item->checker_name ?? '';
    $prod = $item->tgl_produksi?->format('d/m/Y') ?? '';

    $qtyInPack = (int) ($qtyInPack ?? 1);
    $qtyStr = $qtyInPack > 0 ? (string) $qtyInPack : '';

    $beratGram = null;
    $bPack = $item->berat_packaging_gram;
    $bPcs = $item->berat_per_pcs_gram;
    if ($bPack !== null || $bPcs !== null) {
        $beratGram = (int) ($bPack ?? 0) + ((int) ($bPcs ?? 0) * max(0, $qtyInPack));
    } elseif ($item->berat !== null) {
        // fallback: gunakan berat (Kg) jika hanya itu yang tersedia
        $beratGram = (int) round(((float) $item->berat) * 1000);
    }
    $beratStr = $beratGram !== null ? number_format($beratGram, 0, ',', '.') : '';
@endphp

<article class="isi-card">
    <div class="isi-top">
        <div class="isi-no">{{ $itemBarcode->id }}</div>
        <!-- <div class="isi-company">{{ strtoupper($companyName) }}</div> -->
         <div class="isi-company">PT. TASM</div>
    </div>

    <div class="isi-body">
        <div class="isi-fields-top">
            <div class="row"><span class="k">Part No</span><span class="sep">:</span><span class="v">{{ $partNo }}</span></div>
            <div class="row"><span class="k">Part Name</span><span class="sep">:</span><span class="v">{{ $partName }}</span></div>
        </div>
        <div class="isi-fields">
            <div class="row"><span class="k">QTY</span><span class="sep">:</span><span class="v">{{ $qtyStr }}{{ $qtyStr !== '' ? ' Pcs' : '' }}</span></div>
            <div class="row"><span class="k">Berat</span><span class="sep">:</span><span class="v">{{ $beratStr }}{{ $beratStr !== '' ? ' gram' : '' }}</span></div>
            <div class="row"><span class="k">Tgl prod</span><span class="sep">:</span><span class="v">{{ $prod }}</span></div>
            <div class="row"><span class="k">Checker</span><span class="sep">:</span><span class="v">{{ $checker }}</span></div>
        </div>

        <div class="isi-barcode-box">
            <div class="isi-barcode-wrap">
                {!! $qrSvg !!}
            </div>
        </div>
    </div>
</article>

