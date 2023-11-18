<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\AwaitingUserController;
use App\Http\Controllers\Api\PrefectureController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeliveryAreaController;
use App\Http\Controllers\Api\DropoffController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\DropoffHistoryController;
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
    Route::get('/auth-user', [AuthController::class, 'getAuthUser']);
    Route::get('/check-auth', function () {
        return response()->json(true, 200);
    });
});

Route::middleware('auth:sanctum', 'approved')->group(function () {

    // 管理者ユーザー用ルート
    // 以下のルートは管理者（is_adminが1のユーザー）のみがアクセス可能
    Route::middleware('admin')->group(function () {
        Route::get('/user', [UserController::class, 'index']);
        Route::put('/user/{user}', [UserController::class, 'update']);
        Route::delete('/user/{user}', [UserController::class, 'delete']);

        Route::get('/awaiting-user', [AwaitingUserController::class, 'index']);
        Route::put('/awaiting-user/{user}', [AwaitingUserController::class, 'update']);
        Route::delete('/awaiting-user/{user}', [AwaitingUserController::class, 'delete']);

        Route::get('/awaiting-user/exists', [AwaitingUserController::class, 'isExistsAwaitingUser']);

        Route::post('/import-customer', [CustomerController::class, 'importCustomer']);
    });

    // 一般ユーザー用ルート
    Route::apiResource('/area', AreaController::class)->only(['index', 'store']);
    Route::delete('/area', [AreaController::class, 'delete']);

    Route::get('/selected-towns', [DeliveryAreaController::class, 'getSelectedTowns']);
    Route::get('/grouped-selected-towns', [DeliveryAreaController::class, 'getSelectedTownsGroupByCity']);
    Route::put('/delivery-area', [DeliveryAreaController::class, 'update']);

    Route::get('/prefecture', [PrefectureController::class, 'fetchPrefectures']);
    Route::get('/cities', [CityController::class, 'fetchCitiesByPrefectureId']);

    Route::get('/dropoff-history', [DropoffHistoryController::class, 'index']);
    Route::post('/dropoff-history', [DropoffHistoryController::class, 'store']);
    Route::get('dropoff-history/{dropoffHistory}', [DropoffHistoryController::class, 'show']);
    Route::get('/dropoff', [DropoffController::class, 'getDropoffPlace']);

    Route::get('/customer', [CustomerController::class, 'index']);
    Route::post('/customer', [CustomerController::class, 'store']);
    Route::get('/customer/{customer}', [CustomerController::class, 'show']);
    Route::put('/customer/{customer}', [CustomerController::class, 'update']);

    Route::get('/default-town', [CustomerController::class, 'getDefaultTown']);
});
