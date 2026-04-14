<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Barcode Barang') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('item-barcodes.labels') }}" class="btn-egg-secondary" target="_blank" rel="noopener">Cetak semua label (PDF)</a>
                <a href="{{ route('item-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full">
        <div class="w-full">
            <p class="text-base text-egg-700 mb-4">Urutan: <strong>FIFO</strong> (terima FG lebih dulu di atas).</p>
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 rounded-xl">
                <div class="p-4">
                    <table class="min-w-full divide-y divide-egg-200 text-base">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Barcode ID</th>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Part Name</th>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Code</th>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Perusahaan</th>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-egg-200">
                            @forelse($itemBarcodes as $ib)
                                <tr>
                                    <td class="px-3 py-3">{{ $ib->barcode_id }}</td>
                                    <td class="px-3 py-3">{{ $ib->item->part_name ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $ib->item->code ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $ib->item->company->name ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        <a href="{{ route('item-barcodes.show', $ib) }}" class="link-egg">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-10 text-center text-egg-700 text-lg">Belum ada barcode barang. <a href="{{ route('item-barcodes.create') }}" class="link-egg">Buat baru</a></td>
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
