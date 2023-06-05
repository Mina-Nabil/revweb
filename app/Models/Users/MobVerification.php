<?php

namespace App\Models\Users;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MobVerification extends Model
{

    protected $table = 'mob_verifications';

    ///static functions
    public static function newVerification($mober, $mob, $code)
    {
        $newVerf = new self;
        $newVerf->code = $code;
        $newVerf->mob = $mob;
        $newVerf->expiry = (new Carbon())->addHours(7);
        $newVerf->mober()->associate($mober);

        try {
            $newVerf->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public static function getMobVerfication($mob) : self|null
    {
        return self::where("mob", $mob)->latest()->get()->first();
    }

    //scopes
    public function scopeByUser($query, $mober)
    {
        return $query->where('mober_type', $mober->MORPH_TYPE)->where('mober_id', $mober->id);
    }


    ///relations
    public function mober(): MorphTo
    {
        return $this->morphTo();
    }
}
