<?php

use App\Models\Offers\Offer;
use App\Models\Users\Buyer;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Seller::class);
            $table->string('title');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->foreignIdFor(Buyer::class)->nullable();
            $table->foreignIdFor(Showroom::class)->nullable();
            $table->foreignIdFor(Offer::class)->nullable();
            $table->text('note')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('notification_time')->nullable();
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
        Schema::dropIfExists('events');
    }
};
