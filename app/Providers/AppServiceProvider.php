<?php

namespace App\Providers;

use App\Models\Subscriptions\Subscription;
use App\Models\Users\Buyer;
use App\Models\Users\Seller;
use App\Models\Users\Showroom;
use App\Services\FilesHandler;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Seller::MORPH_TYPE    =>  Seller::class,
            Buyer::MORPH_TYPE     =>  Buyer::class,
            Showroom::MORPH_TYPE     =>  Showroom::class,
            Subscription::MORPH_TYPE => Subscription::class
        ]);
    }
}
