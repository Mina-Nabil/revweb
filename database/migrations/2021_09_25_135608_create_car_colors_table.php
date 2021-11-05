<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId("COLR_MODL_ID")->constrained("models");
            $table->string("COLR_NAME");
            $table->string("COLR_ARBC_NAME");
            $table->string("COLR_IMGE")->nullable();
            $table->string("COLR_HEX")->nullable();
            $table->integer("COLR_RED");
            $table->integer("COLR_GREN");
            $table->integer("COLR_BLUE");
            $table->integer("COLR_ALPH");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_colors');
    }
}
