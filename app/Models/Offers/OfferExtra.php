<?php

namespace App\Models\Offers;

use App\Models\Offers\Offer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferExtra extends Model
{
    use HasFactory;

    protected $table = 'offer_extras';
    protected $fillable = ['title', 'price', 'note'];


    ///relations
    public function offer()
    {
        return $this->belongsTo(Offer::class, "OFXT_OFFR_ID");
    }
}
