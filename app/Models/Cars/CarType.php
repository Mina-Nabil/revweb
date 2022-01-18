<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;

class CarType extends Model
{
    protected $table = "types";
    public $timestamps = false;

    function models(){
        return $this->hasMany(CarModel::class, 'MODL_TYPE_ID');
    }

    function cars(){
        return $this->hasManyThrough(Car::class, CarModel::class, 'MODL_TYPE_ID', 'CAR_MODL_ID');
    }

    function active_cars(){
        return $this->hasManyThrough(Car::class, CarModel::class, 'MODL_TYPE_ID', 'CAR_MODL_ID')->where('MODL_ACTV', 1);
    }

}
