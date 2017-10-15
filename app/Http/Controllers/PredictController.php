<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Station;

class PredictController extends BaseController
{
   public function getWeekPredict(Request $request) {
    $today = date("Y-m-d");
    $nextWeek = date("Y-m-d", strtotime("+1 week"));

    $allPredicts = DB::table("predict_history")
                  ->where("VehicleType", $request->card_type)
                  ->where("gantryfrom", $request->start)
                  ->where("date", ">=" , $today)
                  ->where("date", "<=", $nextWeek)->get();

    $points = [];
    foreach ($allPredicts as $key => $value) {
        $hour = 0;
        if ($value->time_type == 'A') {
            $hour += 0;
        } else if ($value->time_type == 'B') {
            $hour += 0.125;
        } else if ($value->time_type == 'C') {
            $hour += 0.25;
        } else if ($value->time_type == 'D') {
             $hour += 0.375;
        } else if ($value->time_type == 'E') {
             $hour += 0.5;
        } else if ($value->time_type == 'F') {
             $hour += 0.75;
        } else if ($value->time_type == 'G') {
             $hour += 0.875;
        } else {
             $hour += 0.9;
        }

        array_push($points, ["average_speed" => $value->average_speed, "day" => $value->day + $value->year + $value->month + $hour]);
    }

   $today = DB::table("predict_history")
                  ->where("VehicleType", $request->card_type)
                  ->where("gantryfrom", $request->start)
                  ->where("date", "=",$today)->get();

    $today_mean = 0;
    foreach ($today as $key => $value) {
        $today_mean += $value->average_speed;
    }
    $today_mean = $today_mean/count($today);
    $next_mean = [];

    $next_days = DB::table("predict_history")
                  ->where("VehicleType", $request->card_type)
                  ->where("gantryfrom", $request->start)
                  ->where("date", ">" , date("Y-m-d"))
                  ->where("date", "<", $nextWeek)->get();

    $mean = 0;
    foreach ($next_days as $key => $value) {
       if ($key%8 == 7) {
        array_push($next_mean, $mean/8);
        $mean = 0;
       }

       $mean += $value->average_speed;
    }

    return ["points" => $points, "today_mean" => $today_mean, "todays" => $today, "next_days" => $next_mean];
   }
}
