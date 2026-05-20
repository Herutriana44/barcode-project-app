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

        <div class="isi-barcode-box">
            <div class="isi-barcode-wrap">
                {!! $qrSvg !!}
            </div>
        </div>
    </div>
</article>

