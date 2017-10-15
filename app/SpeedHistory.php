<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpeedHistory extends Model
{
    /**
     * Get the phone record associated with the user.
     */
	protected $table = 'speed_history';

	public function road() {
		return $this->hasOne('App\Road', 'start', 'gantryfrom');
	}

	public function distance() {
		return $this->hasOne('App\StationDistance', 'gantryfrom', 'gantryfrom');
	}
}
