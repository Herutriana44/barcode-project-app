<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Import Barang (Excel)') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('item-barcodes.import.template') }}" class="btn-egg-secondary">Unduh template .xlsx</a>
                <a href="{{ route('item-barcodes.index') }}" class="btn-egg-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full max-w-3xl mx-auto">
        <div class="bg-white shadow-md border border-egg-200 rounded-xl p-6 space-y-4 text-base">
            <p class="text-egg-700">
                Unggah berkas <strong>.xlsx</strong> sesuai template. Setiap baris (selain header) menjadi satu <strong>barcode barang (FG)</strong> seperti menu buat manual.
                Kolom <strong>nama_perusahaan</strong> harus sama persis dengan nama perusahaan yang sudah ada di sistem (cek daftar saat buat barang).
                Karyawan diisi dengan <strong>nama</strong> yang terdaftar (opsional).
            </p>
            <p class="text-sm text-egg-600">
                Tanggal boleh teks (<code>YYYY-MM-DD</code> atau format umum) atau angka serial Excel. <code>jenis_bahan</code>: kosong, SPCC, atau SESE.
                Kolom akhir template (opsional): <code>qty_sub_pack</code>, <code>berat_packaging_gram</code>, <code>berat_per_pcs_gram</code>.
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

            <form action="{{ route('item-barcodes.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
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
