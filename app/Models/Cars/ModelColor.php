<?php

namespace App\Models\Cars;

use App\Services\FilesHandler;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModelColor extends Model
{
    protected $table = "model_colors";
    public $timestamps = false;
    protected $appends = ["image_url"];

    public $fillable = [
        "COLR_MODL_ID", "COLR_NAME", "COLR_ARBC_NAME", "COLR_IMGE", "COLR_HEX", "COLR_RED", "COLR_GREN", "COLR_BLUE", "COLR_ALPH"
    ];


    public function getImageUrlAttribute()
    {
        return isset($this->COLR_IMGE) ? Storage::url($this->COLR_IMGE) : null;
    }

    public function editInfo($name, $arbcName, $imageURL, $hex, $red, $green, $blue, $alpha)
    {
        try{
            return $this->update([
                "COLR_NAME" => $name,
                "COLR_ARBC_NAME" => $arbcName,
                "COLR_IMGE" => $imageURL ?? $this->COLR_IMGE,
                "COLR_HEX" => $hex,
                "COLR_RED" => $red,
                "COLR_GREN" => $green,
                "COLR_BLUE" => $blue,
                "COLR_ALPH" => $alpha
            ]);
        }  catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" , self::class]);
            return false;
        }
    }

    public function deleteImage()
    {
        try {
            $filesHandler = new FilesHandler();
            return $this->delete() &&  $filesHandler->deleteFile($this->COLR_IMGE);
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" , self::class]);
            return false;
        }
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, "COLR_MODL_ID");
    }
}
