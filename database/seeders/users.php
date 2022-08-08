<?php

namespace Database\Seeders;

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
    }
}
