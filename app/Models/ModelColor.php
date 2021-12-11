<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ModelColor extends Model
{
    protected $table = "model_colors";
    public $timestamps = false;
    protected $appends = ["image_url"];

    public function getImageUrlAttribute(){
        return Storage::url($this->COLR_IMGE);
    }

    public $fillable = [
        "COLR_MODL_ID", "COLR_NAME", "COLR_ARBC_NAME", "COLR_IMGE", "COLR_HEX", "COLR_RED", "COLR_GREN", "COLR_BLUE", "COLR_ALPH"
    ];

    public function editInfo($name, $arbcName, $imageURL, $hex, $red, $green, $blue, $alpha){
        return $this->update([
            "COLR_NAME" => $name,
            "COLR_ARBC_NAME" => $arbcName,
            "COLR_IMGE" => $imageURL ?? NULL,
            "COLR_HEX" => $hex,
            "COLR_RED" => $red,
            "COLR_GREN" => $green,
            "COLR_BLUE" => $blue,
            "COLR_ALPH" => $alpha
        ]);
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, "COLR_MODL_ID");
    }
}
