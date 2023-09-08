<?php

use App\Models\Offers\Offer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement("ALTER TABLE offers MODIFY COLUMN OFFR_STTS ENUM(\"" . implode('","', Offer::STATES) . "\")");


        Schema::create('offer_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Offer::class, "OFDC_OFFR_ID");
            $table->string("title");
            $table->string("doc_url");
            $table->text("note")->nullable();
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
        Schema::dropIfExists('offer_docs');
    }
};
