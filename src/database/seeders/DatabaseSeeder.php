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
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'last_name' => 'テスト',
            'email' => 'test@example.com',
        ]);

        $this->call(PrefectureSeeder::class);
        $this->call(CityTownSeeder::class);
        $this->call(DropoffSeeder::class);
        $this->call(DeliveryAreaSeeder::class);
    }
}
