<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Barcode Perusahaan') }}
            </h2>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Print</button>
                <a href="{{ route('company-barcodes.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">Buat Baru</a>
                <a href="{{ route('company-barcodes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 print:shadow-none">
                <div class="border-2 border-gray-200 p-6 rounded-lg">
                    <div class="flex justify-center mb-4">
                        <div class="barcode-container">
                            {!! $barcodeSvg !!}
                        </div>
                    </div>
                    <p class="text-center text-sm font-mono mb-6">{{ $companyBarcode->barcode_id }}</p>

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
