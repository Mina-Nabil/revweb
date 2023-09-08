<?php

namespace App\Models\Models\Offers;

use App\Models\Offers\Offer;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferDoc extends Model
{
    use HasFactory;

    protected $table = 'offer_docs';
    protected $fillable = ['title', 'doc_url', 'note'];

    //functions
    public function setUrl($url) : bool
    {
        $this->doc_url = $url;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    ///relations
    public function offer()
    {
        return $this->belongsTo(Offer::class, "OFDC_OFFR_ID");
    }
}
