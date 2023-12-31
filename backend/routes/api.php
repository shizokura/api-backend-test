<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// booking list
Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/bookings/groups', [BookingController::class, 'group']);

// if logged in
Route::group(['middleware' => 'auth:sanctum'], function () {
    // logout
    Route::post('/logout', [UserController::class, 'logout']);

    // booking
    Route::put('/bookings', [BookingController::class, 'create']);
    Route::get('/bookings/{id}', [BookingController::class, 'get']);
    Route::patch('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'delete']);
});
