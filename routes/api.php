<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('list')->group(function () {
    Route::get('plat', [Api\ParkingController::class, 'countPlat']);
    Route::get('color', [Api\ParkingController::class, 'getByColor']);
});

Route::post('check-in', [Api\ParkingController::class, 'checkIn']);
Route::post('check-out', [Api\ParkingController::class, 'checkOut']);
