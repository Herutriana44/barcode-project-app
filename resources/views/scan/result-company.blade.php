<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Hasil Scan - Barcode Perusahaan') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-5xl mx-auto w-full">
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <h3 class="font-bold text-2xl text-egg-900 mb-6">{{ $companyBarcode->company->name }}</h3>

                <div class="overflow-x-auto">
                <table class="min-w-full text-base">
                    <thead>
                        <tr class="border-b border-egg-200">
                            <th class="text-left py-3 px-2 font-semibold">Nama Barang</th>
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

                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="link-egg inline-flex items-center text-base lg:text-lg">← Scan lagi</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
