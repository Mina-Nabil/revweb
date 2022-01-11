<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Car extends Model
{
    protected $table = "cars";
    public $timestamps = true;
    protected $appends = array('image', 'image_url');
    protected $with = ['accessories'];
    protected $image;

    protected $casts = [
        'CAR_OFFR' => 'datetime:Y-m-d',
        'CAR_TRND' => 'datetime:Y-m-d',
        'created_at' => 'datetime:d-M-Y H:i',
        'updated_at' => 'datetime:d-M-Y H:i',
    ];


    ////Catalog Functions
    /***
     * load cars by brand ID
     * @param brandIDs array
     */
    static public function getCarsByBrandIDs($brandIDs)
    {

        return self::join("models", "CAR_MODL_ID", "=", "models.id")
            ->join("brands", "MODL_BRND_ID", "=", "brands.id")
            ->whereIn("brands.id", $brandIDs)
            ->select("cars.*", "models.MODL_NAME", "models.MODL_ARBC_NAME", "models.id as MODL_ID", "brands.BRND_NAME", "brands.BRND_ARBC_NAME", "brands.id as BRND_ID")
            ->get();
    }

    static public function getCarsByModel($modelID)
    {
        return self::with("images", "accessories")->where(["CAR_MODL_ID" => $modelID, "CAR_ACTV" => 1])->get();
    }

    public function getImageAttribute()
    {
        if (isset($this->image)) return $this->image;

        $mainImage = $this->images()->orderByDesc('CIMG_VLUE')->first();
        if ($mainImage) {
            $this->image = $mainImage->CIMG_URL;
            return $mainImage->CIMG_URL;
        } else {
            $this->image = $this->model->MODL_IMGE ?? null;
        }
        return $this->image;
    }

    public function getisOwnedAttribute(){

    }

    public function getImageUrlAttribute()
    {
        if (isset($this->image)) return Storage::url($this->image);

        $mainImage = $this->images()->orderByDesc('CIMG_VLUE')->first();
        if ($mainImage) {
            $this->image = $mainImage->CIMG_URL;
            return Storage::url($mainImage->CIMG_URL);
        } else {
            $this->image = $this->model->MODL_IMGE ?? null;
        }
        return Storage::url($this->image);
    }

    public function model()
    {
        return $this->belongsTo('App\Models\CarModel', 'CAR_MODL_ID');
    }

    public function accessories()
    {
        return $this->belongsToMany('App\Models\Accessories', "accessories_cars", "ACCR_CAR_ID", "ACCR_ACSR_ID")
            ->withPivot('ACCR_VLUE');
    }

    public function getAccessories()
    {
        return $this->join('accessories_cars', 'cars.id', '=', 'ACCR_CAR_ID')
            ->join('accessories', 'ACCR_ACSR_ID', '=', 'accessories.id')
            ->select('ACCR_VLUE', 'ACCR_ACSR_ID', 'ACCR_CAR_ID', 'ACSR_NAME', 'ACSR_ARBC_NAME')
            ->where('ACCR_CAR_ID', $this->id)
            ->get();
    }

    public function addImage($imageURL, $sortValue)
    {
        $this->images()->create([
            "CIMG_VLUE" =>  $sortValue,
            "CIMG_URL"  =>  $imageURL
        ]);
    }

    public function deleteImage($id)
    {
        $image = CarImage::findOrFail($id);
        $image->deleteImage();
    }

    public function images()
    {
        return $this->hasMany('App\Models\CarImage', 'CIMG_CAR_ID');
    }

    public function getFullAccessoriesArray()
    {
        //Accessories table
        $allAccessories = Accessories::all();
        $carAccessories = $this->getAccessories()->pluck('ACCR_VLUE', 'ACCR_ACSR_ID')->toArray();


        $accessories = [];
        foreach ($allAccessories as $accessory) {
            if (key_exists($accessory->id, $carAccessories)) {
                $accessories[$accessory->id] = ['ACSR_ARBC_NAME' =>  $accessory->ACSR_ARBC_NAME, 'ACSR_NAME' =>  $accessory->ACSR_NAME, 'isAvailable' => true, 'ACCR_VLUE' => $carAccessories[$accessory->id]];
            } else {
                $accessories[$accessory->id] = ['ACSR_ARBC_NAME' =>  $accessory->ACSR_ARBC_NAME, 'ACSR_NAME' =>  $accessory->ACSR_NAME, 'isAvailable' => false];
            }
        }
        return $accessories;
    }
}
