<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Import Perusahaan (Excel)') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('company-barcodes.import.template') }}" class="btn-egg-secondary">Unduh template .xlsx</a>
                <a href="{{ route('company-barcodes.index') }}" class="btn-egg-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full max-w-3xl mx-auto">
        <div class="bg-white shadow-md border border-egg-200 rounded-xl p-6 space-y-4 text-base">
            <p class="text-egg-700">
                Unggah berkas <strong>.xlsx</strong> sesuai template. Baris dengan <strong>nama_perusahaan</strong> yang sama digabung menjadi <strong>satu perusahaan</strong> dengan beberapa barang stok, lalu dibuat satu <strong>barcode perusahaan</strong> (sama seperti alur buat manual).
                Tiap grup perusahaan wajib punya minimal satu baris dengan <strong>qty &gt; 0</strong>.
            </p>
            <p class="text-sm text-egg-600">
                Kolom <code>code</code> boleh dikosongkan (akan digenerate). Karyawan: isi <strong>nama</strong> yang terdaftar (opsional).
            </p>

            @if ($errors->any())
                <div class="p-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('import_errors'))
                <div class="p-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
                    <p class="font-semibold mb-2">Import dibatalkan karena kesalahan berikut:</p>
                    <ul class="list-disc list-inside space-y-1 max-h-64 overflow-y-auto">
                        @foreach (session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('company-barcodes.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-medium text-egg-800 mb-1">Berkas Excel</label>
                    <input type="file" name="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                        class="block w-full text-sm text-egg-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-egg-200 file:text-egg-900">
                </div>
                <button type="submit" class="btn-egg-primary">Import</button>
            </form>
        </div>
    </div>
</x-app-layout>
