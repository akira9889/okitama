<?php

namespace Database\Seeders;

use App\Models\MainAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainAddresses = ['田喜野井', '習志野', '高根台', '茜浜', '東葛西'];

        foreach ($mainAddresses as $address) {
            MainAddress::create([
                'name' => $address
            ]);
        }
    }
}
