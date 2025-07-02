<?php

use App\Http\Controllers\V1\BookingController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::controller(BookingController::class)
        ->prefix('bookings')
        ->middleware(AuthMiddleware::class)
        ->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::patch('/{booking}/slots/{slot}', 'update');
            Route::post('/{booking}/slots', 'add');
            Route::delete('/{booking}', 'delete');
        });
});

