<?php

namespace Database\Seeders;

use App\Models\Dropoff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DropoffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = ['玄関', '車庫', '自転車', 'その他'];

        foreach ($places as $place) {
            Dropoff::create([
                'name' => $place
            ]);
        }
    }
}
