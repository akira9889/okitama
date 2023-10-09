<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryArea;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

class DeliveryAreaController extends Controller
{
    public function getSelectedTowns()
    {
        $user = auth()->user();
        $selectedTowns = $user->towns()->pluck('town_id');
        Log::debug($selectedTowns);

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
