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
        $inputLatitude = deg2rad($inputCity->latitude); // Convert degrees to radians
        $inputLongitude = deg2rad($inputCity->longitude); // Convert degrees to radians
    
        // Find the 5 closest cities based on the input coordinates using the aggregation framework
        $closestCities = City::raw(function ($collection) use ($inputLatitude, $inputLongitude, $cityName) {
            return $collection->aggregate([
                [
                    '$match' => ['city' => ['$ne' => $cityName]] // Exclude the input city
                ],
                [
                    '$addFields' => [
                        'distance' => [
                            '$multiply' => [
                                6371, // Earth's radius in kilometers
                                [
                                    '$acos' => [
                                        [
                                            '$add' => [
                                                [
                                                    '$multiply' => [
                                                        ['$sin' => $inputLatitude],
                                                        ['$sin' => ['$degreesToRadians' => '$latitude']]
                                                    ]
                                                ],
                                                [
                                                    '$multiply' => [
                                                        ['$cos' => $inputLatitude],
                                                        ['$cos' => ['$degreesToRadians' => '$latitude']],
                                                        ['$cos' => [
                                                            '$subtract' => [
                                                                ['$degreesToRadians' => $inputLongitude],
                                                                ['$degreesToRadians' => '$longitude']
                                                            ]
                                                        ]]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
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

}