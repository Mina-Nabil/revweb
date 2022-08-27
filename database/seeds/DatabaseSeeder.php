<?php

use Database\Seeders\BrandsSeeder;
use Database\Seeders\CarsTestData;
use Database\Seeders\CarTypes;
use Database\Seeders\CitiesSeedData;
use Database\Seeders\users;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(users::class);
        // $this->call(CarTypes::class);
        // $this->call(BrandsSeeder::class);
        // $this->call(CarsTestData::class);
        $this->call(CitiesSeedData::class);
   
    }
}
