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
