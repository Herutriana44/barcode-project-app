<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Scan Karyawan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center space-x-6">
                    @if($employee->photoPublicUrl())
                        <img src="{{ $employee->photoPublicUrl() }}" alt="{{ $employee->name }}" class="w-32 h-32 rounded-full object-cover">
                    @else
                        <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                            No Photo
                        </div>
                    @endif
                    <div>
                        <h3 class="text-2xl font-bold">{{ $employee->name }}</h3>
                        <p class="text-gray-600">NIP: {{ $employee->nip }}</p>
                        <p class="text-gray-600">Departemen: {{ $employee->departemen ?? '-' }}</p>
                        <p class="text-gray-600">Jabatan: {{ $employee->jabatan ?? '-' }}</p>
                        <p class="text-gray-600">Status: {{ $employee->status ?? '-' }}</p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Kembali ke Scan
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
