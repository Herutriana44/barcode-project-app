<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Barcode Perusahaan') }}
            </h2>
            <div class="flex flex-wrap gap-2 justify-end">
                <a href="{{ route('company-barcodes.import') }}" class="btn-egg-secondary">Import Excel</a>
                <a href="{{ route('company-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
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
            <p class="text-base text-egg-700 mb-4">Urutan: <strong>FIFO</strong> (entri lama dulu).</p>
            <div class="mb-4 flex flex-col sm:flex-row gap-2 sm:items-end sm:justify-between">
                <form method="GET" action="{{ route('company-barcodes.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <div class="w-full sm:w-96">
                        <label class="block text-sm font-medium text-egg-800">Cari Nama Perusahaan</label>
                        <input type="text" name="q" value="{{ $q ?? request('q') }}" placeholder="Contoh: PT ABC"
                            class="mt-1 block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900" />
                    </div>
                    <div class="w-full sm:w-60">
                        <label class="block text-sm font-medium text-egg-800">Sort Jumlah Barang</label>
                        <select name="item_count_sort" class="mt-1 block w-full rounded-lg border-egg-300 py-2 px-3 text-sm bg-white text-egg-900">
                            <option value="">Default</option>
                            <option value="most" @selected(($itemCountSort ?? request('item_count_sort')) === 'most')>Terbanyak</option>
                            <option value="least" @selected(($itemCountSort ?? request('item_count_sort')) === 'least')>Tersedikit</option>
                        </select>
                    </div>
                    <div class="flex gap-2 items-end">
                        <button type="submit" class="btn-egg-secondary text-sm h-[42px] px-4 py-2">Cari</button>
                        <a href="{{ route('company-barcodes.index') }}" class="btn-egg-secondary text-sm h-[42px] px-4 py-2 flex items-center">Reset</a>
                    </div>
                </form>
            </div>
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 rounded-xl">
                <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-egg-200 text-base">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Barcode ID</th>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Perusahaan</th>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Jumlah Barang</th>
                                <th class="px-3 py-3 text-left font-semibold text-egg-800 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-egg-200">
                            @forelse($companies as $company)
                                @php
                                    $cb = $company->companyBarcodes->first();
                                @endphp
                                <tr>
                                    <td class="px-3 py-3">{{ $cb->barcode_id ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $company->name ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $company->company_items_count ?? 0 }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($cb)
                                                <a href="{{ route('company-barcodes.show', $cb) }}" class="link-egg">Lihat</a>
                                                <a href="{{ route('company-barcodes.edit', $cb) }}" class="link-egg">Ubah</a>
                                                <form action="{{ route('company-barcodes.destroy', $cb) }}" method="POST" class="inline" onsubmit="return confirm('Hapus seluruh data perusahaan ini, semua barcode perusahaan, dan barang terkait? Tindakan tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm underline">Hapus</button>
                                                </form>
                                            @else
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <a href="{{ route('company-barcodes.create', ['company_id' => $company->id]) }}" class="link-egg">Buat Barcode</a>
                                                    <form action="{{ route('company-barcodes.destroy-company', $company->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus perusahaan ini?');">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm underline">Hapus</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-10 text-center text-egg-700 text-lg">Belum ada perusahaan. <a href="{{ route('company-barcodes.create') }}" class="link-egg">Buat baru</a></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-2">
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
