<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdjustmentOption extends Model
{
    public $timestamps = false;
    protected $table = 'adjustments_options';

    ////model functions
    public function updateInfo(string $name, string $image, bool $is_default=false, string $desc=null)
    {
        $this->ADOP_NAME = $name;
        $this->ADOP_IMGE = $image;
        $this->ADOP_DFLT = $is_default;
        $this->ADOP_DESC = $desc;
        $this->save();
    }

    public function setState(bool $is_active){
        $this->ADOP_ACTV = $is_active;
        $this->save();
    }

    /////relations
    public function adjustment():BelongsTo
    {
        return $this->belongsTo(ModelAdjustment::class, 'ADOP_ADJT_ID');
    } 
}
