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
                'city_id' => 1,
                'prefecture_id' => 12
            ],
            [
                'main_address_id' => 2,
                'city_id' => 1,
                'prefecture_id' => 12
            ],
            [
                'main_address_id' => 4,
                'city_id' => 2,
                'prefecture_id' => 12
            ]
        ];
        
        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
