<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            EmployeeSeeder::class,
            ItemSeeder::class,
            ItemReceivingSeeder::class,
            Fqc038CleanListPartSeeder::class,
            RakSeeder::class,
        ]);
    }
}
