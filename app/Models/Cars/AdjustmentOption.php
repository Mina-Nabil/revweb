<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdjustmentOption extends Model
{
    public $timestamps = false;
    protected $table = 'adjustments_options';
    protected $appends = ['image_url'];
    ////model functions
    public function updateInfo(string $name, string $image = null, string $desc = null)
    {
        $this->ADOP_NAME = $name;
        if ($image != null) {
            $this->ADOP_IMGE = $image;
        }
        $this->ADOP_DESC = $desc;
        $this->save();
    }

    public function setState(bool $is_active)
    {
        $this->ADOP_ACTV = $is_active;
        $this->save();
    }

    public function setDefault()
    {
        DB::transaction(function () {
            self::where('ADOP_ADJT_ID', $this->ADOP_ADJT_ID)
                ->update(['ADOP_DFLT' => '0']);
            $this->ADOP_DFLT = 1;
            $this->save();
        });
    }

    ////accessors

    public function getImageUrlAttribute()
    {
        return isset($this->ADOP_IMGE) ? Storage::url($this->ADOP_IMGE) : null;
    }

    /////relations
    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(ModelAdjustment::class, 'ADOP_ADJT_ID');
    }
}
