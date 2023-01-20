<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function(Blueprint $table){
            $table->id();
            $table->string('name')->unique();
            $table->double('monthly_price');
            $table->double('annual_price');
            $table->integer('admins_limit');
            $table->integer('users_limit');
            $table->integer('models_limit');
            $table->integer('offers_limit');
            $table->integer('services_limit');
            $table->boolean('facility_payment');
            $table->boolean('email_support');
            $table->boolean('chat_support');
            $table->boolean('phone_support');
            $table->boolean('dashboard_access');
            $table->unsignedInteger('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }

}
