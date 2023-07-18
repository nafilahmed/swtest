<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MovieController;
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

Route::controller(AuthController::class)->group(function(){
    Route::post('register','registerUser', 'register');
    Route::post('login','loginUser', 'login');
});

Route::controller(AuthController::class)->middleware('auth:api')->group(function(){

    Route::get('user','getUserDetail');
    Route::get('logout','userLogout');

});


Route::apiResource('/movie', MovieController::class)->middleware('auth:api');
    