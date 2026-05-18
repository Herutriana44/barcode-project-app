<?php

namespace Database\Seeders;

use App\Models\Rak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RakDataSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('perusahaan-rak.json');
        if (!File::exists($path)) {
            return;
        }

        $data = json_decode(File::get($path), true);
        
        foreach ($data as $companyName => $codes) {
            $codeString = implode(', ', $codes);
            
            Rak::updateOrCreate(
                ['company_name' => $companyName],
                ['code' => $codeString]
            );
        }
    }
}
