<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = "cars";
    public $timestamps = true;
    protected $appends = array('image');
    protected $image;

    protected $casts = [
        'CAR_OFFR' => 'datetime:Y-m-d',
        'CAR_TRND' => 'datetime:Y-m-d',
        'created_at' => 'datetime:d-M-Y H:i',
        'updated_at' => 'datetime:d-M-Y H:i',
    ];



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

    public function toggleOffer()
    {
        if (isset($this->CAR_OFFR)) {
            $this->CAR_OFFR = null;
            if ($this->save()) return 0;
        } else {
            $this->CAR_OFFR = new DateTime();
            if ($this->save()) return 1;
        }
    }

    public function toggleTrending()
    {
        if (isset($this->CAR_TRND)) {
            $this->CAR_TRND = null;
            if ($this->save()) return 0;
        } else {
            $this->CAR_TRND = new DateTime();
            if ($this->save()) return 1;
        }
    }
}
