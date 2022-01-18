<?php

namespace App\Models\Cars;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Accessories extends Model
{
    protected $table = "accessories";
    public $timestamps = false;

    static function create($name, $arabicName)
    {
        $newAccessory = new Accessories();
        $newAccessory->ACSR_NAME = $name;
        $newAccessory->ACSR_ARBC_NAME = $arabicName;
        try {
            $newAccessory->save();
            return $newAccessory;
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }

    function updateInfo($name, $arabicName)
    {
        $this->ACSR_NAME = $name;
        $this->ACSR_ARBC_NAME = $arabicName;
        try {
            $this->save();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class] );
            throw $e;
        }
    }
}
