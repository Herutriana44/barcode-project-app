<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Hasil Scan - Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-xl mx-auto w-full space-y-6">

            <div class="p-4 rounded-lg border border-green-300 bg-green-50 text-green-900 text-base" role="alert">
                <p class="font-semibold">Sesi karyawan aktif</p>
                <p class="mt-1 text-sm">
                    Scan masuk: {{ session('active_employee_scanned_at') ? \Carbon\Carbon::parse(session('active_employee_scanned_at'))->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}
                </p>
            </div>

            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <div class="flex items-center gap-6 mb-6">
                    @if ($employee->photoPublicUrl())
                        <img src="{{ $employee->photoPublicUrl() }}" alt="Foto {{ $employee->name }}"
                             class="w-20 h-20 rounded-full object-cover border-2 border-egg-300 shrink-0" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-egg-200 flex items-center justify-center text-3xl font-bold text-egg-600 shrink-0">
                            {{ mb_strtoupper(mb_substr($employee->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-2xl font-bold text-egg-900">{{ $employee->name }}</h3>
                        <p class="text-egg-600">NIP: {{ $employee->nip }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-base">
                    <div><span class="font-medium">Jabatan:</span> {{ $employee->jabatan ?? '-' }}</div>
                    <div><span class="font-medium">Departemen:</span> {{ $employee->departemen ?? '-' }}</div>
                    <div><span class="font-medium">Status:</span> {{ $employee->status ?? '-' }}</div>
                </div>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('scan.index') }}" class="btn-egg-primary">Lanjut Scan Barang</a>
                    <a href="{{ route('dashboard') }}" class="btn-egg-secondary">Dashboard</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
