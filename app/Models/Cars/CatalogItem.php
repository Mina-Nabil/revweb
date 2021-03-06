<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    protected $table = "showroom_catalog";
    protected $with = ["car", "colors", 'car.model', 'car.model.colors', 'car.model.images', 'car.images'];
    public $timestamps = false;

    public function car()
    {
        return $this->belongsTo(Car::class, "SRCG_CAR_ID");
    }

    public function details()
    {
        return $this->hasMany(CatalogItemDetails::class, "SRCD_SRCG_ID");
    }

    public function colors()
    {
        return $this->belongsToMany(ModelColor::class, CatalogItemDetails::class, "SRCD_SRCG_ID", "SRCD_COLR_ID");
    }

    protected $fillable = ['SRCG_CAR_ID', 'SRCG_DEF_PRCE', 'SRCG_MAX_DAYS', 'SRCG_MIN_PYMT', 'SRCG_CAR_ACTV'];
}
