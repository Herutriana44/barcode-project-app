<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            EmployeeSeeder::class,
            ItemReceivingSeeder::class,
            Fqc038CleanListPartSeeder::class,
            RakSeeder::class,
        ]);
    }
}
