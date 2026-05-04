<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">Profil karyawan</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employees.id-card', $employee) }}" target="_blank" class="btn-egg-secondary">Cetak ID card</a>
                <a href="{{ route('employees.edit', $employee) }}" class="btn-egg-primary">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-4xl mx-auto w-full space-y-6">
            @if (session('success'))
                <p class="p-2 text-sm bg-egg-100 border border-egg-300 rounded text-egg-900">{{ session('success') }}</p>
            @endif

            <div class="bg-white border border-egg-200 rounded-xl shadow-md overflow-hidden">
                <div class="p-6 md:p-8 flex flex-col md:flex-row gap-8">
                    <div class="shrink-0">
                        @if ($employee->photoPublicUrl())
                            <img src="{{ $employee->photoPublicUrl() }}" alt="{{ $employee->name }}"
                                class="w-40 h-40 object-cover rounded-xl border border-egg-200 shadow-sm" />
                        @else
                            <div class="w-40 h-40 rounded-xl border border-dashed border-egg-300 bg-egg-50 flex items-center justify-center text-egg-500 text-sm text-center px-2">
                                Belum ada foto
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-2 text-base">
                        <h3 class="text-2xl font-bold text-egg-900">{{ $employee->name }}</h3>
                        <p><span class="font-medium text-egg-800">NIP:</span> {{ $employee->nip }}</p>
                        <p><span class="font-medium text-egg-800">Departemen:</span> {{ $employee->departemen ?? '—' }}</p>
                        <p><span class="font-medium text-egg-800">Jabatan:</span> {{ $employee->jabatan ?? '—' }}</p>
                        <p><span class="font-medium text-egg-800">Status:</span> {{ $employee->status ?? '—' }}</p>
                        <p class="pt-2 text-sm text-egg-600 break-all">
                            <span class="font-medium text-egg-800">Tautan profil:</span><br />
                            <a href="{{ $profileUrl }}" class="link-egg">{{ $profileUrl }}</a>
                        </p>
                    </div>
                </div>

                <div class="border-t border-egg-200 bg-egg-50 p-6 md:p-8">
                    <h4 class="text-lg font-semibold text-egg-900 mb-4">Barcode &amp; QR (arahkan ke halaman ini)</h4>
                    <div class="flex flex-wrap gap-8 items-end">
                        <div class="bg-white p-3 rounded-lg border border-egg-200 shadow-sm">
                            {!! $qrSvg !!}
                            <p class="text-xs text-egg-600 mt-2 text-center">QR</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-egg-200 shadow-sm overflow-x-auto max-w-full">
                            {!! $barcodeSvg !!}
                            <p class="text-xs text-egg-600 mt-2 text-center">Code 128</p>
                        </div>
                    </div>
                </div>
            </div>

            <p>
                <a href="{{ route('employees.index') }}" class="link-egg">← Daftar karyawan</a>
            </p>
        </div>
    </div>
</x-app-layout>
