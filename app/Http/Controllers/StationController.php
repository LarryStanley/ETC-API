<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Station;

class StationController extends BaseController
{
    public function showAllRoad() {
    	$roads = DB::table('road')->get();

    	return $roads;
    }

    public function recordRoad(Request $request) {
    	DB::table('road')->insert([
	    	'start' => $request->start, 
		    'end' => $request->end,
		    'start_lat' => $request->start_lat,
		    'start_lng' => $request->start_lng,
		    'end_lat' => $request->end_lat,
		    'end_lng' => $request->end_lng,
		    'direction' => $request->direction
 		]);
    	return "success";
    }

    public function getAllStation(Request $request) {
        $result = Station::where("road_type", $request->road_type)
                         ->where("direction", $request->direction)
                         ->with("distance")
                         ->get();

        return $result;
    }
}
