<?php

use App\Models\Subscriptions\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::createPlan("Free Plan", 0, 0, 1, 1, 5, 50, 1, true, true, false, false, false, 0);
        Plan::createPlan("Pro Plan", 299, 269, 1, 4, 10, 100, 2, true, true, true, false, false, 50);
        Plan::createPlan("Business Plan", 499, 449, -1, -1, -1, -1, -1, true, true, true, true, true, 100);
    }
}
