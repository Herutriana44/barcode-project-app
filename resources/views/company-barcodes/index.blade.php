<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-egg-900 leading-tight">
                {{ __('Barcode Perusahaan') }}
            </h2>
            <a href="{{ route('company-barcodes.create') }}" class="btn-egg-primary">
                Buat Baru
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto px-2 sm:px-4">
            <p class="text-xs text-egg-700 mb-2">Urutan: <strong>FIFO</strong> (entri lama dulu).</p>
            <div class="bg-white overflow-hidden shadow-sm border border-egg-200 sm:rounded-lg">
                <div class="p-2">
                    <table class="min-w-full divide-y divide-egg-200 text-xs">
                        <thead>
                            <tr>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Barcode ID</th>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Perusahaan</th>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Jumlah Barang</th>
                                <th class="px-2 py-1 text-left font-medium text-egg-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-egg-200">
                            @forelse($companyBarcodes as $cb)
                                <tr>
                                    <td class="px-2 py-1">{{ $cb->barcode_id }}</td>
                                    <td class="px-2 py-1">{{ $cb->company->name ?? '-' }}</td>
                                    <td class="px-2 py-1">{{ $cb->company->companyItems->count() ?? 0 }}</td>
                                    <td class="px-2 py-1">
                                        <a href="{{ route('company-barcodes.show', $cb) }}" class="link-egg">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 py-6 text-center text-egg-700">Belum ada barcode perusahaan. <a href="{{ route('company-barcodes.create') }}" class="link-egg">Buat baru</a></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-2">
                        {{ $companyBarcodes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
