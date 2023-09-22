<?php

namespace App\Models\Offers;

use App\Models\Offers\Offer;
use App\Services\FilesHandler;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OfferDoc extends Model
{
    use HasFactory;

    protected $table = 'offer_docs';
    protected $fillable = ['title', 'doc_url', 'note', 'is_seller'];

    //functions
    public function setUrl($url): bool
    {
        $this->doc_url = $url;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function delete()
    {
        $fileHandler = new FilesHandler();
        $fileHandler->deleteFile($this->doc_url);
        return parent::delete();
    }

    public function deleteImage()
    {
        $fileHandler = new FilesHandler();
        $fileHandler->deleteFile($this->doc_url);
        $this->doc_url = null;
        return $this->save();
    }

    ///relations
    public function offer()
    {
        return $this->belongsTo(Offer::class, "OFDC_OFFR_ID");
    }
}
