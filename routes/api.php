<?php

use App\Http\Controllers\API\authController;
use App\Http\Controllers\API\CasinoController;
use App\Http\Controllers\API\DataCheckController;
use App\Http\Controllers\API\UserCRUDController;
use Illuminate\Http\Request;
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

Route::get('/users', [UserCRUDController::class, 'index']);
Route::post('/users', [UserCRUDController::class, 'store']);
Route::get('/users/{id}', [UserCRUDController::class, 'show']);
Route::post('/users/{id}/up', [UserCRUDController::class, 'update']);
Route::post('/users/{id}/del', [UserCRUDController::class, 'destroy']);
Route::post('/auth', [authController::class, 'index']);
Route::post('/exit', [authController::class, 'store']);
Route::post('/stavka', [CasinoController::class, 'store']);
Route::get('/stavka', [CasinoController::class, 'index']);
Route::get('/stavka/{id}', [CasinoController::class, 'show']);
Route::post('/stavka/{id}/up', [CasinoController::class, 'update']);
Route::post('/stavka/{id}/del', [CasinoController::class, 'destroy']);
Route::get('/datas', [DataCheckController::class, 'index']);
Route::post('/datas', [DataCheckController::class, 'store']);
