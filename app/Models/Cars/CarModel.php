<?php

namespace App\Models\Cars;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CarModel extends Model
{
    protected $table = "models";
    public $timestamps = false;
    protected $appends = ['image_url', 'pdf_url'];
    protected $with = ["brand", "type"];


    ////static functions
    static function create($brandID, $typeID, $name, $arbcName, $year, $overview, $imagePath = null, $pdfPath = null, int $isActive = 0)
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
        if ($pdfPath != null) {
            $newModel->MODL_BRCH = $pdfPath;
        }
        $newModel->MODL_ACTV = $isActive;

        try {
            $newModel->save();
            return $newModel;
        } catch (Exception $e) {
            Log::alert($e->getMessage());
            return false;
        }
    }

    public static function getModelIDs($carIDs = []): array
    {
        return DB::table('models')->join('cars', 'cars.CAR_MODL_ID', '=', 'models.id')
            ->selectRaw('DISTINCT models.id')
            ->whereIn("cars.id", $carIDs)
            ->get()->pluck('id')->toArray();
    }

    ////model functions
    function updateInfo($brand, $type, $name, $arbcName, $year, $overview, $imagePath = null, $pdfPath = null, int $isActive = 0)
    {
        $this->MODL_BRND_ID = $brand;
        $this->MODL_TYPE_ID = $type;
        $this->MODL_NAME = $name;
        $this->MODL_ARBC_NAME = $arbcName;
        $this->MODL_YEAR = $year;
        if ($imagePath != null) {
            $this->MODL_IMGE = $imagePath;
        }
        if ($pdfPath != null) {
            $this->MODL_BRCH = $pdfPath;
        }
        $this->MODL_ACTV = $isActive;
        $this->MODL_OVRV = $overview;

        try {
            $this->save();
            return $this;
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            return false;
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

    ///////relations
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'MODL_BRND_ID');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CarType::class, 'MODL_TYPE_ID');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'CAR_MODL_ID');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ModelColor::class, 'COLR_MODL_ID');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ModelImage::class, 'MOIM_MODL_ID');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(ModelAdjustment::class, 'ADJT_MODL_ID');
    }


    ///////attributes
    public function getImageUrlAttribute()
    {
        return (isset($this->MODL_IMGE)) ? Storage::url($this->MODL_IMGE) : null;
    }

    public function getPdfUrlAttribute()
    {
        return (isset($this->attributes['MODL_BRCH'])) ? Storage::url($this->attributes['MODL_BRCH']) : null;
    }

    public function getTitleAttribute()
    {
        return $this->MODL_NAME . " " . $this->MODL_YEAR;
    }
}
