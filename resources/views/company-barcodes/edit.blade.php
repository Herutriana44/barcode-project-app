@php
    $itemRows = old('items');
    if (! is_array($itemRows)) {
        $itemRows = $companyBarcode->company->companyItems->map(function ($ci) {
            $item = $ci->item;

            return [
                'company_item_id' => (string) $ci->id,
                'part_name' => $item->part_name ?? '',
                'code' => $item->code ?? '',
                'qty' => (string) $ci->qty,
                'posisi_rak' => $ci->posisi_rak ?? '',
                'tingkat' => $ci->tingkat ?? '',
            ];
        })->values()->all();
    }
    if (count($itemRows) === 0) {
        $itemRows = [['company_item_id' => '', 'part_name' => '', 'code' => '', 'qty' => '', 'posisi_rak' => '', 'tingkat' => '']];
    }
@endphp
{{-- Kolom input karyawan per baris barang disembunyikan (permintaan). --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Ubah Data Perusahaan') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-[100rem] mx-auto w-full">
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl">
                <form action="{{ route('company-barcodes.update', $companyBarcode) }}" method="POST" class="p-6 md:p-8 space-y-6 text-base" id="companyBarcodeForm">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-egg-800">Nama perusahaan *</label>
                        <input
                            type="text"
                            name="company_name"
                            id="company_name"
                            value="{{ old('company_name', $companyBarcode->company->name) }}"
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
                                <p class="text-sm text-egg-700 mt-1">Ubah baris yang ada, tambah baris baru, atau hapus baris (qty dikosongkan / 0 akan mengabaikan baris saat simpan — minimal satu baris dengan qty &gt; 0).</p>
                            </div>
                            <button type="button" id="add-item-row" class="btn-egg-secondary text-sm py-2 px-4 lg:min-h-0">
                                + Tambah baris
                            </button>
                        </div>

                        @error('items')
                            <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
                        @enderror

                        <div class="overflow-x-auto border border-egg-200 rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-egg-50 text-egg-800">
                                    <tr>
                                        <th class="text-left py-1.5 px-2 font-semibold">Nama barang</th>
                                        <th class="text-left py-1.5 px-2 font-semibold w-32">Barcode</th>
                                        <th class="text-left py-1.5 px-2 font-semibold w-20">Qty *</th>
                                        <th class="text-left py-1.5 px-2 font-semibold w-24">Rak</th>
                                        <th class="text-left py-1.5 px-2 font-semibold w-24">Tingkat</th>
                                        <th class="w-8 py-1.5 px-1"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-rows" class="divide-y divide-egg-200">
                                    @foreach($itemRows as $idx => $row)
                                        <tr class="item-row" data-row-index="{{ $idx }}">
                                            <td class="py-1 px-2 align-top">
                                                <input type="hidden" name="items[{{ $idx }}][company_item_id]" value="{{ $row['company_item_id'] ?? '' }}" />
                                                <input
                                                    type="text"
                                                    name="items[{{ $idx }}][part_name]"
                                                    value="{{ $row['part_name'] ?? '' }}"
                                                    placeholder="Part name / nama"
                                                    class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1"
                                                />
                                            </td>
                                            <td class="py-1 px-2 align-top">
                                                <input
                                                    type="text"
                                                    name="items[{{ $idx }}][code]"
                                                    value="{{ $row['code'] ?? '' }}"
                                                    placeholder="Opsional"
                                                    class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1"
                                                />
                                            </td>
                                            <td class="py-1 px-2 align-top">
                                                <input
                                                    type="number"
                                                    name="items[{{ $idx }}][qty]"
                                                    value="{{ $row['qty'] ?? '' }}"
                                                    min="0"
                                                    placeholder="0"
                                                    class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 item-qty py-1"
                                                />
                                            </td>
                                            <td class="py-1 px-2 align-top">
                                                <select
                                                    name="items[{{ $idx }}][posisi_rak]"
                                                    data-rak-select
                                                    data-current="{{ $row['posisi_rak'] ?? '' }}"
                                                    class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1 bg-white"
                                                >
                                                    <option value="">—</option>
                                                </select>
                                            </td>
                                            <td class="py-1 px-2 align-top">
                                                <input
                                                    type="text"
                                                    name="items[{{ $idx }}][tingkat]"
                                                    value="{{ $row['tingkat'] ?? '' }}"
                                                    placeholder="Tingkat"
                                                    class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1"
                                                />
                                            </td>
                                            <td class="py-1 px-1 align-top text-center">
                                                <button type="button" class="remove-item-row text-egg-600 hover:text-red-600 p-0.5" title="Hapus baris" aria-label="Hapus baris">&times;</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <button type="submit" class="btn-egg-primary">Simpan perubahan</button>
                        <a href="{{ route('company-barcodes.show', $companyBarcode) }}" class="btn-egg-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="item-row-template">
        <tr class="item-row">
            <td class="py-1 px-2 align-top">
                <input type="hidden" name="items[__I__][company_item_id]" value="" />
                <input type="text" name="items[__I__][part_name]" value="" placeholder="Part name / nama" class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1" />
            </td>
            <td class="py-1 px-2 align-top">
                <input type="text" name="items[__I__][code]" value="" placeholder="Opsional" class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1" />
            </td>
            <td class="py-1 px-2 align-top">
                <input type="number" name="items[__I__][qty]" value="" min="0" placeholder="0" class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 item-qty py-1" />
            </td>
            <td class="py-1 px-2 align-top">
                <select name="items[__I__][posisi_rak]" data-rak-select data-current="" class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1 bg-white">
                    <option value="">—</option>
                </select>
            </td>
            <td class="py-1 px-2 align-top">
                <input type="text" name="items[__I__][tingkat]" value="" placeholder="Tingkat" class="w-full rounded border-egg-300 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 py-1" />
            </td>
            <td class="py-1 px-1 align-top text-center">
                <button type="button" class="remove-item-row text-egg-600 hover:text-red-600 p-0.5" title="Hapus baris" aria-label="Hapus baris">&times;</button>
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
                refreshRakOptions();
            });

            function bindRemove(btn) {
                if (!btn) return;
                btn.addEventListener('click', function () {
                    const rows = tbody.querySelectorAll('.item-row');
                    if (rows.length <= 1) {
                        rows[0].querySelectorAll('input:not([type="hidden"])').forEach(function (el) { el.value = ''; });
                        rows[0].querySelectorAll('select').forEach(function (el) { el.selectedIndex = 0; });
                        return;
                    }
                    btn.closest('tr').remove();
                });
            }

            tbody.querySelectorAll('.remove-item-row').forEach(bindRemove);

            const companyNameInput = document.getElementById('company_name');

            async function fetchRakOptions(companyName) {
                const name = (companyName || '').trim();
                if (!name) return [];
                const url = `{{ route('raks.options') }}?company_name=${encodeURIComponent(name)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return [];
                const json = await res.json();
                console.log('Rak Debug - Raw Data:', json.raw_data);
                console.log('Rak Debug - Parsed Codes:', json.parsed_codes);
                return Array.isArray(json.codes) ? json.codes : [];
            }

            function applyOptionsToAllSelects(codes) {
                document.querySelectorAll('select[data-rak-select]').forEach(function (sel) {
                    const current = (sel.getAttribute('data-current') || '').trim();
                    const keep = sel.value || current;
                    sel.innerHTML = '<option value="">—</option>';
                    codes.forEach(function (c) {
                        const opt = document.createElement('option');
                        opt.value = c;
                        opt.textContent = c;
                        sel.appendChild(opt);
                    });
                    if (keep) {
                        sel.value = keep;
                        if (sel.value !== keep) {
                            const opt = document.createElement('option');
                            opt.value = keep;
                            opt.textContent = keep + ' (tidak tersedia)';
                            sel.appendChild(opt);
                            sel.value = keep;
                        }
                    }
                });
            }

            let lastCompany = '';
            async function refreshRakOptions() {
                const name = (companyNameInput?.value || '').trim();
                if (name === lastCompany) return;
                lastCompany = name;
                const codes = await fetchRakOptions(name);
                applyOptionsToAllSelects(codes);
            }

            if (companyNameInput) {
                companyNameInput.addEventListener('input', function () {
                    window.clearTimeout(companyNameInput._rakT);
                    companyNameInput._rakT = window.setTimeout(refreshRakOptions, 250);
                });
            }

            refreshRakOptions();
        })();
    </script>
</x-app-layout>
