<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            
            $table->id();
            $table->foreignId('CAR_MODL_ID')->constrained('models');
            $table->string('CAR_CATG');
            $table->integer('CAR_PRCE');
            $table->integer('CAR_VLUE')->default(500);  //the higher the better

            $table->tinyInteger('CAR_ACTV')->default(0);

            //Car specs -- all nullable as they can add car before publish
            $table->integer('CAR_HPWR')->nullable();
            $table->integer('CAR_SEAT')->nullable();
            $table->double('CAR_ACC')->nullable(); //0-100 acceleration
            $table->string('CAR_ENCC')->nullable();
            $table->string('CAR_TORQ')->nullable();
            $table->string('CAR_TRNS')->nullable(); //transmission type
            $table->integer('CAR_TPSP')->nullable(); //top speed
            $table->double('CAR_HEIT')->nullable();
            $table->integer('CAR_RIMS')->nullable(); //wheel raduis
            $table->integer('CAR_TRNK')->nullable(); //fuel trunk capacity
            $table->string('CAR_DIMN')->nullable(); //dimensions

            //Car marketing info
            $table->string('CAR_TTL1')->nullable();
            $table->text('CAR_PRG1')->nullable();
            $table->string('CAR_TTL2')->nullable();
            $table->text('CAR_PRG2')->nullable();



            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
