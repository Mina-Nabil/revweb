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
            "CAR_PRCE"      =>  200000
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 1,   //BMW
            "CAR_CATG"      => "Baseline",
            "CAR_PRCE"      =>  180000
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 2,   //BMW
            "CAR_CATG"      => "HighLine",
            "CAR_PRCE"      =>  250000
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 2,   //BMW
            "CAR_CATG"      => "High HighLine",
            "CAR_PRCE"      =>  300000
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 3,   //BMW
            "CAR_CATG"      => "MG W7sha",
            "CAR_PRCE"      =>  5000
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 4,   //BMW
            "CAR_CATG"      => "Active",
            "CAR_PRCE"      =>  5000
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 4,   //BMW
            "CAR_CATG"      => "Allure",
            "CAR_PRCE"      =>  5000
        ]);

        DB::table("cars")->insert([
            "CAR_MODL_ID"   => 5,   //BMW
            "CAR_CATG"      => "GT Line (3rbety)",
            "CAR_PRCE"      =>  500000
        ]);
    }
}
