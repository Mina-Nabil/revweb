<?php

namespace App\Models\Offers;

use App\Models\Cars\ModelColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferRequestColors extends Model
{
    use SoftDeletes;

    protected $table = "offers_requests_colors";
    protected $with = ["model_color"];
    protected $fillable = ["OFRC_COLR_ID", "OFRC_PRTY"];
    public $timestamps = false;

    public function model_color()
    {
        return $this->belongsTo(ModelColor::class, "OFRC_COLR_ID");
    }
}
