<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string("CNTR_NAME");
            $table->string("CNTR_ARBC_NAME");
        });
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId("CITY_CNTR_ID")->constrained("countries");
            $table->string("CITY_NAME");
            $table->string("CITY_ARBC_NAME");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
    }
}
