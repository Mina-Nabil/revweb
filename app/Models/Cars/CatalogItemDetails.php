<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;

class CatalogItemDetails extends Model
{
    protected $table = "showroom_catalog_details";
    public $timestamps = false;

    protected $fillable = ['SRCD_CAR_ID', 'SRCD_COLR_ID'];
}
