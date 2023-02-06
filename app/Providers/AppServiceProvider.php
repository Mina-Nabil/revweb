<?php

namespace App\Providers;

use App\Models\Users\Buyer;
use App\Models\Users\Seller;
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
        Relation::enforceMorphMap([
            "seller"    =>  Seller::class,
            "buyer"     =>  Buyer::class
        ]);
    }
}
