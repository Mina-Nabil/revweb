<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("OFRQ_BUYR_ID")->constrained("buyers");
            $table->foreignId("OFRQ_CAR_ID")->constrained("buyers");
            $table->date("OFRQ_DATE");
            $table->enum("OFRQ_STTS", ["New", "Cancelled", "Done"]);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('offers_requests_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId("OFRC_OFRQ_ID")->constrained("buyers");
            $table->integer("OFRC_PRTY")->default(0);
            $table->softDeletes();
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("OFFR_SLLR_ID")->constrained('sellers');
            $table->foreignId("OFFR_SHRM_ID")->constrained('showrooms');
            $table->foreignId("OFFR_BUYR_ID")->constrained('buyers');
            $table->foreignId("OFFR_CAR_ID")->constrained('cars');
            $table->integer("OFFR_PRCE");
            $table->integer("OFFR_MIN_PYMT");
            $table->dateTime("OFFR_STRT_DATE");
            $table->date("OFFR_EXPR_DATE");
            $table->text("OFFR_CMNT")->nullable();
            $table->enum("OFFR_STTS", ["New", "Expired", "Accepted"])->nullable();
            $table->timestamps();
        });

        Schema::create('offer_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId("OFCL_OFFR_ID")->constrained('offers');
            $table->foreignId("OFCL_COLR_ID")->constrained('model_colors');
            $table->integer("OFCL_AVLB")->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_colors');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('offers_requests_colors');
        Schema::dropIfExists('offers_requests');
    }
}
