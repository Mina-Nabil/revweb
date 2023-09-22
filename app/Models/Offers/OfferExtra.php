<?php

namespace App\Models\Offers;

use App\Models\Offers\Offer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OfferExtra extends Model
{
    use HasFactory;

    protected $table = 'offer_extras';
    protected $fillable = ['title', 'price', 'note', 'image_url'];
    protected $appends = ['full_url'];


    ///relations
    public function offer()
    {
        return $this->belongsTo(Offer::class, "OFXT_OFFR_ID");
    }  
    
    //Accessors
    public function getFullUrlAttribute()
    {
        return (isset($this->image_url)) ? Storage::url($this->image_url) : null;
    }
}
