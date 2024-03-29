<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ADJT_MODL_ID')->constrained('models');
            $table->string('ADJT_NAME');
            $table->unsignedTinyInteger('ADJT_ACTV')->default(1);
            $table->text('ADJT_DESC')->nullable();
        });

        Schema::create('adjustments_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ADOP_ADJT_ID')->constrained('model_adjustments');
            $table->string('ADOP_NAME');
            $table->string('ADOP_IMGE')->nullable();
            $table->unsignedTinyInteger('ADOP_DFLT');
            $table->unsignedTinyInteger('ADOP_ACTV')->default(1);
            $table->text('ADOP_DESC')->nullable();
        });

        Schema::create('car_adjustment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('CRAD_ADOP_ID')->constrained('adjustments_options');
            $table->foreignId('CRAD_CAR_ID')->constrained('cars');
        });

        Schema::create('offer_adjustment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('OADO_ADOP_ID')->constrained('adjustments_options');
            $table->foreignId('OADO_ADJT_ID')->constrained('model_adjustments');
            $table->foreignId('OADO_OFFR_ID')->constrained('offers');
        });

        Schema::create('offer_request_adjustment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ORAO_ADOP_ID')->constrained('adjustments_options');
            $table->foreignId('ORAO_ADJT_ID')->constrained('model_adjustments');
            $table->foreignId('ORAO_OFRQ_ID')->constrained('offers_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_request_adjustment_options');
        Schema::dropIfExists('offer_adjustment_options');
        Schema::dropIfExists('car_adjustment_options');
        Schema::dropIfExists('adjustments_options');
        Schema::dropIfExists('model_adjustments');
    }
}
