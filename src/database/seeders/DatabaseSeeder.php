<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(PrefectureSeeder::class);
        $this->call(CityTownSeeder::class);
        $this->call(DropoffSeeder::class);
        $this->call(DeliveryAreaSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(CustomerDropoffSeeder::class);
    }
}
