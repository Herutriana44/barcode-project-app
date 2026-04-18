<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">Tambah karyawan</h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-xl mx-auto w-full">
            <div class="bg-white border border-egg-200 rounded-xl p-6 md:p-8 shadow-md">
                <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="space-y-5 text-base">
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
