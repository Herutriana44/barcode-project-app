<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-egg-900 leading-tight">
            {{ __('Buat Barcode Perusahaan') }}
        </h2>
    </x-slot>

    @php
        $itemRows = old('items', [
            ['part_name' => '', 'code' => '', 'qty' => '', 'posisi_rak' => '', 'tingkat' => ''],
            ['part_name' => '', 'code' => '', 'qty' => '', 'posisi_rak' => '', 'tingkat' => ''],
        ]);
    @endphp

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm border border-egg-200 sm:rounded-lg">
                <form action="{{ route('company-barcodes.store') }}" method="POST" class="p-6 space-y-8" id="companyBarcodeForm">
                    @csrf

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-egg-800">Nama perusahaan *</label>
                        <input
                            type="text"
                            name="company_name"
                            id="company_name"
                            value="{{ old('company_name') }}"
                            required
                            maxlength="255"
                            placeholder="Contoh: PT Contoh Indonesia"
                            class="mt-1 block w-full rounded-md border-egg-300 shadow-sm focus:border-egg-500 focus:ring-egg-500"
                        />
                        @error('company_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-egg-900">Barang</h3>
                                <p class="text-sm text-egg-700 mt-1">Isi data barang secara manual. Kode boleh kosong (akan digenerate otomatis). Minimal satu baris dengan qty &gt; 0.</p>
                            </div>
                            <button type="button" id="add-item-row" class="btn-egg-secondary text-sm py-2 px-4 lg:min-h-0">
                                + Tambah baris
                            </button>
                        </div>

                        @error('items')
                            <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
                        @enderror

                        <div class="overflow-x-auto border border-egg-200 rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-egg-50 text-egg-800">
                                    <tr>
                                        <th class="text-left py-3 px-3 font-semibold">Nama barang</th>
                                        <th class="text-left py-3 px-3 font-semibold w-36">Kode</th>
                                        <th class="text-left py-3 px-3 font-semibold w-24">Qty *</th>
                                        <th class="text-left py-3 px-3 font-semibold w-28">Rak</th>
                                        <th class="text-left py-3 px-3 font-semibold w-28">Tingkat</th>
                                        <th class="w-12 py-3 px-2"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-rows" class="divide-y divide-egg-200">
                                    @foreach($itemRows as $idx => $row)
                                        <tr class="item-row" data-row-index="{{ $idx }}">
                                            <td class="py-3 px-3 align-top">
                                                <input
                                                    type="text"
                                                    name="items[{{ $idx }}][part_name]"
                                                    value="{{ $row['part_name'] ?? '' }}"
                                                    placeholder="Part name / nama"
                                                    class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500"
                                                />
                                            </td>
                                            <td class="py-3 px-3 align-top">
                                                <input
                                                    type="text"
                                                    name="items[{{ $idx }}][code]"
                                                    value="{{ $row['code'] ?? '' }}"
                                                    placeholder="Opsional"
                                                    class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500"
                                                />
                                            </td>
                                            <td class="py-3 px-3 align-top">
                                                <input
                                                    type="number"
                                                    name="items[{{ $idx }}][qty]"
                                                    value="{{ $row['qty'] ?? '' }}"
                                                    min="0"
                                                    placeholder="0"
                                                    class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500 item-qty"
                                                />
                                            </td>
                                            <td class="py-3 px-3 align-top">
                                                <input
                                                    type="text"
                                                    name="items[{{ $idx }}][posisi_rak]"
                                                    value="{{ $row['posisi_rak'] ?? '' }}"
                                                    placeholder="Rak"
                                                    class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500"
                                                />
                                            </td>
                                            <td class="py-3 px-3 align-top">
                                                <input
                                                    type="text"
                                                    name="items[{{ $idx }}][tingkat]"
                                                    value="{{ $row['tingkat'] ?? '' }}"
                                                    placeholder="Tingkat"
                                                    class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500"
                                                />
                                            </td>
                                            <td class="py-3 px-2 align-top text-center">
                                                <button type="button" class="remove-item-row text-egg-600 hover:text-red-600 p-1" title="Hapus baris" aria-label="Hapus baris">&times;</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <button type="submit" class="btn-egg-primary">Generate Barcode</button>
                        <a href="{{ route('company-barcodes.index') }}" class="btn-egg-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="item-row-template">
        <tr class="item-row">
            <td class="py-3 px-3 align-top">
                <input type="text" name="items[__I__][part_name]" value="" placeholder="Part name / nama" class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500" />
            </td>
            <td class="py-3 px-3 align-top">
                <input type="text" name="items[__I__][code]" value="" placeholder="Opsional" class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500" />
            </td>
            <td class="py-3 px-3 align-top">
                <input type="number" name="items[__I__][qty]" value="" min="0" placeholder="0" class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500 item-qty" />
            </td>
            <td class="py-3 px-3 align-top">
                <input type="text" name="items[__I__][posisi_rak]" value="" placeholder="Rak" class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500" />
            </td>
            <td class="py-3 px-3 align-top">
                <input type="text" name="items[__I__][tingkat]" value="" placeholder="Tingkat" class="w-full rounded-md border-egg-300 text-sm shadow-sm focus:border-egg-500 focus:ring-egg-500" />
            </td>
            <td class="py-3 px-2 align-top text-center">
                <button type="button" class="remove-item-row text-egg-600 hover:text-red-600 p-1" title="Hapus baris" aria-label="Hapus baris">&times;</button>
            </td>
        </tr>
    </template>

    <script>
        (function () {
            const tbody = document.getElementById('items-rows');
            const tpl = document.getElementById('item-row-template');
            let nextIndex = {{ count($itemRows) }};

            document.getElementById('add-item-row').addEventListener('click', function () {
                const row = tpl.content.firstElementChild.cloneNode(true);
                row.querySelectorAll('[name*="__I__"]').forEach(function (el) {
                    el.name = el.name.replace('__I__', String(nextIndex));
                });
                nextIndex++;
                tbody.appendChild(row);
                bindRemove(row.querySelector('.remove-item-row'));
            });

            function bindRemove(btn) {
                if (!btn) return;
                btn.addEventListener('click', function () {
                    const rows = tbody.querySelectorAll('.item-row');
                    if (rows.length <= 1) {
                        rows[0].querySelectorAll('input').forEach(function (el) { el.value = ''; });
                        return;
                    }
                    btn.closest('tr').remove();
                });
            }

            tbody.querySelectorAll('.remove-item-row').forEach(bindRemove);
        })();
    </script>
</x-app-layout>
