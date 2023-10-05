<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'main_address_id' => 1,
                'city_id' => 12204,
                'prefecture_id' => 12
            ],
            [
                'main_address_id' => 2,
                'city_id' => 12204,
                'prefecture_id' => 12
            ],
            [
                'main_address_id' => 4,
                'city_id' => 12216,
                'prefecture_id' => 12
            ],
            [
                'main_address_id' => 5,
                'city_id' => 13123,
                'prefecture_id' => 13
            ]
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
