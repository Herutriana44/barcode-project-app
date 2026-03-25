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

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 print:shadow-none">
                <div class="border-2 border-egg-200 p-6 rounded-lg">
                    <div class="flex flex-col sm:flex-row flex-wrap items-start justify-center gap-10 mb-6 print:flex-row print:gap-8">
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
                    <p class="text-center text-base font-mono mb-6 font-medium text-egg-900">{{ $companyBarcode->barcode_id }}</p>

                    <h3 class="font-semibold text-lg mb-4">{{ $companyBarcode->company->name }}</h3>

                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Nama Barang</th>
                                <th class="text-left py-2">Qty</th>
                                <th class="text-left py-2">Posisi Rak</th>
                                <th class="text-left py-2">Tingkat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companyBarcode->company->companyItems as $ci)
                                <tr class="border-b">
                                    <td class="py-2">{{ $ci->item->part_name ?? $ci->item->part_number ?? 'Item #'.$ci->item->id }}</td>
                                    <td class="py-2">{{ $ci->qty }}</td>
                                    <td class="py-2">{{ $ci->posisi_rak ?? '-' }}</td>
                                    <td class="py-2">{{ $ci->tingkat ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
