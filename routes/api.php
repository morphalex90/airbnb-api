<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::prefix('v1')->group(function () {


    ##### Rooms
    Route::post('rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('rooms/{room_slug}', [RoomController::class, 'show'])->name('rooms.show');

    // Route::middleware('auth:api')->group(function () {
    // });


    #### User
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);

        Route::middleware('auth:api')->group(function () {
            Route::get('user', [UserController::class, 'user']);
            Route::patch('update', [UserController::class, 'update']);
            Route::get('logout', [AuthController::class, 'logout']);
        });

        // Confirm user email
        Route::get('email/verify', function () {
            return '';
        })->middleware('auth')->name('verification.notice');
        Route::get('account/verify/{id}', [AuthController::class, 'verifyAccount'])->name('verification.verify');

        // Password reset
        Route::get('account/password', function () {
            return '';
        })->middleware('guest')->name('password.request');
        Route::post('password/reset', [AuthController::class, 'sendPasswordReset'])->middleware('guest')->name('password.email');
        Route::get('reset-password/{token}', function ($token) {
            return '';
        })->middleware('guest')->name('password.reset');
        Route::post('password/reset/confirm', [AuthController::class, 'saveNewPassword'])->middleware('guest')->name('password.update');
    });
});
