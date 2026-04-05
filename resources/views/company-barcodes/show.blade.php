<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-egg-900 leading-tight">
                {{ __('Detail Barcode Perusahaan') }}
            </h2>
            <div class="flex flex-wrap gap-2 justify-end">
                <button type="button" onclick="window.print()" class="btn-egg-primary">Print</button>
                <a href="{{ route('company-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
                <a href="{{ route('company-barcodes.index') }}" class="btn-egg-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-4xl mx-auto px-2 sm:px-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2 print:shadow-none">
                <div class="border border-egg-200 p-2 rounded-lg">
                    <div class="flex flex-col sm:flex-row flex-wrap items-start justify-center gap-4 mb-3 print:flex-row print:gap-4">
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-xs font-medium text-egg-700 mb-2 uppercase tracking-wide">Barcode (Code 128)</span>
                            <div class="barcode-container overflow-x-auto max-w-full">
                                {!! $barcodeSvg !!}
                            </div>
                        </div>
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-xs font-medium text-egg-700 mb-2 uppercase tracking-wide">Kode QR</span>
                            <div class="qr-container w-[180px] max-w-full flex justify-center [&_svg]:max-w-full [&_svg]:h-auto">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-sm font-mono mb-3 font-medium text-egg-900">{{ $companyBarcode->barcode_id }}</p>

                    <h3 class="font-semibold text-base mb-2">{{ $companyBarcode->company->name }}</h3>

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr class="border-b border-egg-200">
                                <th class="text-left py-1 px-1">Nama</th>
                                <th class="text-left py-1 px-1">Qty</th>
                                <th class="text-left py-1 px-1">Rak</th>
                                <th class="text-left py-1 px-1">Tingkat</th>
                                <th class="text-left py-1 px-1">Op. mobil</th>
                                <th class="text-left py-1 px-1">Pengirim</th>
                                <th class="text-left py-1 px-1">Op. forklift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companyBarcode->company->companyItems as $ci)
                                <tr class="border-b border-egg-100">
                                    <td class="py-1 px-1">{{ $ci->item->part_name ?? $ci->item->part_number ?? 'Item #'.$ci->item->id }}</td>
                                    <td class="py-1 px-1">{{ $ci->qty }}</td>
                                    <td class="py-1 px-1">{{ $ci->posisi_rak ?? '-' }}</td>
                                    <td class="py-1 px-1">{{ $ci->tingkat ?? '-' }}</td>
                                    <td class="py-1 px-1">{{ $ci->item->operatorMobil->name ?? '-' }}</td>
                                    <td class="py-1 px-1">{{ $ci->item->pengirim->name ?? '-' }}</td>
                                    <td class="py-1 px-1">{{ $ci->item->operatorForklift->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
