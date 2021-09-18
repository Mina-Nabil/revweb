<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('ACSR_NAME')->unique();
            $table->string('ACSR_ARBC_NAME')->nullable();
        });

        Schema::create('accessories_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ACCR_CAR_ID')->constrained('cars');
            $table->foreignId('ACCR_ACSR_ID')->constrained('accessories');
            $table->string('ACCR_VLUE')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accessories_cars');
        Schema::dropIfExists('accessories');
    }
}
