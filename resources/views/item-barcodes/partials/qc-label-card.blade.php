@php
    $item = $itemBarcode->item;
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
                <img src="{{ asset('icon.png') }}" alt="" class="label-logo-img" />
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
                            <div class="side-hdr" style="text-align:left;margin-bottom:0.5mm;">QR (URL scan)</div>
                            {!! $qrSvg !!}
                        </td>
                    </tr>
                    @if(! empty($barcodeSvg))
                    <tr>
                        <td class="barcode-slot-mini overflow-x-auto max-w-full">
                            <div class="side-hdr" style="text-align:left;margin-bottom:0.5mm;">Barcode (URL)</div>
                            <div class="[&_svg]:max-w-full [&_svg]:h-auto">{!! $barcodeSvg !!}</div>
                        </td>
                    </tr>
                    @endif
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
