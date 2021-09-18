<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('BRND_NAME')->unique();
            $table->string('BRND_ARBC_NAME')->nullable();
            $table->tinyInteger('BRND_ACTV')->default(1);
            $table->string('BRND_LOGO')->nullable();
            $table->string('BRND_IMGE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brands');
    }
}
