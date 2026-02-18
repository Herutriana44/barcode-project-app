<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Scan - Barcode Perusahaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
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

                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="text-blue-600 hover:underline">← Scan lagi</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
