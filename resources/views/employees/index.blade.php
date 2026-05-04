<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-bold text-3xl text-egg-900 leading-tight">Karyawan</h2>
            <a href="{{ route('employees.create') }}" class="btn-egg-primary">Tambah</a>
        </div>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-5xl mx-auto w-full">
            @if (session('success'))
                <p class="mb-2 p-2 text-sm bg-egg-100 border border-egg-300 rounded text-egg-900">{{ session('success') }}</p>
            @endif
            <div class="bg-white border border-egg-200 rounded overflow-hidden">
                <table class="min-w-full text-base">
                    <thead class="bg-egg-50 text-egg-800">
                        <tr>
                            <th class="text-left py-3 px-3 font-semibold">Nama</th>
                            <th class="text-left py-3 px-3 font-semibold">NIP</th>
                            <th class="text-left py-3 px-3 font-semibold">Departemen</th>
                            <th class="text-left py-3 px-3 font-semibold">Jabatan</th>
                            <th class="text-left py-3 px-3 font-semibold">Status</th>
                            <th class="text-right py-3 px-3 font-semibold w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-egg-200">
                        @forelse($employees as $e)
                            <tr>
                                <td class="py-3 px-3">{{ $e->name }}</td>
                                <td class="py-3 px-3 font-mono text-sm">{{ $e->nip }}</td>
                                <td class="py-3 px-3">{{ $e->departemen ?? '—' }}</td>
                                <td class="py-3 px-3">{{ $e->jabatan ?? '—' }}</td>
                                <td class="py-3 px-3">{{ $e->status ?? '—' }}</td>
                                <td class="py-3 px-3 text-right space-x-2">
                                    <a href="{{ route('employees.show', $e) }}" class="link-egg">Detail</a>
                                    <a href="{{ route('employees.id-card', $e) }}" target="_blank" class="link-egg">ID card</a>
                                    <a href="{{ route('employees.edit', $e) }}" class="link-egg">Edit</a>
                                    <form action="{{ route('employees.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 px-2 text-center text-egg-600">Belum ada karyawan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-2">{{ $employees->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
