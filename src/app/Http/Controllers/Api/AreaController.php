<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AreaRequest;
use App\Models\City;
use App\Models\Town;
use App\Models\DeliveryArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = Town::join('cities', 'towns.city_id', '=', 'cities.id')
            ->join('prefectures', 'cities.prefecture_id', '=', 'prefectures.id')
            ->select('towns.*', 'cities.name as city_name', 'cities.prefecture_id', 'prefectures.name as prefecture_name')
            ->orderBy('prefectures.id', 'asc')
            ->orderBy('cities.id', 'asc')
            ->orderBy('towns.id', 'asc')
            ->get();

        $area = $results->groupBy([
            'prefecture_name',
            'city_name'
        ])->map(function ($prefectureData) {
            return $prefectureData->map(function ($cityData) {
                return $cityData->pluck('name', 'id');
            });
        });

        return $area;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AreaRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $exits = City::where('id', $data['city_id'])->exists();

            if (!$exits) {
                City::create([
                    'id' => $data['city_id'],
                    'name' => $data['city_name'],
                    'prefecture_id' => $data['prefecture_id']
                ]);
            }

            $town = Town::create([
                'name' => $data['town_name'],
                'city_id' => $data['city_id']
            ]);

            DeliveryArea::create([
                'user_id' => $request->user()->id,
                'town_id' => $town['id']
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'errors' => ['database' => ['データの追加に失敗しました。']]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validate([
                'delete_towns.*' => ['required', 'integer']
            ]);

            $townIds = $data['delete_towns'];
            DeliveryArea::whereIn('town_id', $townIds)->delete();

            Town::whereIn('id', $townIds)->delete();

            //townが削除されたcity（townが0件になったcity）を削除
            $cityIds = Town::pluck('city_id')->unique();
            City::whereNotIn('id', $cityIds)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'errors' => ['database' => ['データの削除に失敗しました。']]
            ], 500);
        }
    }
}
