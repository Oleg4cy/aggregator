<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\ServiceCenterController::class, 'index'])->name('home');
Route::get('/shop/{id}', [App\Http\Controllers\ServiceCenterController::class, 'show'])->name('shop');
Route::get('/404', [App\Http\Controllers\UndefinedController::class, 'index'])->name('undefined');

