<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PeopleGamificationController;
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

Route::get('messages', [MessageController::class, 'index']);
Route::get('messages/{id}', [MessageController::class, 'show']);
Route::post('messages', [MessageController::class, 'store']);
Route::post('messages/update/{id}', [MessageController::class, 'update']);
Route::delete('messages/{id}', [MessageController::class, 'destroy']);

Route::get('people-gamifications', [PeopleGamificationController::class, 'index']);
Route::get('people-gamifications/{id}', [PeopleGamificationController::class, 'show']);
Route::post('people-gamifications', [PeopleGamificationController::class, 'store']);
Route::post('people-gamifications/update/{id}', [PeopleGamificationController::class, 'update']);
Route::delete('people-gamifications/{id}', [PeopleGamificationController::class, 'destroy']);