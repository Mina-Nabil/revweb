<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankingInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banking_info', function (Blueprint $table) {
            $table->id();
            $table->string("BANK_HLDR_NAME"); //Account Holder Name
            $table->string("BANK_ACNT");
            $table->string("BANK_BRCH");
            $table->string("BANK_IBAN");
            $table->foreignId("BANK_SHRM_ID");
        });

        Schema::table("showrooms", function (Blueprint $table) {
            $table->unsignedBigInteger("SHRM_BANK_ID")->nullable();
            $table->foreign("SHRM_BANK_ID")->references("id")->on("banking_info")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('showrooms', function (Blueprint $table) {
            $table->dropForeign(["SHRM_BANK_ID"]);
            $table->dropColumn("SHRM_BANK_ID");
        });
        Schema::dropIfExists('banking_info');
    }
}
