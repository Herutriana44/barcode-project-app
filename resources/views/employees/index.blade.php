<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-lg text-egg-900 leading-tight">Karyawan</h2>
            <a href="{{ route('employees.create') }}" class="btn-egg-primary">Tambah</a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-5xl mx-auto px-2 sm:px-4">
            @if (session('success'))
                <p class="mb-2 p-2 text-sm bg-egg-100 border border-egg-300 rounded text-egg-900">{{ session('success') }}</p>
            @endif
            <div class="bg-white border border-egg-200 rounded overflow-hidden">
                <table class="min-w-full text-xs">
                    <thead class="bg-egg-50 text-egg-800">
                        <tr>
                            <th class="text-left py-1.5 px-2 font-semibold">Nama</th>
                            <th class="text-left py-1.5 px-2 font-semibold">NIP</th>
                            <th class="text-left py-1.5 px-2 font-semibold">Telepon</th>
                            <th class="text-right py-1.5 px-2 font-semibold w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-egg-200">
                        @forelse($employees as $e)
                            <tr>
                                <td class="py-1.5 px-2">{{ $e->name }}</td>
                                <td class="py-1.5 px-2">{{ $e->nip ?? '—' }}</td>
                                <td class="py-1.5 px-2">{{ $e->phone ?? '—' }}</td>
                                <td class="py-1.5 px-2 text-right space-x-2">
                                    <a href="{{ route('employees.edit', $e) }}" class="link-egg">Edit</a>
                                    <form action="{{ route('employees.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 px-2 text-center text-egg-600">Belum ada karyawan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-2">{{ $employees->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
