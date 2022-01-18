<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;

class CarAccessory extends Model
{
    protected $table = "accessories_cars";
    public $timestamps = false;

    function car(){
        return $this->belongsTo(Car::class, 'ACCR_CAR_ID');
    }

    function accessory(){
        return $this->belongsTo(Accessories::class, 'ACCR_ACSR_ID');
    }

    function unlink(){
        return $this->delete();
    }

    function setValue($value){
        $this->ACCR_VLUE = $value;
        return $this->save();
    }
}
