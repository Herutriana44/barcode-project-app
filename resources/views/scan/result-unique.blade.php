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
            @if (session('error'))
                <p class="p-3 text-sm bg-red-50 border border-red-200 rounded text-red-900">{{ session('error') }}</p>
            @endif

            @if ($expiredWarning ?? false)
                <div class="p-4 rounded-lg border border-red-300 bg-red-50 text-red-900 text-base leading-snug" role="alert">
                    <p class="font-semibold">Peringatan: Barang Expired!</p>
                    <p class="mt-1">Barang ini sudah melewati masa expired. Harap lakukan pemeriksaan lebih lanjut.</p>
                </div>
            @elseif ($approachingExpiry ?? false)
                <div class="p-4 rounded-lg border border-orange-300 bg-orange-50 text-orange-900 text-base leading-snug" role="alert">
                    <p class="font-semibold">Peringatan: Barang Hampir Expired!</p>
                    <p class="mt-1">Barang ini akan expired dalam 30 hari ke depan. Disarankan untuk segera dikeluarkan (FIFO).</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md border border-egg-200 sm:rounded-xl p-8">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-base">
                    <div><span class="font-medium">Customer:</span> {{ $uniqueItem->item->customer ?? '-' }}</div>
                    <div><span class="font-medium">Part Name:</span> {{ $uniqueItem->item->part_name ?? '-' }}</div>
                    <div><span class="font-medium">Part Number:</span> {{ $uniqueItem->item->part_number ?? '-' }}</div>
                    <div><span class="font-medium">Qty:</span> {{ $uniqueItem->qty ?? 0 }} Pcs</div>
                    <div><span class="font-medium">Unique ID:</span> {{ $uniqueItem->id }}</div>
                    <div><span class="font-medium">Code:</span> {{ $uniqueItem->item->code ?? '-' }}</div>
                    <div><span class="font-medium">Jumlah Box Pecahan :</span> 1 </div>
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

            @if(isset($expiringList) && ($expiringList['items']->isNotEmpty() || $expiringList['uniqueItems']->isNotEmpty()))
                <div class="bg-white overflow-hidden shadow-md border border-orange-200 sm:rounded-xl p-8">
                    <h3 class="text-lg font-bold text-orange-900 mb-4">Peringatan Expired Lainnya:</h3>
                    <ul class="list-disc list-inside space-y-2">
                        @foreach($expiringList['items'] as $expItem)
                            <li>
                                <span class="font-medium">{{ $expItem->part_name }}</span> ({{ $expItem->tgl_expired->format('d/m/Y') }})
                            </li>
                        @endforeach
                        @foreach($expiringList['uniqueItems'] as $expUnique)
                            <li>
                                <span class="font-medium">{{ $expUnique->item->part_name ?? 'Unique Item' }}</span> ({{ $expUnique->expired_date->format('d/m/Y') }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
