<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-egg-900 leading-tight">
            {{ __('Hasil Scan - Barcode Unique Item') }}
        </h2>
    </x-slot>

    <div class="py-8 w-full">
        <div class="max-w-4xl mx-auto w-full space-y-6">
            @if (session('success'))
                <p class="p-3 text-sm bg-green-50 border border-green-200 rounded text-green-900">{{ session('success') }}</p>
            @endif

            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-base">
                    <div><span class="font-medium">Customer:</span> {{ $uniqueItem->item->customer ?? '-' }}</div>
                    <div><span class="font-medium">Part Name:</span> {{ $uniqueItem->item->part_name ?? '-' }}</div>
                    <div><span class="font-medium">Part Number:</span> {{ $uniqueItem->item->part_number ?? '-' }}</div>
                    <div><span class="font-medium">Qty:</span> {{ $uniqueItem->qty ?? 0 }} Pcs</div>
                    <div><span class="font-medium">Unique ID:</span> {{ $uniqueItem->id }}</div>
                    <div><span class="font-medium">Code:</span> {{ $uniqueItem->item->code ?? '-' }}</div>
                    <div><span class="font-medium">Jumlah Box :</span> 1 </div>
                </div>

                <div class="mt-8 border-t border-egg-200 pt-6">
                    <h3 class="text-lg font-bold text-egg-900 mb-3">Mutasi stok (Unique Item)</h3>
                    <form action="{{ route('scan.movement', ['barcode_id' => 'IB-'.$uniqueItem->item->id.'-'.$uniqueItem->item->itemReceivings()->first()?->id.'-'.$uniqueItem->id]) }}" method="POST" class="space-y-4 max-w-md">
                        @csrf
                        <fieldset>
                            <legend class="text-sm font-medium text-egg-800 mb-2">Arah</legend>
                            <div class="flex flex-wrap gap-4 text-base">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="direction" value="out" class="rounded-full border-egg-300 text-egg-700 focus:ring-egg-500" required />
                                    Barang keluar
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="direction" value="in" class="rounded-full border-egg-300 text-egg-700 focus:ring-egg-500" />
                                    Barang masuk
                                </label>
                            </div>
                        </fieldset>
                        <input type="hidden" name="qty" value="1">
                        <button type="submit" class="w-full btn-egg-primary">Konfirmasi</button>
                    </form>

                <div class="mt-6">
                    <a href="{{ route('scan.index') }}" class="link-egg inline-flex items-center text-base lg:text-lg">← Scan lagi</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
