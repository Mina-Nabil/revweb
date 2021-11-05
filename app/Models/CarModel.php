<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = "models";
    public $timestamps = false;

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'MODL_BRND_ID');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\CarType', 'MODL_TYPE_ID');
    }

    public function cars()
    {
        return $this->hasMany('App\Models\Car', 'CAR_MODL_ID');
    }

    public function colors()
    {
        return $this->hasMany(ModelColor::class, 'COLR_MODL_ID');
    }

    static function create($brandID, $typeID, $name, $arbcName, $year, $overview, $imagePath = null, $backgroundImgPath = null, $pdfPath = null, $isActive = false, $isMain = false)
    {
        $newModel = new self();
        $newModel->MODL_BRND_ID = $brandID;
        $newModel->MODL_TYPE_ID = $typeID;
        $newModel->MODL_NAME = $name;
        $newModel->MODL_ARBC_NAME = $arbcName;
        // $this->MODL_BRCH = $brochureCode;
        $newModel->MODL_YEAR = $year;
        $newModel->MODL_OVRV = $overview;

        if ($imagePath != null) {
            $newModel->MODL_IMGE = $imagePath;
        }
        if ($backgroundImgPath != null) {
            $newModel->MODL_BGIM = $backgroundImgPath;
        }
        if ($pdfPath != null) {
            $newModel->MODL_PDF = $pdfPath;
        }
        $newModel->MODL_ACTV = $isActive == 'on' ? 1 : 0;
        $newModel->MODL_MAIN = $isMain == 'on' ? 1 : 0;

        try {
            $newModel->save();
            return $newModel;
        } catch (Exception $e) {
            throw $e;
        }
    }


    function toggleMain()
    {
        if ($this->MODL_MAIN == 0) {
            if (isset($this->MODL_IMGE) && strlen($this->MODL_IMGE) > 0 && isset($this->MODL_OVRV) && strlen($this->MODL_OVRV) > 0)
                $this->MODL_MAIN = 1;
        } else {
            $this->MODL_MAIN = 0;
        }
        $this->save();
    }


    function toggleActive()
    {
        if ($this->MODL_ACTV == 0) {
            $this->MODL_ACTV = 1;
        } else {
            $this->MODL_ACTV = 0;
        }
        $this->save();
    }

    static function getModelYears()
    {
        return self::selectRaw('DISTINCT MODL_YEAR')->join('brands', 'brands.id', '=', 'MODL_BRND_ID')
            ->where('MODL_ACTV', 1)->where('BRND_ACTV', 1)->get()->pluck('MODL_YEAR');
    }
}
