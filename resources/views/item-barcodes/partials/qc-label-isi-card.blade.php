@php
    $item = $itemBarcode->item;
    $companyName = $item->company->name ?? '—';
    $partNo = $item->part_number ?? '';
    $partName = $item->part_name ?? '';
    $checker = $item->checker_name ?? '';
    $prod = $item->tgl_produksi?->format('d/m/Y') ?? '';

    // Ambil berat per pcs dalam gram sebagai float
    $beratPcsGram = $item->berat_per_pcs_gram !== null ? (float) $item->berat_per_pcs_gram : 0.0;
    $beratStr = number_format($beratPcsGram, 2, '.', '');

    $qtyInPack = (int) ($item->qty_sub_pack ?? 0);
    $qtyStr = $qtyInPack > 0 ? (string) (int) $qtyInPack : '';
@endphp

<article class="isi-card">
    <div class="isi-top">
        <div class="isi-no">{{ $itemBarcode->id }}</div>
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
