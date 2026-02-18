<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create(['name' => 'PT. ABC Indonesia']);
        Company::create(['name' => 'CV. XYZ Manufacturing']);
        Company::create(['name' => 'UD. Sumber Maju']);
    }
}
