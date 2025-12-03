<?php

use App\Infrastructure\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/healthz', HealthCheckController::class);
