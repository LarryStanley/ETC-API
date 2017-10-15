<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\SpeedHistory;
use App\Station;

class Weather extends Command
{

    protected $signature = 'weather:add';

    protected $description = 'add weather id in station.';

    public function __construct()
    {
        parent::__construct();

    }

    public function handle()
    {
        $wetaher_stations = DB::table("weather_month_stations")
                              ->get();

        $stations = Station::all();

        foreach ($stations as $key => $station) {
            $miniDistance = 1000000;
            foreach ($wetaher_stations as $index => $weather_station) {
                $distance = $this->distance($weather_station->lat, $weather_station->lng, $station->lat, $station->lng, "K");
               
                if ($miniDistance > $distance) {
                    $miniDistance = $distance;
                    $station->weather_month_station = $weather_station->stationId;
                }
            }

            $station->save();
        }

        $this->info('mission complete');
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