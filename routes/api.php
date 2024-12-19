<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login')->name('auth.login');
        Route::middleware('auth:api')->group(function () {
            Route::get('me', 'me')->name('auth.me');
            Route::post('refresh', 'refresh')->name('auth.refresh');
            Route::post('logout', 'logout')->name('auth.logout');
        });
    });

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
