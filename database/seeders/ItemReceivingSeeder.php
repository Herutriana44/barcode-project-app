<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemReceiving;
use Illuminate\Database\Seeder;

class ItemReceivingSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::whereDoesntHave('itemReceivings')->get();

        foreach ($items as $item) {
            ItemReceiving::create([
                'item_id' => $item->id,
                'transfer_slip_no' => 'TS-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                'tanggal_terima_fg' => now()->subDays(rand(1, 10)),
                'jumlah_box' => rand(1, 5),
            ]);
        }
    }
}
