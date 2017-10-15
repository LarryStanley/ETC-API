<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    /**
     * Get the phone record associated with the user.
     */
	protected $table = 'station';
	public $timestamps = false;

	public function distance() {
		return $this->hasOne('App\StationDistance', 'gantryfrom', 'station_number');
	}
}
