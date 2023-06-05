<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailVerifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('mob_verifications');

        Schema::create('mob_verifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('mober');
            $table->string('mob');
            $table->string('code');
            $table->dateTime('expiry');
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
        Schema::dropIfExists('mob_verifications');
    }
};
