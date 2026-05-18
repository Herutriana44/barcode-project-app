<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Log Aktivitas Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="bg-white shadow-sm border border-egg-200 sm:rounded-xl overflow-x-auto">
                <table class="w-full text-sm text-left min-w-[800px]">
                    <thead class="text-xs text-egg-700 uppercase bg-egg-50">
                        <tr>
                            <th class="px-6 py-4">NIP</th>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Departemen</th>
                            <th class="px-6 py-4">Jabatan</th>
                            <th class="px-6 py-4">Di</th>
                            <th class="px-6 py-4">Activity</th>
                            <th class="px-6 py-4">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-egg-200">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-6 py-4 font-mono text-egg-600">
                                    @if($log->employee)
                                        <a href="{{ route('employees.show', $log->employee->id) }}" class="text-egg-700 hover:text-egg-900 underline">
                                            {{ $log->employee->nip }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $log->employee->name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $log->employee->departemen ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $log->employee->jabatan ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $log->target_type }}</td>
                                <td class="px-6 py-4">{{ $log->activity }}</td>
                                <td class="px-6 py-4 max-w-xs truncate" title="{{ $log->details }}">{{ $log->details }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-egg-500">Belum ada log aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-egg-100">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>