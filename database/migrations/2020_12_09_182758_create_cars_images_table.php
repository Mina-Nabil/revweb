<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('CIMG_CAR_ID')->constrained('cars');
            $table->string('CIMG_URL');
            $table->integer('CIMG_VLUE')->default(500);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars_images');
    }
}
