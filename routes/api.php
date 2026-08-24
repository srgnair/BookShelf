<?php

use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.')
    ->group(function () {
        Route::apiResource('books', BookController::class)->only(['index', 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::apiResource('books', BookController::class)->only(['store', 'update', 'destroy']);
        });
    });
