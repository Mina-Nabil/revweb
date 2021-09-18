<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = "brands";
    public $timestamps = false;

    function models()
    {
        return $this->hasMany('App\Models\CarModel', 'MODL_BRND_ID');
    }

    function cars()
    {
        return $this->hasManyThrough('App\Models\Car', 'App\Models\CarModel', 'MODL_BRND_ID', 'CAR_MODL_ID');
    }

    function toggle()
    {
        if ($this->BRND_ACTV == 0) {
            if (isset($this->BRND_LOGO) && strlen($this->BRND_LOGO) > 0)
                $this->BRND_ACTV = 1;
        } else {
            $this->BRND_ACTV = 0;
        }
        $this->save();
    }
}
