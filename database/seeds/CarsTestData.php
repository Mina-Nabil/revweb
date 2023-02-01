<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarsTestData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('models')->insert([
            "MODL_NAME" => "X6",
            "MODL_BRND_ID"  =>  1,   //BMW
            "MODL_TYPE_ID"  =>  2,  //HB
            "MODL_YEAR"     =>  "2022",
        ]);
        DB::table('models')->insert([
            "MODL_NAME" => "X3",
            "MODL_ARBC_NAME"    => "اكس ٣",
            "MODL_BRND_ID"  =>  1,   //BMW
            "MODL_TYPE_ID"  =>  1,  //SEDAN
            "MODL_YEAR"     =>  "2022",
        ]);
        DB::table('models')->insert([
            "MODL_NAME" => "ZX",
            "MODL_ARBC_NAME"    => "اكس ٣ ااا",
            "MODL_BRND_ID"  =>  2,   //MG
            "MODL_TYPE_ID"  =>  1,
            "MODL_YEAR"     =>  "2022",
        ]);

        DB::table('models')->insert([
            "MODL_NAME" => "2008",
            "MODL_ARBC_NAME"    => "2008",
            "MODL_BRND_ID"  =>  3,   //Peageot
            "MODL_TYPE_ID"  =>  3,  //HB
            "MODL_YEAR"     =>  "2022",
        ]);
        DB::table('models')->insert([
            "MODL_NAME" => "301",
            "MODL_ARBC_NAME"    => "301",
            "MODL_BRND_ID"  =>  1,   //Peageot
            "MODL_TYPE_ID"  =>  1,  //Sedan
            "MODL_YEAR"     =>  "2022",
        ]);
        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 1,   //BMW
            "CAR_CATG"      => "HighLine",
            "CAR_PRCE"      =>  200000,
            "CAR_HPWR"      =>  150,
            "CAR_SEAT"      =>  4,
            "CAR_ACC"      =>  6,
            "CAR_ENCC"      =>  1600,
            "CAR_TORQ"      =>  "3000 / 6000 RPM",
            "CAR_TRNS"      =>  "6 Speeds",
            "CAR_TPSP"      =>  220,
            "CAR_HEIT"      =>  50,
            "CAR_RIMS"      =>  16,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "30 * 50",
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 1,   //BMW
            "CAR_CATG"      => "Baseline",
            "CAR_PRCE"      =>  180000,
            "CAR_HPWR"      =>  180,
            "CAR_SEAT"      =>  12,
            "CAR_ACC"      =>  4,
            "CAR_ENCC"      =>  2500,
            "CAR_TORQ"      =>  "3500 / 6000 RPM",
            "CAR_TRNS"      =>  "12 Speeds",
            "CAR_TPSP"      =>  220,
            "CAR_HEIT"      =>  100,
            "CAR_RIMS"      =>  18,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "50 * 50",
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 2,   //BMW
            "CAR_CATG"      => "HighLine",
            "CAR_PRCE"      =>  250000,
            "CAR_HPWR"      =>  100,
            "CAR_SEAT"      =>  12,
            "CAR_ACC"      =>  4,
            "CAR_ENCC"      =>  2500,
            "CAR_TORQ"      =>  "3500 / 6000 RPM",
            "CAR_TRNS"      =>  "12 Speeds",
            "CAR_TPSP"      =>  120,
            "CAR_HEIT"      =>  100,
            "CAR_RIMS"      =>  18,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "100 * 100",
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 2,   //BMW
            "CAR_CATG"      => "High HighLine",
            "CAR_PRCE"      =>  300000,
            "CAR_HPWR"      =>  130,
            "CAR_SEAT"      =>  5,
            "CAR_ACC"      =>  4,
            "CAR_ENCC"      =>  3000,
            "CAR_TORQ"      =>  "3500 / 6000 RPM",
            "CAR_TRNS"      =>  "12 Speeds",
            "CAR_TPSP"      =>  120,
            "CAR_HEIT"      =>  100,
            "CAR_RIMS"      =>  18,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "100 * 100",
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 3,   //BMW
            "CAR_CATG"      => "MG W7sha",
            "CAR_PRCE"      =>  5000,
            "CAR_HPWR"      =>  100,
            "CAR_SEAT"      =>  12,
            "CAR_ACC"      =>  4,
            "CAR_ENCC"      =>  2500,
            "CAR_TORQ"      =>  "3500 / 6000 RPM",
            "CAR_TRNS"      =>  "12 Speeds",
            "CAR_TPSP"      =>  120,
            "CAR_HEIT"      =>  100,
            "CAR_RIMS"      =>  18,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "100 * 100",
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 4,   //BMW
            "CAR_CATG"      => "Active",
            "CAR_PRCE"      =>  5000,
            "CAR_HPWR"      =>  100,
            "CAR_SEAT"      =>  12,
            "CAR_ACC"      =>  4,
            "CAR_ENCC"      =>  2500,
            "CAR_TORQ"      =>  "3500 / 6000 RPM",
            "CAR_TRNS"      =>  "12 Speeds",
            "CAR_TPSP"      =>  120,
            "CAR_HEIT"      =>  100,
            "CAR_RIMS"      =>  18,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "100 * 100",
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 4,   //BMW
            "CAR_CATG"      => "Allure",
            "CAR_PRCE"      =>  5000,
            "CAR_HPWR"      =>  100,
            "CAR_SEAT"      =>  12,
            "CAR_ACC"      =>  4,
            "CAR_ENCC"      =>  2500,
            "CAR_TORQ"      =>  "3500 / 6000 RPM",
            "CAR_TRNS"      =>  "12 Speeds",
            "CAR_TPSP"      =>  120,
            "CAR_HEIT"      =>  100,
            "CAR_RIMS"      =>  18,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "100 * 100",
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 5,   //BMW
            "CAR_CATG"      => "GT Line (3rbety)",
            "CAR_PRCE"      =>  500000,
            "CAR_HPWR"      =>  100,
            "CAR_SEAT"      =>  12,
            "CAR_ACC"      =>  4,
            "CAR_ENCC"      =>  2500,
            "CAR_TORQ"      =>  "3500 / 6000 RPM",
            "CAR_TRNS"      =>  "12 Speeds",
            "CAR_TPSP"      =>  120,
            "CAR_HEIT"      =>  100,
            "CAR_RIMS"      =>  18,
            "CAR_TRNK"      =>  50,
            "CAR_DIMN"      =>  "100 * 100",
        ]);
    }
}
