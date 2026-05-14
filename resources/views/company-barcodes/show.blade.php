<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">
                {{ __('Detail Barcode Perusahaan') }}
            </h2>
            <div class="flex flex-wrap gap-2 justify-end">
                <button type="button" onclick="window.print()" class="btn-egg-primary">Print</button>
                <a href="{{ route('company-barcodes.edit', $companyBarcode) }}" class="btn-egg-primary">Ubah</a>
                <form action="{{ route('company-barcodes.destroy', $companyBarcode) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Hapus seluruh data perusahaan ini, semua barcode perusahaan, dan barang terkait? Tindakan tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-egg-secondary text-red-700 border-red-200 hover:bg-red-50">Hapus</button>
                </form>
                <a href="{{ route('company-barcodes.create') }}" class="btn-egg-primary">Buat Baru</a>
                <a href="{{ route('company-barcodes.index') }}" class="btn-egg-secondary">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-5xl mx-auto w-full">
            @if (session('success'))
                <p class="mb-4 p-2 text-sm bg-egg-100 border border-egg-300 rounded text-egg-900">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p class="mb-4 p-2 text-sm bg-red-50 border border-red-200 rounded text-red-800">{{ session('error') }}</p>
            @endif
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl p-6 print:shadow-none">
                <div class="border border-egg-200 p-6 rounded-xl">
                    <div class="flex flex-col sm:flex-row flex-wrap items-start justify-center gap-8 mb-6 print:flex-row print:gap-6">
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Barcode (Code 128)</span>
                            <p class="text-xs text-egg-600 text-center mb-2 max-w-md">Isi: URL scan — pemindai garis mengirim tautan ke halaman ini.</p>
                            <div class="barcode-container overflow-x-auto max-w-full">
                                {!! $barcodeSvg !!}
                            </div>
                        </div>
                        <div class="flex flex-col items-center w-full sm:w-auto">
                            <span class="text-sm font-semibold text-egg-700 mb-2 uppercase tracking-wide">Kode QR</span>
                            <p class="text-xs text-egg-600 text-center mb-2 max-w-md">Isi: URL yang sama — dibuka di browser ponsel.</p>
                            <div class="qr-container w-[220px] max-w-full flex justify-center [&_svg]:max-w-full [&_svg]:h-auto">
                                {!! $qrCodeSvg !!}
                            </div>
                            <a href="{{ route('company-barcodes.download-qr', $companyBarcode) }}" class="btn-egg-secondary mt-2 text-sm">Download QR (PNG)</a>
                        </div>
                    </div>
                    <div class="no-print mb-6 max-w-2xl mx-auto">
                        <p class="text-xs font-medium text-egg-700 mb-1">Tautan scan (salin untuk dibagikan)</p>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" readonly id="company-scan-url" value="{{ $scanUrl }}" class="flex-1 rounded-lg border-egg-300 py-2 px-3 text-sm font-mono bg-egg-50 text-egg-900">
                            <button type="button" class="btn-egg-secondary shrink-0 text-sm" data-copy-target="company-scan-url">Salin URL</button>
                        </div>
                    </div>
                    <p class="text-center text-lg font-mono mb-4 font-medium text-egg-900">{{ $companyBarcode->barcode_id }}</p>

                    <div class="py-8 w-full">
        <div class="max-w-5xl mx-auto w-full">
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <h3 class="font-bold text-2xl text-egg-900 mb-6">{{ $companyBarcode->company->name }}</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-base">
                        <thead>
                            <tr class="border-b border-egg-200">
                            <th class="text-left py-3 px-2 font-semibold">Company</th>
                            <th class="text-left py-3 px-2 font-semibold">Part Name</th>
                            <th class="text-left py-3 px-2 font-semibold">Barcode</th>
                            <th class="text-left py-3 px-2 font-semibold">Model</th>
                            <th class="text-left py-3 px-2 font-semibold">Berat (Kg)</th>
                            <th class="text-left py-3 px-2 font-semibold">Qty</th>
                            <th class="text-left py-3 px-2 font-semibold">Posisi Rak</th>
                            <!-- <th class="text-left py-3 px-2 font-semibold">Tingkat</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($companyBarcode->company->items as $item)
                            <tr class="border-b border-egg-100">
                                <td class="py-3 px-2">{{ $item->customer ?? '-' }}</td>
                                <td class="py-3 px-2">
                                    @if($item->itemBarcodes->isNotEmpty())
                                        <a href="{{ route('scan.show', $item->itemBarcodes->first()->barcode_id) }}" class="text-blue-600 hover:underline">{{ $item->part_name ?? '-' }}</a>
                                    @else
                                        {{ $item->part_name ?? '-' }}
                                    @endif
                                </td>
                                <td class="py-3 px-2">{{ $item->code ?? '-' }}</td>
                                <td class="py-3 px-2">{{ $item->model ?? '-' }}</td>
                                <td class="py-3 px-2">{{ $item->berat !== null ? $item->berat : '-' }}</td>
                                <td class="py-3 px-2">{{ $item->dynamic_qty ?? $item->qty ?? '-' }}</td>
                                <td class="py-3 px-2">{{ $item->posisi_rak ?? '-' }}</td>
                                <!-- <td class="py-3 px-2">{{ $item->tingkat ?? '-' }}</td> -->
                            </tr>                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="link-egg inline-flex items-center text-base lg:text-lg">← Scan lagi</a>
                </div>
            </div>
        </div>
    </div>

                    <!-- <div class="overflow-x-auto">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-base">
                        <thead>
                            <tr class="border-b border-egg-200">
                                <th class="text-left py-3 px-2 font-semibold">Nama</th>
                                <th class="text-left py-3 px-2 font-semibold">Qty</th>
                                <th class="text-left py-3 px-2 font-semibold">Rak</th>
                                <th class="text-left py-3 px-2 font-semibold">Tingkat</th>
                                <th class="text-left py-3 px-2 font-semibold">Op. mobil</th>
                                <th class="text-left py-3 px-2 font-semibold">Pengirim</th>
                                <th class="text-left py-3 px-2 font-semibold">Op. forklift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companyBarcode->company->companyItems as $ci)
                                <tr class="border-b border-egg-100">
                                    <td class="py-3 px-2">{{ $ci->item->part_name ?? $ci->item->part_number ?? 'Item #'.$ci->item->id }}</td>
                                    <td class="py-3 px-2">{{ $ci->qty }}</td>
                                    <td class="py-3 px-2">{{ $ci->posisi_rak ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->tingkat ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->item->operatorMobil->name ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->item->pengirim->name ?? '-' }}</td>
                                    <td class="py-3 px-2">{{ $ci->item->operatorForklift->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.querySelectorAll('[data-copy-target]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var id = btn.getAttribute('data-copy-target');
                    var el = document.getElementById(id);
                    if (!el || !navigator.clipboard) return;
                    navigator.clipboard.writeText(el.value).then(function () {
                        var t = btn.textContent;
                        btn.textContent = 'Disalin';
                        setTimeout(function () { btn.textContent = t; }, 1500);
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
