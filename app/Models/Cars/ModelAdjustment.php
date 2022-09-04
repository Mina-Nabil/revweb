<?php

namespace App\Models\Cars;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelAdjustment extends Model
{
    protected $table = 'model_adjustments';
    public $timestamps = false;
    protected $with = ['options'];
    protected $appends = ['default_option'];

    //////static queries
    public static function newModelAdjustment(int $modelID, string $name, string $desc = null): self
    {
        $newAdjustment = new self;
        $newAdjustment->ADJT_MODL_ID = $modelID;
        $newAdjustment->ADJT_NAME = $name;
        $newAdjustment->ADJT_DESC = $desc;

        if ($newAdjustment->save()) return $newAdjustment;
        else return false;
    }
    ///////model functions
    public function updateInfo(string $name, string $desc = null): bool
    {
        $this->ADJT_NAME = $name;
        $this->ADJT_DESC = $desc;
        return $this->save();
    }

    public function setActiveState(bool $state): bool
    {
        $this->ADJT_ACTV = $state ? 1 : 0;
        return $this->save();
    }

    public function addOption(string $name, string $image = null, string $desc = null)
    {
        $newOption = new AdjustmentOption;
        $newOption->ADOP_NAME = $name;
        $newOption->ADOP_IMGE = $image;
        $newOption->ADOP_DFLT = $this->options()->count() == 0;
        $newOption->ADOP_DESC = $desc;
        return $this->options()->save($newOption);
    }

    ////accessors
    public function getDefaultOptionAttribute()
    {
        $this->loadMissing('options');
        return $this->options()->where('ADOP_DFLT', 1)->first();
    }

    //////relations
    public function options(): HasMany
    {
        return $this->hasMany(AdjustmentOption::class, 'ADOP_ADJT_ID');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'ADJT_MODL_ID');
    }
}
