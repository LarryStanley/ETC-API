<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\SpeedHistory;
use App\Station;
use App\WeatherStation;
use App\PredictHistory;

class AddDate extends Command
{

    protected $signature = 'predict:add';

    protected $description = 'add date in predict.';

    public function __construct()
    {
        parent::__construct();

    }

    public function handle()
    {

      $predicts = PredictHistory::where("date", null)->get();

      foreach ($predicts as $key => $predict) {
        $date = strtotime($predict->year."-".$predict->month."-".$predict->day);
        $predict->date = date('Y-m-d', $date);

        $predict->save();
        $this->info($key/count($predicts)*100);
      }

      $this->info("mission complete");

    }
}