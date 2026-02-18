<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $companies = \App\Models\Company::all();
        if ($companies->isEmpty()) {
            return;
        }

        $items = [
            [
                'customer' => 'Customer A',
                'part_name' => 'Bracket Assembly',
                'part_number' => 'BRK-001',
                'model' => 'Model X',
                'berat' => 2.5,
                'qty' => 100,
                'inspector_name' => 'Budi Santoso',
                'tgl_produksi' => now()->subDays(30),
                'tgl_expired' => now()->addYear(),
                'code' => 'BRK-001-X-001',
                'posisi_rak' => 'A',
                'tingkat' => '1',
                'ukuran_material' => '100x50mm',
                'jenis_bahan' => 'SPCC',
                'quantity_material' => 50,
                'no_surat_jalan_material' => 'SJ-2024-001',
                'tanggal_terima_material' => now()->subDays(35),
            ],
            [
                'customer' => 'Customer B',
                'part_name' => 'Shaft Component',
                'part_number' => 'SFT-002',
                'model' => 'Model Y',
                'berat' => 1.2,
                'qty' => 200,
                'inspector_name' => 'Budi Santoso',
                'tgl_produksi' => now()->subDays(15),
                'tgl_expired' => now()->addMonths(6),
                'code' => 'SFT-002-Y-002',
                'posisi_rak' => 'B',
                'tingkat' => '2',
                'ukuran_material' => '80x40mm',
                'jenis_bahan' => 'SESE',
                'quantity_material' => 100,
                'no_surat_jalan_material' => 'SJ-2024-002',
                'tanggal_terima_material' => now()->subDays(20),
            ],
            [
                'customer' => 'Customer A',
                'part_name' => 'Bearing Housing',
                'part_number' => 'BRG-003',
                'model' => 'Model X',
                'berat' => 3.0,
                'qty' => 50,
                'inspector_name' => 'Budi Santoso',
                'tgl_produksi' => now()->subDays(7),
                'tgl_expired' => now()->addYear(),
                'code' => 'BRG-003-X-003',
                'posisi_rak' => 'A',
                'tingkat' => '3',
                'ukuran_material' => '120x60mm',
                'jenis_bahan' => 'SPCC',
                'quantity_material' => 50,
                'no_surat_jalan_material' => 'SJ-2024-003',
                'tanggal_terima_material' => now()->subDays(10),
            ],
        ];

        foreach ($companies as $company) {
            foreach ($items as $itemData) {
                Item::create(array_merge($itemData, ['company_id' => $company->id]));
            }
        }
    }
}
