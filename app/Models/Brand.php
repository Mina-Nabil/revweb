<?php

namespace App\Models;

use App\Services\FilesHandler;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $table = "brands";
    public $timestamps = false;

    static function create($name, $arbcName, $isActive, $logoPath = null, $imagePath = null)
    {
        $newBrand = new self();
        $newBrand->BRND_NAME = $name;
        $newBrand->BRND_ARBC_NAME = $arbcName;
        $newBrand->BRND_LOGO = $logoPath;
        $newBrand->BRND_LOGO = $imagePath;
        $newBrand->BRND_ACTV = $isActive == 'on' ? 1 : 0;
        try {
            $newBrand->save();
            return $newBrand;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateInfo($name, $arbcName, $isActive, $logoPath = null, $imagePath = null)
    {
        $filesHandler = new FilesHandler();
        $this->BRND_NAME = $name;
        $this->BRND_ARBC_NAME = $arbcName;
        if ($logoPath != null) {
            if ($this->BRND_LOGO != null)
                try {
                    $filesHandler->deleteFile($this->BRND_LOGO);
                } catch (Exception $e) {
                    throw $e;
                }
            $this->BRND_LOGO = $logoPath;
        }
        if ($imagePath != null) {
            if ($this->BRND_IMGE != null)
                try {
                    $filesHandler->deleteFile($this->BRND_IMGE);
                } catch (Exception $e) {
                    throw $e;
                }
            $this->BRND_IMGE = $imagePath;
        }
        $this->BRND_ACTV = $isActive == 'on' ? 1 : 0;
        try {
            $this->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function setBRNDLOGOAttribute($value)
    {
        $this->attributes['BRND_LOGO'] = Storage::url($value);
    }

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
