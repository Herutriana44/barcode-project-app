@php
    $labelQtyPcs = $labelQtyPcs ?? null;
    $quantityUseStatic = $quantityUseStatic ?? false;
    $item = $itemBarcode->item;
    $companyName = $headerCompanyName ?? ($item->company->name ?? '—');
    
    $beratGram = $item->berat !== null ? (float) $item->berat : 0;
    $beratKg = $beratGram / 1000;
    
    $totalBeratLabel = $beratKg * ($labelQtyPcs ?? 1);
    $beratStr = number_format($totalBeratLabel, 3, '.', '');
    
    if ($quantityUseStatic) {
        $qtyStr = $item->static_qty !== null ? (string) (int) $item->static_qty : '';
        $qtySuffix = $qtyStr !== '' ? ' Pcs' : '';
    } elseif ($labelQtyPcs !== null) {
        $qtyStr = (int) $labelQtyPcs > 0 ? (string) (int) $labelQtyPcs : '';
        $qtySuffix = $qtyStr !== '' ? ' Pcs' : '';
    } else {
        $qtyStr = $item->static_qty !== null ? (string) $item->static_qty : '';
        $qtySuffix = $qtyStr !== '' ? ' Pcs' : '';
    }
@endphp
<article class="label-card" style="font-size: 8pt; width: 95mm; padding: 2mm;">
    <table class="label-table" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="3" style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 1mm;">{{ $companyName }}</td>
        </tr>
        <tr>
            <td style="width: 60%; vertical-align: top; padding-top: 1mm;">
                <table style="width: 100%;">
                    <tr><td style="width: 25mm;">Cust</td><td>: {{ $item->customer ?? '' }}</td></tr>
                    <tr><td>Part No</td><td>: {{ $item->part_number ?? '' }}</td></tr>
                    <tr><td>Part Nm</td><td>: {{ $item->part_name ?? '' }}</td></tr>
                    <tr><td>Model</td><td>: {{ $item->model !== null && $item->model !== '' ? $item->model : '-' }}</td></tr>
                    <tr><td>Berat</td><td>: {{ $beratStr }} Kg</td></tr>
                    <tr><td>Qty</td><td>: {{ $qtyStr }}{{ $qtySuffix }}</td></tr>
                </table>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: center;">
                <div style="font-weight:bold;">{{ $item->code ?? '' }}</div>
                <div class="[&_svg]:h-10">{!! $qrSvg !!}</div>
            </td>
        </tr>
        <tr style="border-top: 1px solid #000;">
            <td colspan="2" style="padding-top: 1mm; font-size: 7pt;">
                Prod: {{ $item->tgl_produksi?->format('d/m/y') ?? '-' }} | Exp: {{ $item->tgl_expired?->format('d/m/y') ?? '-' }} | Status: OK
            </td>
        </tr>
    </table>
</article>
