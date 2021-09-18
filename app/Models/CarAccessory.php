<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarAccessory extends Model
{
    protected $table = "accessories_cars";
    public $timestamps = false;

    function car(){
        return $this->belongsTo('App\Models\Car', 'ACCR_CAR_ID');
    }

    function accessory(){
        return $this->belongsTo('App\Models\Accessories', 'ACCR_ACSR_ID');
    }

    function unlink(){
        return $this->delete();
    }

    function setValue($value){
        $this->ACCR_VLUE = $value;
        return $this->save();
    }
}
