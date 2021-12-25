<?php

namespace App\Models;

use App\Services\FilesHandler;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $table = "brands";
    public $timestamps = false;
    protected $appends = ['logo_url'];

    static function create($name, $arbcName, $isActive, $logoPath = null, $imagePath = null)
    {
        $newBrand = new self();
        $newBrand->BRND_NAME = $name;
        $newBrand->BRND_ARBC_NAME = $arbcName;
        $newBrand->BRND_LOGO = $logoPath;
        $newBrand->BRND_IMGE = $imagePath;
        $newBrand->BRND_ACTV = $isActive;
        try {
            $newBrand->save();
            return $newBrand;
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }

    static function getActive()
    {
        return self::where("BRND_ACTV", 1)->get();
    }

    public function updateInfo($name, $arbcName, $isActive, $logoPath = null, $imagePath = null)
    {
        $filesHandler = new FilesHandler();
        $this->BRND_NAME = $name;
        $this->BRND_ARBC_NAME = $arbcName;
        if ($logoPath != null) {
            $this->BRND_LOGO = $logoPath;
        }
        if ($imagePath != null) {
            $this->BRND_IMGE = $imagePath;
        }
        $this->BRND_ACTV = $isActive;
        try {
            return $this->save();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }

    public function getLogoUrlAttribute()
    {
        return (isset($this->attributes['BRND_LOGO'])) ? Storage::url($this->attributes['BRND_LOGO']) : null;
    }

    function activeModels()
    {
        return $this->models()->with("type", "colors", "images", "cars", "cars.images", "brand")->where(["MODL_ACTV" => 1], ["CAR_ACTV" => 1])->get();
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
        try {
            $this->save();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }
}
