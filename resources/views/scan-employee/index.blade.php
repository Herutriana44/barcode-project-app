<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Scan Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-2xl mx-auto w-full space-y-6">

            @if (session('warning'))
                <div class="p-4 rounded-lg border border-amber-400 bg-amber-50 text-amber-900 text-base" role="alert">
                    {{ session('warning') }}
                </div>
            @endif
            @if (session('success'))
                <p class="p-3 text-sm bg-green-50 border border-green-200 rounded text-green-900">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p class="p-3 text-sm bg-red-50 border border-red-200 rounded text-red-900">{{ session('error') }}</p>
            @endif

            {{-- Karyawan aktif saat ini --}}
            @if ($activeEmployee)
                <div class="bg-white border border-green-300 shadow-sm sm:rounded-xl p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    @if ($activeEmployee->photoPublicUrl())
                        <img src="{{ $activeEmployee->photoPublicUrl() }}" alt="Foto {{ $activeEmployee->name }}"
                             class="w-16 h-16 rounded-full object-cover border-2 border-green-400 shrink-0" />
                    @else
                        <div class="w-16 h-16 rounded-full bg-egg-200 flex items-center justify-center text-2xl font-bold text-egg-600 shrink-0">
                            {{ mb_strtoupper(mb_substr($activeEmployee->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-green-700 mb-0.5">Karyawan Aktif</p>
                        <p class="text-xl font-bold text-egg-900 truncate">{{ $activeEmployee->name }}</p>
                        <p class="text-sm text-egg-600">NIP: {{ $activeEmployee->nip }}
                            @if ($activeEmployee->jabatan) · {{ $activeEmployee->jabatan }} @endif
                            @if ($activeEmployee->departemen) · {{ $activeEmployee->departemen }} @endif
                        </p>
                        <p class="text-xs text-egg-500 mt-1">
                            Scan masuk: {{ session('active_employee_scanned_at') ? \Carbon\Carbon::parse(session('active_employee_scanned_at'))->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('scan-employee.destroy') }}" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 text-sm font-medium transition"
                                onclick="return confirm('Akhiri sesi karyawan ini?')">
                            Akhiri Sesi
                        </button>
                    </form>
                </div>
            @endif

            {{-- Form scan / input manual --}}
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8 space-y-8">

                {{-- Kamera --}}
                <div>
                    <h3 class="text-xl font-bold text-egg-900 mb-3">Scan Badge dengan Kamera</h3>
                    <div id="emp-reader" class="w-full border-2 border-egg-200 rounded-xl overflow-hidden" style="min-height:300px;"></div>
                    <p class="text-sm text-egg-600 mt-2">Arahkan kamera ke barcode atau QR pada badge karyawan.</p>
                </div>

                <div class="border-t border-egg-100"></div>

                {{-- Input manual --}}
                <div>
                    <h3 class="text-xl font-bold text-egg-900 mb-3">Input Manual</h3>
                    <form method="POST" action="{{ route('scan-employee.store') }}" class="flex flex-col sm:flex-row gap-3 items-stretch">
                        @csrf
                        <input type="text" name="badge_code" id="badge_code"
                               placeholder="NIP atau EMP-{nip}"
                               value="{{ old('badge_code') }}"
                               autofocus
                               class="flex-1 rounded-lg border-egg-300 py-3 px-4 text-base shadow-sm focus:border-egg-500 focus:ring-egg-500 @error('badge_code') border-red-400 @enderror" />
                        <button type="submit" class="btn-egg-primary shrink-0">Scan Masuk</button>
                    </form>
                    @error('badge_code')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-egg-600 mt-2">Ketik NIP karyawan atau kode badge <span class="font-mono">EMP-{nip}</span>, lalu klik Scan Masuk.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
    (function () {
        const input = document.getElementById('badge_code');
        let scanner = null;

        function submitCode(code) {
            // Isi input dan submit form
            input.value = code;
            input.closest('form') ? input.closest('form').submit() : null;
        }

        function startScanner() {
            scanner = new Html5Qrcode('emp-reader');
            Html5Qrcode.getCameras().then(cameras => {
                if (!cameras || cameras.length === 0) return;
                const camId = cameras[cameras.length - 1].id; // prefer back camera
                scanner.start(
                    camId,
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        scanner.stop().catch(() => {});
                        // Ekstrak NIP dari URL scan jika perlu
                        let code = decodedText.trim();
                        const match = code.match(/\/scan\/(EMP-[^/?#]+)/);
                        if (match) code = match[1];
                        submitCode(code);
                    },
                    () => {}
                ).catch(() => {});
            }).catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    })();
    </script>
    @endpush
</x-app-layout>
