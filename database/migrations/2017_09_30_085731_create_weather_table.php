<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeatherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weather', function (Blueprint $table) {
            $table->increments('id');
            $table->double('lat')->comment('緯度');
            $table->double('lon')->comment('經度');
            $table->string('locationName')->comment('測站編號');
            $table->string('stationId')->comment('測站ID');
            $table->dateTimeTz('obsTime')->comment('觀測資料時間');
            $table->float('ELEV')->comment('高度 （單位 公尺）');
            $table->float('WDIR')->comment('風向 單位 度 風向0表示無風');
            $table->float('WDSD')->commnet('風速');
            $table->float('TEMP')->comment('溫度');
            $table->double('HUMD')->comment('相對濕度');
            $table->float('PRES')->comment('氣壓');
            $table->double('SUN')->comment('日照指數');
            $table->float('H_24R')->comment('日累積雨量');
            $table->float('WS15M')->comment('觀測時間前十五分鐘最大風速');
            $table->float('WD15M')->comment('觀測時間前推十五分鐘內發生最大風速');
            $table->float('WS15T')->comment('觀測時間前推十五分鐘內最大風速發生時間');
            $table->string('CITY');
            $table->string('CITY_SN')->comment('縣市編號');
            $table->string('TOWN')->comment('鄉鎮');
            $table->string('TOWN_SN')->comment('鄉鎮編號');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weather');
    }
}
