<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CityService;

class CityController extends Controller
{
    public function fetchCitiesByPrefectureId(Request $request)
    {
            $validated = $request->validate([
                'prefecture_id' => 'required|integer|exists:prefectures,id',
            ]);

            $cities = CityService::fetchCitiesByPrefectureId($validated['prefecture_id']);

            return $cities;
    }
}
