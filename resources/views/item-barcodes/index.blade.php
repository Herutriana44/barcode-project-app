<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-egg-900 leading-tight">
                {{ __('Barcode Barang') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('item-barcodes.labels') }}" class="btn-egg-secondary" target="_blank" rel="noopener">Label cetak</a>
                <a href="{{ route('item-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto px-2 sm:px-4">
            <p class="text-xs text-egg-700 mb-2">Urutan: <strong>FIFO</strong> (terima FG lebih dulu di atas).</p>
            <div class="bg-white overflow-hidden shadow-sm border border-egg-200 sm:rounded-lg">
                <div class="p-2">
                    <table class="min-w-full divide-y divide-egg-200 text-xs">
                        <thead>
                            <tr>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Barcode ID</th>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Part Name</th>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Code</th>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Perusahaan</th>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-egg-200">
                            @forelse($itemBarcodes as $ib)
                                <tr>
                                    <td class="px-2 py-1">{{ $ib->barcode_id }}</td>
                                    <td class="px-2 py-1">{{ $ib->item->part_name ?? '-' }}</td>
                                    <td class="px-2 py-1">{{ $ib->item->code ?? '-' }}</td>
                                    <td class="px-2 py-1">{{ $ib->item->company->name ?? '-' }}</td>
                                    <td class="px-2 py-1">
                                        <a href="{{ route('item-barcodes.show', $ib) }}" class="link-egg">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-2 py-6 text-center text-egg-700">Belum ada barcode barang. <a href="{{ route('item-barcodes.create') }}" class="link-egg">Buat baru</a></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-2">
                        {{ $itemBarcodes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
