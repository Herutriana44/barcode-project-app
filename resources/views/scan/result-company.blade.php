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
                                <th class="text-left py-3 px-2 font-semibold">Company</th>
                                <th class="text-left py-3 px-2 font-semibold">Part Name</th>
                                <th class="text-left py-3 px-2 font-semibold">Part Number</th>
                                <th class="text-left py-3 px-2 font-semibold">Model</th>
                                <th class="text-left py-3 px-2 font-semibold">Berat (Kg)</th>
                                <th class="text-left py-3 px-2 font-semibold">Qty</th>
                                <th class="text-left py-3 px-2 font-semibold">Posisi Rak</th>
                                <th class="text-left py-3 px-2 font-semibold">Tingkat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companyBarcode->company->items as $item)
                                <tr class="border-b border-egg-100">
                                    <td class="py-3 px-2">{{ $item->customer ?? '-' }}</td>
                                    <td class="py-3 px-2">
                                        @if($item->itemBarcodes->isNotEmpty())
                                            <a href="{{ route('scan.show', $item->itemBarcodes->first()->barcode_id) }}" class="text-blue-600 hover:underline">{{ $item->part_name ?? '-' }}</a>
                                        @else
                                            {{ $item->part_name ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-2">{{ $item->part_number ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $item->model ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $item->berat !== null ? $item->berat : '-' }}</td>
                                    <td class="py-3 px-2">{{ $item->dynamic_qty ?? $item->qty ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $item->posisi_rak ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $item->tingkat ?? '-' }}</td>
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
