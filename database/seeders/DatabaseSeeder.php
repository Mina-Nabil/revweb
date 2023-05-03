<?php
namespace Database\Seeders;

use Database\Seeds\BrandsSeeder;
use Database\Seeds\CarsTestData;
use Database\Seeds\CarTypes;
use Database\Seeds\CitiesSeedData;
use Database\Seeds\PlansSeeder;
use Database\Seeds\users;
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
        $this->call(BrandsSeeder::class);
        $this->call(CarsTestData::class);
        // $this->call(CitiesSeedData::class);
        // $this->call(PlansSeeder::class);
   
    }
}
