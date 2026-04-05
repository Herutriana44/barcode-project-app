<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Detail Barcode Perusahaan') }}
            </h2>
            <div class="flex flex-wrap gap-2 justify-end">
                <button type="button" onclick="window.print()" class="btn-egg-primary">Print</button>
                <a href="{{ route('company-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
                <a href="{{ route('company-barcodes.index') }}" class="btn-egg-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-5xl mx-auto w-full">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl p-6 print:shadow-none">
                <div class="border border-egg-200 p-6 rounded-xl">
                    <div class="flex flex-col sm:flex-row flex-wrap items-start justify-center gap-8 mb-6 print:flex-row print:gap-6">
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Barcode (Code 128)</span>
                            <div class="barcode-container overflow-x-auto max-w-full">
                                {!! $barcodeSvg !!}
                            </div>
                        </div>
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Kode QR</span>
                            <div class="qr-container w-[220px] max-w-full flex justify-center [&_svg]:max-w-full [&_svg]:h-auto">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-lg font-mono mb-4 font-medium text-egg-900">{{ $companyBarcode->barcode_id }}</p>

                    <h3 class="font-bold text-2xl mb-4">{{ $companyBarcode->company->name }}</h3>

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-base">
                        <thead>
                            <tr class="border-b border-egg-200">
                                <th class="text-left py-3 px-2 font-semibold">Nama</th>
                                <th class="text-left py-3 px-2 font-semibold">Qty</th>
                                <th class="text-left py-3 px-2 font-semibold">Rak</th>
                                <th class="text-left py-3 px-2 font-semibold">Tingkat</th>
                                <th class="text-left py-3 px-2 font-semibold">Op. mobil</th>
                                <th class="text-left py-3 px-2 font-semibold">Pengirim</th>
                                <th class="text-left py-3 px-2 font-semibold">Op. forklift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companyBarcode->company->companyItems as $ci)
                                <tr class="border-b border-egg-100">
                                    <td class="py-3 px-2">{{ $ci->item->part_name ?? $ci->item->part_number ?? 'Item #'.$ci->item->id }}</td>
                                    <td class="py-3 px-2">{{ $ci->qty }}</td>
                                    <td class="py-3 px-2">{{ $ci->posisi_rak ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->tingkat ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->item->operatorMobil->name ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->item->pengirim->name ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->item->operatorForklift->name ?? '-' }}</td>
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
