<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->insert([
            "CNTR_NAME" => "Egypt",
            "CNTR_ARBC_NAME" => "مصر",
        ]);
        DB::table('countries')->insert([
            "CNTR_NAME" => "UAE",
            "CNTR_ARBC_NAME" => "الإمارات العربية المتحدة",
        ]);
        
        DB::table('cities')->insert([
            "CITY_CNTR_ID" => 1,
            "CITY_NAME" => "Cairo",
            "CNTR_ARBC_NAME" => "القاهره",
        ]);
        DB::table('cities')->insert([
            "CITY_CNTR_ID" => 1,
            "CITY_NAME" => "Alex",
            "CNTR_ARBC_NAME" => "الاسكندريه",
        ]);
        DB::table('cities')->insert([
            "CITY_CNTR_ID" => 1,
            "CITY_NAME" => "Suez",
            "CNTR_ARBC_NAME" => "السويس",
        ]);
        DB::table('cities')->insert([
            "CITY_CNTR_ID" => 2,
            "CITY_NAME" => "Dubai",
            "CNTR_ARBC_NAME" => "دبي",
        ]);
        DB::table('cities')->insert([
            "CITY_CNTR_ID" => 2,
            "CITY_NAME" => "Abu Dhabi",
            "CNTR_ARBC_NAME" => "ابو ظبي",
        ]);
        
    }
}
