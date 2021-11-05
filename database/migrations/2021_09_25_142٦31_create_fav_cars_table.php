<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fav_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId("FAVC_BUYR_ID")->constrained("buyers");
            $table->foreignId("FAVC_CAR_ID")->constrained("cars");
        });

        Schema::create('owned_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId("OWND_BUYR_ID")->constrained("buyers");
            $table->foreignId("OWND_CAR_ID")->constrained("cars");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('owned_cars');
        Schema::dropIfExists('fav_cars');
    }
}
