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

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Ada kesalahan!</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
                                <select name="customer" id="customer_company" class="mt-1 block w-full rounded-md border-egg-300 bg-white">
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
                                <label class="block text-base font-medium text-egg-800">Qty Sub Pack</label>
                                <input type="number" name="qty_sub_pack" value="{{ old('qty_sub_pack', $item->qty_sub_pack) }}" class="mt-1 block w-full rounded-md border-egg-300">
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
                            <select name="posisi_rak" id="posisi_rak" data-rak-select data-current="{{ old('posisi_rak', $item->posisi_rak) }}"
                                class="mt-1 block w-full rounded-md border-egg-300 bg-white max-w-full whitespace-normal break-words">
                                <option value="" class="whitespace-normal break-words">—</option>
                            </select>
                            <input type="text" name="posisi_rak_manual" id="posisi_rak_manual" value="{{ old('posisi_rak', $item->posisi_rak) }}"
                                class="mt-1 block w-full rounded-md border-egg-300 hidden" placeholder="Masukkan nama rak manual">
                        </div>
                            <!-- <div>
                                <label class="block text-base font-medium text-egg-800">Tingkat</label>
                                <input type="text" name="tingkat" value="{{ old('tingkat', $item->tingkat) }}" class="mt-1 block w-full rounded-md border-egg-300">
                            </div> -->
                        </div>

                        <!-- <h4 class="text-lg font-bold text-egg-900 mt-6 mb-3">Material</h4>
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
                        </div> -->
                    </div>

                    {{-- Input karyawan (opsional) pada barang disembunyikan sesuai permintaan. --}}

                    <!-- <div class="border-b pb-4">
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
                    </div> -->

                    <div class="flex flex-wrap gap-4">
                        <button type="submit" class="btn-egg-primary">Simpan perubahan</button>
                        <a href="{{ route('item-barcodes.show', $itemBarcode) }}" class="btn-egg-secondary">Batal</a>
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

            // Initialize auto state
            function initAutoState() {
                if (!produksi.value) return;
                const base = parseYmd(produksi.value);
                if (!base) return;
                
                const expected = toYmd(addMonthsSafe(base, 3));
                if (!expired.value) {
                    expired.value = expected;
                    expired.dataset.auto = '1';
                } else if (expired.value === expected) {
                    expired.dataset.auto = '1';
                }
            }
            initAutoState();

            expired.addEventListener('input', function () {
                expired.dataset.auto = '0';
            });

            produksi.addEventListener('input', function (e) {
                console.log('Produksi date changed:', e.target.value);
                const base = parseYmd(e.target.value);
                console.log('Parsed base date:', base);
                if (!base) return;
                
                const newExpired = toYmd(addMonthsSafe(base, 3));
                console.log('Calculated expired:', newExpired);

                if (!expired.value || expired.dataset.auto === '1') {
                    expired.value = newExpired;
                    expired.dataset.auto = '1';
                }
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
                
                try {
                    const response = await fetch('/perusahaan-rak.json');
                    const data = await response.json();
                    
                    for (const key in data) {
                        if (key.toLowerCase() === name.toLowerCase()) {
                            return data[key];
                        }
                    }
                    return [];
                } catch (e) {
                    console.error('Failed to fetch rak data:', e);
                    return [];
                }
            }

            function applyOptions(codes) {
                const current = (rakSelect.getAttribute('data-current') || '').trim();
                const keep = rakSelect.value || current;
                const manualInput = document.getElementById('posisi_rak_manual');

                if (codes.length === 0) {
                    rakSelect.classList.add('hidden');
                    manualInput.classList.remove('hidden');
                    manualInput.name = 'posisi_rak';
                    rakSelect.name = 'posisi_rak_old';
                } else {
                    rakSelect.classList.remove('hidden');
                    manualInput.classList.add('hidden');
                    manualInput.name = 'posisi_rak_manual';
                    rakSelect.name = 'posisi_rak';
                    
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
                            opt.textContent = keep;
                            rakSelect.appendChild(opt);
                            rakSelect.value = keep;
                        }
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
