<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Perusahaan & Rak') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 text-green-600 font-bold">{{ session('success') }}</div>
                @endif
                
                <form action="{{ route('perusahaan-rak.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <textarea name="data" rows="20" class="w-full border-gray-300 rounded-md shadow-sm">{{ json_encode($data, JSON_PRETTY_PRINT) }}</textarea>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update JSON</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
