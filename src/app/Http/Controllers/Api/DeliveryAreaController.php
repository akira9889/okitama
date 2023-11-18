<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryAreaController extends Controller
{
    public function getSelectedTowns()
    {
        $user = auth()->user();
        $selectedTowns = $user->towns()->get();

        return response()->json($selectedTowns);
    }

    public function getSelectedTownsGroupByCity()
    {
        $user = auth()->user();
        $towns = $user->towns()->with('city')->orderBy('city_id')->get();

        $groupedTowns = $towns->groupBy('city_id')->map(function ($group) {
            return [
                'cityName' => $group->first()->city->name,
                'towns' => $group->map(function ($town) {
                    return ['townId' => $town->id, 'townName' => $town->name];
                }),
            ];
        });

        return response()->json(array_values($groupedTowns->toArray()));
    }

    public function update(Request $request)
    {
        $request->validate([
            'selectedTowns.*' => 'integer|exists:towns,id',
        ]);

        $selectedTowns = $request->input('selectedTowns', []);

        $user = $request->user();

        if (empty($selectedTowns)) {
            $user->towns()->detach();
        } else {
            $user->towns()->sync($selectedTowns);
        }
    }
}
