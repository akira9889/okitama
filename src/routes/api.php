<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\PrefectureController;
use App\Http\Controllers\CityController;
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
    
    Route::get('/prefecture', [PrefectureController::class, 'fetchPrefectures']);
    Route::get('/cities', [CityController::class, 'fetchCitiesByPrefectureId']);
});
