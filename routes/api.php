<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/data/cities', [App\Http\Controllers\LocationController::class, 'cities']);
Route::get('/data/cityInfo', [App\Http\Controllers\LocationController::class, 'getCityInfo']);
Route::get('/data/location', [App\Http\Controllers\LocationController::class, 'location']);
Route::get('/data/allLocations', [App\Http\Controllers\LocationController::class, 'allLocations']);
Route::get('/data/allCategories', [App\Http\Controllers\EquipmentTypeController::class, 'allEquipmentTypes']);
Route::get('/data/services', [App\Http\Controllers\ReviewSourceController::class, 'reviewSources']);
Route::get('/filter/shop', [App\Http\Controllers\ServiceCenterController::class, 'serviceCenterList']);

Route::middleware('auth.admin')->group(function () {
    // search shop
});


