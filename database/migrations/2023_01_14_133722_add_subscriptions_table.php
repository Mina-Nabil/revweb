<?php

use App\Subscriptions\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function(Blueprint $table){
            $table->id();
            $table->foreignId('showroom_id');
            $table->foreignId('seller_id'); //subscribed by
            $table->foreignId('plan_id');
            $table->enum('state', Subscription::STATES)->default(Subscription::ACTIVE_STATE);
            $table->enum('type', Subscription::SUBSCRIPTION_TYPES);
            $table->dateTime('expiry_date');
            $table->dateTime('cancellation_date')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
