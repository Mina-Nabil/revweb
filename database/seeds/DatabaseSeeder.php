<?php

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
        $this->call(users::class);
        $this->call(CarTypes::class);
        $this->call(BrandsSeeder::class);
        $this->call(CarsTestData::class);
   
    }
}
