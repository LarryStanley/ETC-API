<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\SpeedHistory;
use App\Station;

class HistoryController extends BaseController
{
    public function getHistory(Request $request) {

    	$history = SpeedHistory:://with('road')
                   where("month", $request->month)
                   ->where('year', $request->year)
                   ->where('day', $request->day)
                   ->where('time_type', $request->time_type)
                   ->where('car_type', $request->car_type)
                   ->with('distance')
                   //->limit(10)
                   ->get();
        /*foreach ($history as $key => $value) {
            if (isset($value->road)){
                array_push($result, ["color" => "red", "direction" => json_decode($value->road->direction)]);
            }
        }*/

    	return $history;
    }

    public function getMonthHistory(Request $request) {

        $history = SpeedHistory:://with('road')
                   where("month", $request->month)
                   ->where('year', $request->year)
                   ->where('car_type', $request->car_type)
                   ->with('distance')
                   ->get();

        $results = [];
        $currentTimeType = "Z";
        $timePeriod = [];
        foreach ($history as $key => $value) {
            if ($currentTimeType == $value->time_type) {
                array_push($timePeriod, $value);
            } else {
                if ($key)
                    array_push($results, $timePeriod);
                $timePeriod = [];
                array_push($timePeriod, $value);
                $currentTimeType = $value->time_type;
            }
        }

        return $results;
    }

    public function getStationMonthHistory(Request $request) {
        $history = SpeedHistory:://with('road')
                   where('year', $request->year)
                   ->where('car_type', $request->car_type)
                   ->where('gantryfrom', $request->station)
                   ->where('time_type', $request->time_type)
                   ->orderBy('month', "asc")
                   ->orderBy("day", "asc")
                   ->orderBy("time_type", "asc")
                   ->get(["day", "average_speed", "time_type", "month"]);

        foreach ($history as $key => $value) {
            if ($value->time_type == 'A') {
                $history[$key]->day += 0;
            } else if ($value->time_type == 'B') {
                $history[$key]->day += 0.125;
            } else if ($value->time_type == 'C') {
                $history[$key]->day += 0.25;
            } else if ($value->time_type == 'D') {
                $history[$key]->day += 0.375;
            } else if ($value->time_type == 'E') {
                $history[$key]->day += 0.5;
            } else if ($value->time_type == 'F') {
                $history[$key]->day += 0.75;
            } else if ($value->time_type == 'G') {
                $history[$key]->day += 0.875;
            } else {
                $history[$key]->day += 0.9;
            }

            $history[$key]->day += ($value->month -1) * 30;
        }

        $station = DB::table('station')->where("station_number", $request->station)->limit(1)->get();


        $weather = DB::table('weather_month_history')
                    ->where("stationId", $station[0]->weather_month_station)
                    ->where('year', $request->year)
                    ->OrderBy('month', 'asc')
                    ->get();

        $newTemp = [];
        $newRain = [];
        $newWind = [];
        foreach ($weather as $key => $value) {
            array_push($newTemp, ["day" => ($value->month - 1)*30, "average_speed" => $value->temp]);
            array_push($newRain, ["day" => ($value->month - 1)*30, "average_speed" => $value->rain]);
            array_push($newWind, ["day" => ($value->month - 1)*30, "average_speed" => $value->wind]);
        }


        return [$history, $newTemp, $newRain, $newWind];
    }

    public function getWeatherHistory(Request $request) {
        $next = strtotime($request->time) + 60*60*3;
        $next = date("Y-m-d H:i:s", $next);
        $weather = DB::table('weather')
                    ->where("stationId", $request->stationId)
                    ->where("obsTime", ">", $request->time)
                    ->where("obsTime", "<=", $next)
                    ->OrderBy('obsTime', 'asc')
                    ->limit(1)
                    ->get();
        return $weather;
    }

    public function getStationWeatherHistory(Request $request) {
        $weather = DB::table('weather')
                    ->where("stationId", $request->stationId)
                    ->where("obsTime", ">=", $request->year."-01-01 00:00:00")
                    ->where("obsTime", "<=", $request->year."-12-31 00:00:00")
                    ->OrderBy('obsTime', 'asc')
                    ->get();

        foreach ($weather as $key => $value) {
            $day = intval(date( "d", strtotime($value->obsTime)));
            $hour = intval(date( "hh", strtotime($value->obsTime)));

            $weather[$key]->obsTime = $day + $hour * 0.01;
        }

        return $weather;
    }

    public function getWeatherStations(Request $request) {
        $wetaher_stations = DB::table('weather')
                              ->select('lat', 'lon', 'stationId', 'locationName')
                              ->distinct('stationId')
                              ->groupBy('stationId')
                              ->get();

        $stations = Station::all();

        foreach ($stations as $key => $station) {
            $miniDistance = 1000000;
            foreach ($wetaher_stations as $index => $weather_station) {
                $distance = $this->distance($weather_station->lat, $weather_station->lon, $station->lat, $station->lng, "K");
                if ($miniDistance > $distance) {
                    $miniDistance = $distance;
                    $station->weather_station_id = $weather_station->stationId;
                    $station->save();
                }
            }

        }

        return "success";
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
          return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
      } else {
          return $miles;
      }
    }

}
