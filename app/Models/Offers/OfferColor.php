<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Model;

class OfferColor extends Model
{
    protected $table = "offer_colors";
    public $timestamps = false;
    protected $with = ["model_color"];
    protected $fillable = ["OFCL_COLR_ID"];

    public function model_color()
    {
        return $this->belongsTo(ModelColor::class, "OFRC_COLR_ID");
    }
}
