<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;
    protected $table = "cities";

    function showrooms(){
        return $this->hasMany(Showroom::class, "SHRM_CITY_ID");
    }

    function country(){
        return $this->belongsTo(Country::class, "CITY_CNTR_ID");
    }
}
