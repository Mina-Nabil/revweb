<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string("SLLR_NAME");
            $table->string("SLLR_MAIL")->unique();
            $table->string("SLLR_MOB1")->unique();
            $table->string("SLLR_PASS");
            $table->string("SLLR_IMGE")->nullable();
            $table->integer("SLLR_MAIL_VRFD")->default(0);
            $table->integer("SLLR_MOB1_VRFD")->default(0);
            $table->string("SLLR_MOB2")->nullable();
            $table->integer("SLLR_MOB2_VRFD")->default(0);
            $table->string("SLLR_PUSH_ID")->nullable();
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
        Schema::dropIfExists('sellers');
    }
}
