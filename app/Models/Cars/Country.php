<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    protected $table = "countries";

    function cities(){
        return $this->hasMany(City::class, "CITY_CNTR_ID");
    }

    function showrooms(){
        return $this->hasManyThrough(Showroom::class, City::class, "CITY_CNTR_ID", "SHRM_CITY_ID");
    }
}
