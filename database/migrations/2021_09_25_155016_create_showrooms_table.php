<?php

use App\Models\Users\JoinRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowroomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('showrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("SHRM_OWNR_ID")->constrained("sellers");
            $table->foreignId("SHRM_CITY_ID")->constrained("cities");
            $table->string("SHRM_NAME")->unique();
            $table->string("SHRM_MAIL");
            $table->string("SHRM_ADRS");
            $table->string("SHRM_MOB1");
            $table->integer("SHRM_VRFD")->default(0);
            $table->integer("SHRM_ACTV")->default(0);
            $table->integer("SHRM_MAIL_VRFD")->default(0);
            $table->integer("SHRM_MOB1_VRFD")->default(0);
            $table->string("SHRM_MOB2")->nullable();
            $table->integer("SHRM_MOB2_VRFD")->default(0);
            $table->string("SHRM_RECD")->nullable();
            $table->string("SHRM_IMGE")->nullable();
            $table->enum("SHRM_RECD_STTS", ["Undelivered", "Submitted", "Rejected", "Valid"])->default("Undelivered");
            $table->string("SHRM_RECD_FRNT")->nullable();
            $table->string("SHRM_RECD_BACK")->nullable();
            $table->date("SHRM_VRFD_SNCE")->nullable();
            $table->double("SHRM_BLNC")->default(0);
            $table->double("SHRM_OFRS_SENT")->default(0);
            $table->double("SHRM_OFRS_ACPT")->default(0); //offers accepted by buyers
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sellers', function (Blueprint $table){
            $table->foreignId("SLLR_SHRM_ID")->nullable()->constrained("showrooms"); //seller for the showroom
            $table->integer("SLLR_CAN_MNGR")->default(0); //can manage account
        });

        //handling join requests between showrooms and sellers
        Schema::create('join_requests', function (Blueprint $table){
            $table->id();
            $table->foreignId("JNRQ_SLLR_ID")->constrained("sellers");
            $table->foreignId("JNRQ_SHRM_ID")->constrained("showrooms"); 
            $table->enum("JNRQ_STTS", JoinRequest::STATES);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('join_requests');
        Schema::table('sellers', function (Blueprint $table){
            $table->dropForeign(["SLLR_SHRM_ID"]);
            $table->dropColumn("SLLR_SHRM_ID"); 
            $table->dropColumn("SLLR_CAN_MNGR"); 
        });
        Schema::dropIfExists('showrooms');
    }
}
