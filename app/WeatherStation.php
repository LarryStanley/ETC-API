<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeatherStation extends Model
{
    /**
     * Get the phone record associated with the user.
     */
	protected $table = 'weather_stations';
	public $timestamps = false;
}
