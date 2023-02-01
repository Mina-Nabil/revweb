<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("dash_types")->insert([
            "DHTP_NAME" => "admin"
        ]);

        DB::table('dash_users')->insert([
            "DASH_USNM" => "mina",
            "DASH_FLNM" => "Mina Nabil",
            "DASH_PASS" => bcrypt('mina@revmo'),           
            "DASH_TYPE_ID" => 1,
        ]);

        DB::table('dash_users')->insert([
            "DASH_USNM" => "steven",
            "DASH_FLNM" => "Steven Ashraf",
            "DASH_PASS" => bcrypt('steven123'),           
            "DASH_TYPE_ID" => 1,
        ]);

        DB::table('dash_users')->insert([
            "DASH_USNM" => "adel",
            "DASH_FLNM" => "Mr. Adel",
            "DASH_PASS" => bcrypt('adel123'),           
            "DASH_TYPE_ID" => 1,
        ]);
    }
}
