<?php

namespace App\Models\Users;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DashUser extends Authenticatable
{
    use Notifiable;
    protected $table = "dash_users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'DASH_USNM', 'DASH_FLNM', 'DASH_PASS', 'DASH_IMGE', 'DASH_TYPE_ID',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'remember_token',
    ];

    public function getAuthPassword(){
        return $this->DASH_PASS;
    }

    public function dash_types(){
        return $this->hasOne( DashType::class , 'id', 'DASH_TYPE_ID');
    }
}
