<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Ubah Nama Perusahaan') }-+}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-3xl mx-auto w-full">
            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl">
                <form action="{{ route('company-barcodes.update', $companyBarcode) }}" method="POST" class="p-6 md:p-8 space-y-6 text-base">
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
                            class="mt-1 block w-full rounded-md border-egg-300 shadow-sm focus:border-egg-500 focus:ring-egg-500"
                        />
                        @error('company_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-egg-600 text-white px-6 py-2 rounded-md font-medium hover:bg-egg-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
