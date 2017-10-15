<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredictHistory extends Model
{
    /**
     * Get the phone record associated with the user.
     */
	protected $table = 'predict_history';
	public $timestamps = false;
}
