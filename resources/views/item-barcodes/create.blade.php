<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Buat Barcode Barang') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-4xl mx-auto w-full">
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl">
                <form action="{{ route('item-barcodes.store') }}" method="POST" class="p-6 md:p-8 space-y-6 text-base">
                    @csrf

                    <div class="border-b pb-4">
                        <h3 class="text-xl font-bold text-egg-900 mb-4">2A: Info Label Barang</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-base font-medium text-egg-800">Perusahaan</label>
                                <input type="text" value="{{ $warehouseCompany->name }}" class="mt-1 block w-full rounded-md border-egg-300 bg-egg-50" readonly>
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Code (Kode Unik) *</label>
                                <input type="text" name="code" value="{{ old('code') }}" required class="mt-1 block w-full rounded-md border-egg-300">
                                @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Customer</label>
                                <select name="customer" id="customer_company" class="mt-1 block w-full rounded-md border-egg-300 bg-white">
                                    <option value="">—</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->name }}" {{ old('customer') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Part Name</label>
                                <input type="text" name="part_name" value="{{ old('part_name') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Part Number</label>
                                <input type="text" name="part_number" value="{{ old('part_number') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Model</label>
                                <input type="text" name="model" value="{{ old('model') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Berat</label>
                                <input type="number" step="0.01" name="berat" value="{{ old('berat') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Qty *</label>
                                <input type="number" name="qty" value="{{ old('qty', 0) }}" required class="mt-1 block w-full rounded-md border-egg-300">
                                @error('qty')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Inspector Name</label>
                                <input type="text" name="inspector_name" value="{{ old('inspector_name') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tgl Produksi</label>
                                <input type="date" name="tgl_produksi" value="{{ old('tgl_produksi') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tgl Expired</label>
                                <input type="date" name="tgl_expired" value="{{ old('tgl_expired') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Posisi Rak</label>
                                <select name="posisi_rak" id="posisi_rak" data-rak-select data-current="{{ old('posisi_rak') }}"
                                    class="mt-1 block w-full rounded-md border-egg-300 bg-white">
                                    <option value="">—</option>
                                </select>
                            </div>
                            <!-- <div>
                                <label class="block text-base font-medium text-egg-800">Tingkat</label>
                                <input type="text" name="tingkat" value="{{ old('tingkat') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div> -->
                        </div>

                        <!-- <h4 class="text-lg font-bold text-egg-900 mt-6 mb-3">Material</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-base font-medium text-egg-800">Ukuran Material</label>
                                <input type="text" name="ukuran_material" value="{{ old('ukuran_material') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Jenis Bahan</label>
                                <select name="jenis_bahan" class="mt-1 block w-full rounded-md border-egg-300">
                                    <option value="">-- Pilih --</option>
                                    <option value="SPCC" {{ old('jenis_bahan') == 'SPCC' ? 'selected' : '' }}>SPCC</option>
                                    <option value="SESE" {{ old('jenis_bahan') == 'SESE' ? 'selected' : '' }}>SESE</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Quantity Material</label>
                                <input type="number" name="quantity_material" value="{{ old('quantity_material') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">No Surat Jalan Material</label>
                                <input type="text" name="no_surat_jalan_material" value="{{ old('no_surat_jalan_material') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tanggal Terima Material</label>
                                <input type="date" name="tanggal_terima_material" value="{{ old('tanggal_terima_material') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                        </div> -->
                    </div>

                    <!-- {{-- Input karyawan (opsional) pada barang disembunyikan sesuai permintaan.
                    <div class="border-b pb-3">
                        <h3 class="text-xl font-bold text-egg-900 mb-4">Karyawan (opsional)</h3>
                        ...
                    </div>
                    --}}

                    <div class="border-b pb-4">
                        <h3 class="text-xl font-bold text-egg-900 mb-4">2B: Barang Masuk (Checker/Finishing)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-base font-medium text-egg-800">Nomor Transfer Slip</label>
                                <input type="text" name="transfer_slip_no" value="{{ old('transfer_slip_no') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Tanggal Terima FG ke Gudang</label>
                                <input type="date" name="tanggal_terima_fg" value="{{ old('tanggal_terima_fg') }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                            <div>
                                <label class="block text-base font-medium text-egg-800">Jumlah Box</label>
                                <input type="number" name="jumlah_box" value="{{ old('jumlah_box', 0) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div>
                        </div>
                    </div> -->

                    <div class="flex flex-wrap gap-4">
                        <button type="submit" class="btn-egg-primary">Generate Barcode</button>
                        <a href="{{ route('item-barcodes.index') }}" class="btn-egg-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const produksi = document.querySelector('input[name="tgl_produksi"]');
            const expired = document.querySelector('input[name="tgl_expired"]');
            if (!produksi || !expired) return;

            function toYmd(d) {
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            }

            function parseYmd(s) {
                if (!s) return null;
                const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s);
                if (!m) return null;
                const d = new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
                return Number.isNaN(d.getTime()) ? null : d;
            }

            function addMonthsSafe(date, months) {
                const d = new Date(date.getTime());
                const day = d.getDate();
                d.setDate(1);
                d.setMonth(d.getMonth() + months);
                const lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
                d.setDate(Math.min(day, lastDay));
                return d;
            }

            function setDefaultExpiredIfEmpty() {
                if (expired.value) return;
                const base = parseYmd(produksi.value) || new Date();
                const next = addMonthsSafe(base, 3);
                expired.value = toYmd(next);
                expired.dataset.auto = '1';
            }

            setDefaultExpiredIfEmpty();

            expired.addEventListener('input', function () {
                expired.dataset.auto = '0';
            });

            produksi.addEventListener('input', function () {
                if (expired.value && expired.dataset.auto !== '1') return;
                const base = parseYmd(produksi.value);
                if (!base) return;
                expired.value = toYmd(addMonthsSafe(base, 3));
                expired.dataset.auto = '1';
            });
        })();
    </script>

    <script>
        (function () {
            const customerSelect = document.getElementById('customer_company');
            const rakSelect = document.getElementById('posisi_rak');
            if (!customerSelect || !rakSelect) return;

            async function fetchRakOptions(companyName) {
                const name = (companyName || '').trim();
                if (!name) return [];
                const url = `{{ route('raks.options') }}?company_name=${encodeURIComponent(name)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return [];
                const json = await res.json();
                return Array.isArray(json.codes) ? json.codes : [];
            }

            function applyOptions(codes) {
                const current = (rakSelect.getAttribute('data-current') || '').trim();
                const keep = rakSelect.value || current;
                rakSelect.innerHTML = '<option value="">—</option>';
                codes.forEach(function (c) {
                    const opt = document.createElement('option');
                    opt.value = c;
                    opt.textContent = c;
                    rakSelect.appendChild(opt);
                });
                if (keep) {
                    rakSelect.value = keep;
                    if (rakSelect.value !== keep) {
                        const opt = document.createElement('option');
                        opt.value = keep;
                        opt.textContent = keep + ' (tidak tersedia)';
                        rakSelect.appendChild(opt);
                        rakSelect.value = keep;
                    }
                }
            }

            let last = '';
            async function refresh() {
                const name = (customerSelect.value || '').trim();
                if (name === last) return;
                last = name;
                const codes = await fetchRakOptions(name);
                applyOptions(codes);
            }

            customerSelect.addEventListener('change', refresh);
            refresh();
        })();
    </script>
</x-app-layout>
