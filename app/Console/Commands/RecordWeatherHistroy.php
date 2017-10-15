<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\SpeedHistory;
use App\Station;
use App\WeatherStation;


class RecordWeatherHistroy extends Command
{

    protected $signature = 'history:addweather';

    protected $description = 'add weather id in speed history.';

    public function __construct()
    {
        parent::__construct();

    }

    public function handle()
    {

      $weather_stations = DB::table("weather")->distinct()->get(["stationId", "lat", "lon", "locationName"]);

      foreach ($weather_stations as $key => $station) {
        $new_station = new WeatherStation;
        $new_station->stationId = $station->stationId;
        $new_station->lat = $station->lat;
        $new_station->lng = $station->lon;
        $new_station->locationName = $station->locationName;
        $nearbyStations = [];
        foreach ($weather_stations as $index => $nearBy) {
          array_push($nearbyStations, [
            "distance" => $this->distance($station->lat, $station->lon, $nearBy->lat, $station->lon, "K"),
            "stationId" => $nearBy->stationId
          ]); 
        }

        usort($nearbyStations, function($item1, $item2) {
            return $item1['distance'] <=> $item2['distance']; 
        });
        $new_station->nearby_stations = json_encode($nearbyStations);
        if (json_encode($nearbyStations)) {
          $new_station->save();    
        }
      }

      $this->info("mission complete");

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