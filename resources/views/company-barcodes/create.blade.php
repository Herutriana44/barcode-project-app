<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Barcode Perusahaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('company-barcodes.store') }}" method="POST" class="p-6 space-y-6" id="companyBarcodeForm">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Perusahaan *</label>
                        <select name="company_id" id="company_id" required class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Barang</h3>
                        <p class="text-sm text-gray-500 mb-4">Centang barang dan isi qty, posisi rak, tingkat untuk barang yang akan dimasukkan ke barcode perusahaan.</p>

                        <div id="items-container" class="space-y-4">
                            @php
                                $selectedCompanyId = old('company_id', $companies->first()?->id);
                                $selectedCompany = $companies->firstWhere('id', (int)$selectedCompanyId) ?? $companies->first();
                                $items = $selectedCompany ? $selectedCompany->items : collect();
                            @endphp
                            @forelse($items as $item)
                                <div class="flex items-center gap-4 p-4 border rounded-lg item-row" data-company-id="{{ $selectedCompany->id }}">
                                    <input type="checkbox" name="item_include[{{ $item->id }}]" value="1" class="item-checkbox">
                                    <div class="flex-1">
                                        <span class="font-medium">{{ $item->part_name ?? $item->part_number ?? 'Item #'.$item->id }}</span>
                                        <span class="text-gray-500 text-sm">({{ $item->code }})</span>
                                    </div>
                                    <div class="w-24">
                                        <input type="number" name="item_qty[{{ $item->id }}]" value="0" min="0" class="rounded-md border-gray-300 item-qty" placeholder="Qty">
                                    </div>
                                    <div class="w-24">
                                        <input type="text" name="item_posisi[{{ $item->id }}]" class="rounded-md border-gray-300" placeholder="Rak">
                                    </div>
                                    <div class="w-24">
                                        <input type="text" name="item_tingkat[{{ $item->id }}]" class="rounded-md border-gray-300" placeholder="Tingkat">
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500" id="no-items-msg">Pilih perusahaan untuk melihat daftar barang. Pastikan perusahaan memiliki barang.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">Generate Barcode</button>
                        <a href="{{ route('company-barcodes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const companiesData = @json($companiesJson);

        document.getElementById('company_id').addEventListener('change', function() {
            const companyId = parseInt(this.value);
            const container = document.getElementById('items-container');
            const noItemsMsg = document.getElementById('no-items-msg');

            const company = companiesData.find(c => c.id === companyId);
            if (!company || !company.items || company.items.length === 0) {
                container.innerHTML = '<p class="text-gray-500" id="no-items-msg">Tidak ada barang di perusahaan ini.</p>';
                return;
            }

            let html = '';
            company.items.forEach((item) => {
                html += `
                    <div class="flex items-center gap-4 p-4 border rounded-lg item-row" data-company-id="${company.id}">
                        <input type="checkbox" name="item_include[${item.id}]" value="1" class="item-checkbox">
                        <div class="flex-1">
                            <span class="font-medium">${item.part_name || item.part_number || 'Item #'+item.id}</span>
                            <span class="text-gray-500 text-sm">(${item.code || ''})</span>
                        </div>
                        <div class="w-24">
                            <input type="number" name="item_qty[${item.id}]" value="0" min="0" class="rounded-md border-gray-300 item-qty" placeholder="Qty">
                        </div>
                        <div class="w-24">
                            <input type="text" name="item_posisi[${item.id}]" class="rounded-md border-gray-300" placeholder="Rak">
                        </div>
                        <div class="w-24">
                            <input type="text" name="item_tingkat[${item.id}]" class="rounded-md border-gray-300" placeholder="Tingkat">
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        });

    </script>
</x-app-layout>
