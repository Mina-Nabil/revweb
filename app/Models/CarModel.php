<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = "models";
    public $timestamps = false;

    public function brand(){
        return $this->belongsTo('App\Models\Brand', 'MODL_BRND_ID');
    }

    public function type(){
        return $this->belongsTo('App\Models\CarType', 'MODL_TYPE_ID');
    }

    public function cars(){
        return $this->hasMany('App\Models\Car', 'CAR_MODL_ID');
    }

    public function colorImages(){
        return $this->hasMany('App\Models\ModelImage', 'MOIM_MODL_ID');
    }


    function toggleMain(){
        if($this->MODL_MAIN == 0) {
            if(isset($this->MODL_IMGE) && strlen($this->MODL_IMGE)>0 && isset($this->MODL_OVRV) && strlen($this->MODL_OVRV)>0)
            $this->MODL_MAIN = 1;
        } else {
            $this->MODL_MAIN = 0;
        }
        $this->save();
    }


    function toggleActive(){
        if($this->MODL_ACTV == 0) {
            $this->MODL_ACTV = 1;
        } else {
            $this->MODL_ACTV = 0;
        }
        $this->save();
    }

    static function getModelYears(){
        return self::selectRaw('DISTINCT MODL_YEAR')->join('brands', 'brands.id', '=', 'MODL_BRND_ID')
                    ->where('MODL_ACTV', 1)->where('BRND_ACTV', 1)->get()->pluck('MODL_YEAR');
    }
}
