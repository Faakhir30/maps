<?php

namespace App\Models;

use MongoDb\Laravel\Eloquent\Model as Eloquent;

class City extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'city';
    protected $fillable = ['locId', 'country', 'region', 'city', 'postalCode', 'latitude', 'longitude', 'metroCode', 'areaCode'];
}