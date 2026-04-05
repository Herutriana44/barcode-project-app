<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-egg-900 leading-tight">
            {{ __('Hasil Scan - Barcode Perusahaan') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-4xl mx-auto px-2 sm:px-4">
            <div class="bg-white overflow-hidden shadow-sm border border-egg-200 sm:rounded-lg p-3">
                <h3 class="font-semibold text-base text-egg-900 mb-2">{{ $companyBarcode->company->name }}</h3>

                <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead>
                        <tr class="border-b border-egg-200">
                            <th class="text-left py-1 px-1">Nama Barang</th>
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

                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="link-egg inline-flex items-center text-base lg:text-lg">← Scan lagi</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
