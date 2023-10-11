<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\PrefectureController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeliveryAreaController;
use App\Http\Controllers\Api\DropoffController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'getAuthUser']);

    Route::get('/check-auth', function () {
        return response()->json(true, 200);
    });

    Route::apiResource('/area', AreaController::class)->only(['index', 'store']);
    Route::delete('/area', [AreaController::class, 'delete']);

    Route::get('/selected-towns', [DeliveryAreaController::class, 'getSelectedTowns']);
    Route::put('/delivery-area', [DeliveryAreaController::class, 'update']);

    Route::get('/prefecture', [PrefectureController::class, 'fetchPrefectures']);
    Route::get('/cities', [CityController::class, 'fetchCitiesByPrefectureId']);
    Route::get('/dropoff', [DropoffController::class, 'getDropoffPlace']);

    Route::post('/customer', [CustomerController::class, 'store']);
    Route::get('/default-town', [CustomerController::class, 'getDefaultTown']);
});
