<?php

use App\Http\Controllers\Api\JobController;
use Illuminate\Support\Facades\Route;

Route::prefix('jobs')->group(function (): void {
    Route::get('/', [JobController::class, 'index']);
    Route::get('{job:slug}', [JobController::class, 'show']);
});
