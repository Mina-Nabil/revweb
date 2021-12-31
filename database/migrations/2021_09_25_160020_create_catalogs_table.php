<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('showroom_catalog', function (Blueprint $table){
            $table->id();
            $table->foreignId("SRCG_SHRM_ID")->constrained("showrooms");
            $table->foreignId("SRCG_CAR_ID")->constrained("cars");
            $table->integer("SRCG_DEF_PRCE")->nullable(); //default offer price
            $table->integer("SRCG_MAX_DAYS")->default(7); //offer usual time
            $table->integer("SRCG_MIN_PYMT")->nullable(); //offer min downpayment
            $table->integer("SRCG_CAR_ACTV")->default(1); //is the car still active
            // $table->integer("SRCG_DEF_ACTV")->default(0); //is default offer active
        });

        Schema::create('showroom_catalog_details', function (Blueprint $table){
            $table->id();
            $table->foreignId("SRCD_SRCG_ID")->constrained("showroom_catalog");
            $table->foreignId("SRCD_COLR_ID")->constrained("model_colors");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('showroom_catalog_details');
        Schema::dropIfExists('showroom_catalog');
    }
}
