<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModelsImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_images', function(Blueprint $table){
            $table->id();
            $table->foreignId("MOIM_MODL_ID")->constrained('models');
            $table->string("MOIM_URL");
            $table->integer("MOIM_SORT")->default(500);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("model_images");
    }
}
