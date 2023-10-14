<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(CustomerRequest $request) {
        $data = $request->validated();

        $customer = new Customer;

        //Customerテーブルのカラムだけを抽出して保存
        $customerData = array_filter($data, function ($key) use ($customer) {
            return in_array($key, $customer->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $customer->fill($customerData)->save();

        $customer->dropoffs()->attach($data['dropoff_ids']);

        if ($data['is_checked_default']) {
            $user = $request->user();
            $currentDefaultTown = $user->defaultTown();

            // 既存のデフォルト町域のdefaultフラグを0に設定
            if ($currentDefaultTown) {
                $user->towns()->updateExistingPivot($currentDefaultTown->id, ['default' => false]);
            }

            // 新しいデフォルト町域のdefaultフラグを1に設定
            $newDefaultTownId = $data['town_id'];
            $user->towns()->updateExistingPivot($newDefaultTownId, ['default' => true]);
        }
    }



    public function getDefaultTown()
    {
        $user = auth()->user();
        return $user->defaultTown();
    }
}