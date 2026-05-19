<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Edit Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white shadow-sm border border-egg-200 sm:rounded-xl p-8">
                <form action="{{ route('activity-logs.update', $activityLog->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-egg-700">Deskripsi</label>
                        <textarea name="details" rows="5" class="mt-1 block w-full rounded-lg border-egg-300 shadow-sm focus:ring-egg-500 focus:border-egg-500">{{ old('details', $activityLog->details) }}</textarea>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="btn-egg-primary">Simpan Perubahan</button>
                        <a href="{{ route('activity-logs.index') }}" class="btn-egg-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>