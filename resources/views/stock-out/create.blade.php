<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-egg-900 leading-tight">Pengeluaran FIFO</h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-xl mx-auto px-2 sm:px-4">
            @if (session('success'))
                <p class="mb-2 p-2 text-sm bg-egg-100 border border-egg-300 rounded text-egg-900">{{ session('success') }}</p>
            @endif
            <div class="bg-white border border-egg-200 rounded p-3 text-sm">
                <p class="text-xs text-egg-700 mb-3 leading-snug">Stok dikurangi dari batch <strong>paling lama</strong> (tanggal terima FG / entri lama) sesuai jenis stok.</p>
                @if($companies->isEmpty())
                    <p class="text-sm text-egg-700">Belum ada perusahaan. Buat barcode barang atau perusahaan terlebih dahulu.</p>
                @else
                <form method="POST" action="{{ route('stock-out.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-egg-800">Perusahaan *</label>
                        <select name="company_id" required class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm">
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}" @selected(old('company_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-egg-800">Jenis stok *</label>
                        <select name="scope" required class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm">
                            <option value="item" @selected(old('scope', 'item') === 'item')>Barcode barang (qty pada item)</option>
                            <option value="company_item" @selected(old('scope') === 'company_item')>Barcode perusahaan (qty pada baris perusahaan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-egg-800">Qty keluar *</label>
                        <input type="number" name="qty" value="{{ old('qty', 1) }}" min="1" required
                            class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm" />
                        @error('qty')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-egg-800">Filter part number (opsional, barcode barang)</label>
                        <input type="text" name="part_number" value="{{ old('part_number') }}"
                            class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-egg-800">Filter part name (opsional)</label>
                        <input type="text" name="part_name" value="{{ old('part_name') }}"
                            class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm" />
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn-egg-primary">Proses FIFO</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
