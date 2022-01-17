<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->string("BUYR_NAME");
            $table->string("BUYR_MAIL")->unique();
            $table->string("BUYR_MOB1")->unique();
            $table->date("BUYR_BDAY");
            $table->string("BUYR_PASS");
            $table->enum("BUYR_GNDR", ["Male", "Female", "Prefer not to Say"]);
            $table->integer("BUYR_MAIL_VRFD")->default(0);
            $table->integer("BUYR_MOB1_VRFD")->default(0);
            $table->string("BUYR_MOB2")->nullable();
            $table->integer("BUYR_MOB2_VRFD")->default(0);
            $table->string("BUYR_BANK")->nullable();
            $table->string("BUYR_IBAN")->nullable();
            $table->string("BUYR_IMGE")->nullable();
            $table->string("BUYR_NTID")->nullable();
            $table->string("BUYR_FBAC")->nullable(); //fb account
            $table->string("BUYR_NTID_FRNT")->nullable();
            $table->string("BUYR_NTID_BACK")->nullable();
            $table->enum("BUYR_NTID_STTS", ["Undelivered", "Submitted", "Rejected", "Valid"])->default("Undelivered");
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
        Schema::dropIfExists('buyers');
    }
}
