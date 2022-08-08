<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('types')->insert([
            "TYPE_NAME" => "Sedan",
            "TYPE_ARBC_NAME" => "سيدان",
        ]);
        DB::table('types')->insert([
            "TYPE_NAME" => "Hatch Back",
            "TYPE_ARBC_NAME" => "هاتش باك",
        ]);
        DB::table('types')->insert([
            "TYPE_NAME" => "4 Wheel Drive",
            "TYPE_ARBC_NAME" => "دفع رباعي",
        ]);
    }
}
