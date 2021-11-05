<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

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
            throw $e;
        }
    }
}
