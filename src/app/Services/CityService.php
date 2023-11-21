<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Validation\ValidationException;

class CityService
{
    public static function fetchCitiesByPrefectureId(int $prefecture_id)
    {
        try {

        $prefecture_id = str_pad($prefecture_id, 2, 0, STR_PAD_LEFT);
        $url = 'https://www.land.mlit.go.jp/webland/api/CitySearch?area=' . $prefecture_id;

        $method = "POST";
        $client = new Client();

        $response = $client->request($method, $url);

        $cities = $response->getBody();
        $cities = json_decode($cities, true);

        return $cities;
        } catch (ValidationException $e) {;
            return response()->json(['error' => 'Invalid Prefecture ID'], 400);
        }
    }
}
