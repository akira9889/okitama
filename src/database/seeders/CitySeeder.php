<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            12204 => '船橋市',
            12216 => '習志野市',
            13123 => '江戸川区',
        ];

        foreach ($cities as $id => $city) {
            City::create([
                'id' => $id,
                'name' => $city
            ]);
        }
    }
}
