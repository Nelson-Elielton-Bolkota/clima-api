<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/weather', [WeatherController::class, 'current']);

Route::apiResource('cities', CityController::class);
