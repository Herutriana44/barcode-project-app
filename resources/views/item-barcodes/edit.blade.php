@php
    $item = $itemBarcode->item;
    $recv = $itemBarcode->itemReceiving;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Ubah Data Barang') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-4xl mx-auto w-full">
            <p class="text-sm text-egg-600 mb-4">Barcode ID tetap: <span class="font-mono font-medium text-egg-900">{{ $itemBarcode->barcode_id }}</span></p>
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl">
                <form action="{{ route('item-barcodes.update', $itemBarcode) }}" method="POST" class="p-6 md:p-8 space-y-6 text-base">
                    @csrf
                    @method('PUT')

                    <div class="border-b pb-4">
                        <h3 class="text-xl font-bold text-egg-900 mb-4">2A: Info Label Barang</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-base font-medium text-egg-800">Perusahaan</label>
                                <input type="text" value="{{ $warehouseCompany->name }}" class="mt-1 block w-full rounded-md border-egg-300 bg-egg-50" readonly>
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Code (Kode Unik) *</label>
                                <input type="text" name="code" value="{{ old('code', $item->code) }}" required class="mt-1 block w-full rounded-md border-egg-300">
                                @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Customer</label>
                                <select name="customer" class="mt-1 block w-full rounded-md border-egg-300">
                                    <option value="">—</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->name }}" @selected(old('customer', $item->customer) == $c->name)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Part Name</label>
                                <input type="text" name="part_name" value="{{ old('part_name', $item->part_name) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Part Number</label>
                                <input type="text" name="part_number" value="{{ old('part_number', $item->part_number) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Model</label>
                                <input type="text" name="model" value="{{ old('model', $item->model) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Berat</label>
                                <input type="number" step="0.01" name="berat" value="{{ old('berat', $item->berat) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Qty *</label>
                                <input type="number" name="qty" value="{{ old('qty', $item->qty) }}" required class="mt-1 block w-full rounded-md border-egg-300">
                                @error('qty')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Inspector Name</label>
                                <input type="text" name="inspector_name" value="{{ old('inspector_name', $item->inspector_name) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tgl Produksi</label>
                                <input type="date" name="tgl_produksi" value="{{ old('tgl_produksi', $item->tgl_produksi?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tgl Expired</label>
                                <input type="date" name="tgl_expired" value="{{ old('tgl_expired', $item->tgl_expired?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Posisi Rak</label>
                                <input type="text" name="posisi_rak" value="{{ old('posisi_rak', $item->posisi_rak) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tingkat</label>
                                <input type="text" name="tingkat" value="{{ old('tingkat', $item->tingkat) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                        </div>

                        <h4 class="text-lg font-bold text-egg-900 mt-6 mb-3">Material</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-base font-medium text-egg-800">Ukuran Material</label>
                                <input type="text" name="ukuran_material" value="{{ old('ukuran_material', $item->ukuran_material) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Jenis Bahan</label>
                                <select name="jenis_bahan" class="mt-1 block w-full rounded-md border-egg-300">
                                    <option value="">-- Pilih --</option>
                                    <option value="SPCC" @selected(old('jenis_bahan', $item->jenis_bahan) == 'SPCC')>SPCC</option>
                                    <option value="SESE" @selected(old('jenis_bahan', $item->jenis_bahan) == 'SESE')>SESE</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Quantity Material</label>
                                <input type="number" name="quantity_material" value="{{ old('quantity_material', $item->quantity_material) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">No Surat Jalan Material</label>
                                <input type="text" name="no_surat_jalan_material" value="{{ old('no_surat_jalan_material', $item->no_surat_jalan_material) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tanggal Terima Material</label>
                                <input type="date" name="tanggal_terima_material" value="{{ old('tanggal_terima_material', $item->tanggal_terima_material?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                        </div>
                    </div>

                    {{-- Input karyawan (opsional) pada barang disembunyikan sesuai permintaan. --}}

                    <div class="border-b pb-4">
                        <h3 class="text-xl font-bold text-egg-900 mb-4">2B: Barang Masuk (Checker/Finishing)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-base font-medium text-egg-800">Nomor Transfer Slip</label>
                                <input type="text" name="transfer_slip_no" value="{{ old('transfer_slip_no', $recv->transfer_slip_no) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tanggal Terima FG ke Gudang</label>
                                <input type="date" name="tanggal_terima_fg" value="{{ old('tanggal_terima_fg', $recv->tanggal_terima_fg?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Jumlah Box</label>
                                <input type="number" name="jumlah_box" value="{{ old('jumlah_box', $recv->jumlah_box) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <button type="submit" class="btn-egg-primary">Simpan perubahan</button>
                        <a href="{{ route('item-barcodes.show', $itemBarcode) }}" class="btn-egg-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
