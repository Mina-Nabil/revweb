<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class BankInfo extends Model
{
    public $timestamps = false;
    protected $table = "banking_info";

    function showroom(){
        return $this->hasOne(Showroom::class, "BANK_SHRM_ID");
    }
}
