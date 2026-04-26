<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Barcode Barang') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('item-barcodes.import') }}" class="btn-egg-secondary">Import Excel</a>
                <a href="{{ route('item-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full">
        <div class="w-full">
            @if (session('success'))
                <p class="mb-4 p-2 text-sm bg-egg-100 border border-egg-300 rounded text-egg-900">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p class="mb-4 p-2 text-sm bg-red-50 border border-red-200 rounded text-red-800">{{ session('error') }}</p>
            @endif
            <p class="text-base text-egg-700 mb-4">Urutan: <strong>FIFO</strong> (terima FG lebih dulu di atas).</p>
            <div class="mb-4 flex flex-col sm:flex-row gap-2 sm:items-end sm:justify-between">
                <form method="GET" action="{{ route('item-barcodes.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <div class="w-full sm:w-96">
                        <label class="block text-sm font-medium text-egg-800">Cari Part Code / Code Part</label>
                        <input type="text" name="q" value="{{ $q ?? request('q') }}" placeholder="Contoh: 955985"
                            class="mt-1 block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-egg-secondary text-sm">Cari</button>
                        <a href="{{ route('item-barcodes.index') }}" class="btn-egg-secondary text-sm">Reset</a>
                    </div>
                </form>
                <a href="{{ route('item-barcodes.labels', ($q ?? request('q')) ? ['q' => ($q ?? request('q'))] : []) }}"
                    class="btn-egg-secondary" target="_blank" rel="noopener">
                    Cetak semua label (PDF)
                </a>
            </div>
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
                                    <td class="px-3 py-3">{{ $ib->item->customer ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('item-barcodes.show', $ib) }}" class="link-egg">Lihat</a>
                                            <a href="{{ route('item-barcodes.edit', $ib) }}" class="link-egg">Ubah</a>
                                            <form action="{{ route('item-barcodes.destroy', $ib) }}" method="POST" class="inline" onsubmit="return confirm('Hapus barcode barang ini? Data penerimaan terkait juga akan dihapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm underline">Hapus</button>
                                            </form>
                                        </div>
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
