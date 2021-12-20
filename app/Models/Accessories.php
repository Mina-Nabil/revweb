<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Accessories extends Model
{
    protected $table = "accessories";
    public $timestamps = false;

    function create($name, $arabicName)
    {
        $this->ACSR_NAME = $name;
        $this->ACSR_ARBC_NAME = $arabicName;
        try {
            $this->save();
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
