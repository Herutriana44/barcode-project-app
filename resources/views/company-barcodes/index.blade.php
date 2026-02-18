<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Barcode Perusahaan') }}
            </h2>
            <a href="{{ route('company-barcodes.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                Buat Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Barcode ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perusahaan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Barang</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($companyBarcodes as $cb)
                                <tr>
                                    <td class="px-4 py-2">{{ $cb->barcode_id }}</td>
                                    <td class="px-4 py-2">{{ $cb->company->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $cb->company->companyItems->count() ?? 0 }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('company-barcodes.show', $cb) }}" class="text-blue-600 hover:underline">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada barcode perusahaan. <a href="{{ route('company-barcodes.create') }}" class="text-blue-600 hover:underline">Buat baru</a></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $companyBarcodes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
