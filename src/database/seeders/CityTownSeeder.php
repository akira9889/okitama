<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CityTownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prefectures = [
            12 => '千葉県',
            13 => '東京都'
        ];

        $cities = [
            12204 => '船橋市',
            12216 => '習志野市',
            13123 => '江戸川区'
        ];

        $area = [
            '千葉県' => [
                '船橋市' => ['西船橋', '田喜野井', '習志野', '高根台'],
                '習志野市' => ['茜浜', '秋津', '香澄', '東習志野', '大久保', '花咲', '鷺沼', '津田沼'],
                '美浜区' => ['豊砂', '中瀬', '浜田']
            ],
            '東京都' => [
                '江戸川区' => ['東葛西', '篠崎町', '瑞江', '船堀'],
            ]
        ];

        foreach ($area as $prefecture_name => $city_data) {
            $prefecture_id = array_search($prefecture_name, $prefectures);
            foreach ($city_data as $city_name => $towns) {
                $city_id = array_search($city_name, $cities);

                DB::table('cities')->insert([
                    'id' => $city_id,
                    'name' => $city_name,
                    'prefecture_id' => $prefecture_id,
                ]);

                foreach ($towns as $town_name) {
                    DB::table('towns')->insert([
                        'name' => $town_name,
                        'city_id' => $city_id,
                    ]);
                }
            }
        }

    }
}
