<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-egg-900 leading-tight">Tambah karyawan</h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-lg mx-auto px-2 sm:px-4">
            <div class="bg-white border border-egg-200 rounded p-3">
                <form method="POST" action="{{ route('employees.store') }}" class="space-y-3 text-sm">
                    @csrf
                    @include('employees.partials.form', ['employee' => null])
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn-egg-primary">Simpan</button>
                        <a href="{{ route('employees.index') }}" class="btn-egg-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
