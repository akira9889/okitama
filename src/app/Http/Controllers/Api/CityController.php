<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Validation\ValidationException;

class CityController extends Controller
{
    public function fetchCitiesByPrefectureId(Request $request)
    {
        try {
            $validated = $request->validate([
                'prefecture_id' => 'required|integer|exists:prefectures,id',
            ]);

            $prefecture_id = str_pad($validated['prefecture_id'], 2, 0, STR_PAD_LEFT);
            $url = 'https://www.land.mlit.go.jp/webland/api/CitySearch?area=' . $prefecture_id;

            $method = "GET";
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
