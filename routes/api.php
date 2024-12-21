<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
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

Route::prefix('company')
    ->middleware('auth:api')
    ->controller(CompanyController::class)
    ->group(function () {
        Route::get('', 'index')->name('company.index');
        Route::post('', 'store')->name('company.store');
        Route::get('{company}', 'show')->name('company.show');
        Route::put('{company}', 'update')->name('company.update');
        Route::delete('{company}', 'destroy')->name('company.destroy');
    });

Route::prefix('employee')
    ->middleware('auth:api')
    ->controller(EmployeeController::class)
    ->group(function () {
        Route::get('company/{company}', 'index')->name('employee.index');
        Route::post('company/{company}', 'store')->name('employee.store');
        Route::get('{employee}', 'show')->name('employee.show');
        Route::put('{employee}', 'update')->name('employee.update');
        Route::delete('{employee}', 'destroy')->name('employee.destroy');
    });
