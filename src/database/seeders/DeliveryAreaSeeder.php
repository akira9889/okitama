<?php

namespace Database\Seeders;

use App\Models\DeliveryArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $town_ids = [1, 2, 3, 4, 5];

        foreach($town_ids as $town_id)
        DeliveryArea::create([
            'user_id' => 1,
            'town_id' => $town_id
        ]);
    }
}
