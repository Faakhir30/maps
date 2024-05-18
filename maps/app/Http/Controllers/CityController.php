<?php

namespace App\Http\Controllers;

use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::orderBy('locId', 'desc')->take(10)->get();
        return view('cities.index', compact('cities'));
    }

    public function findClosestCities($cityName)
    {
        // Get the coordinates (latitude and longitude) of the input city
        $inputCity = City::where('city', $cityName)->first();
        $inputLatitude = $inputCity->latitude; // Convert degrees to radians
        $inputLongitude = $inputCity->longitude; // Convert degrees to radians
        // Find the 5 closest cities based on the input coordinates using the aggregation framework
        $closestCities = City::raw(function ($collection) use ($inputLatitude, $inputLongitude, $cityName) {
            return $collection->aggregate([
                [
                    '$match' => ['city' => ['$ne' => $cityName]] // Exclude the input city
                ],
                [
                    '$addFields' => [
                        'distance' => [
                            '$add' => [
                                ['$pow' => [['$subtract' => ['$latitude', $inputLatitude]], 2]],
                                ['$pow' => [['$subtract' => ['$longitude', $inputLongitude]], 2]]
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => ['distance' => 1] // Sort by distance in ascending order
                ],
                [
                    '$limit' => 5 // Limit the result to 5 closest cities
                ]
            ]);
        });
        return $closestCities;
    }

    public function getCityNamesThatStartWith($initials)
    {
        $cities = City::where('city', 'like', $initials . '%')->take(20)->get();
        return $cities;
    }


    public function getNeariestCities($long, $lat)
    {

        try {
            $closestCities = City::raw(function ($collection) use ($long, $lat) {
                return $collection->aggregate([
                    [
                        '$addFields' => [
                            'distance' => [
                                '$add' => [
                                    ['$pow' => [['$subtract' => ['$latitude', (int) $lat]], 2]],
                                    ['$pow' => [['$subtract' => ['$longitude',(int)  $long]], 2]]
                                ]
                            ]
                        ]
                    ],
                    [
                        '$sort' => ['distance' => 1] // Sort by distance in ascending order
                    ],
                    [
                        '$limit' => 1 // Limit the result to 5 closest cities
                    ]
                ]);
            });

            return response()->json($closestCities[0]);
        } catch (\Exception $e) {
            // Log the error message
            // Log::error('Error fetching nearest cities: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), $lat, $long], 500);
        }
    }

}