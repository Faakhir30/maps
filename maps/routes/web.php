<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/cities', [CityController::class, 'index']);

Route::get('/query/{cityName}', [CityController::class, 'findClosestCities']);
Route::get('/get-cities/{initials}', [CityController::class, 'getCityNamesThatStartWith']);
