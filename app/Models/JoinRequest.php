<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    protected $table = "join_requests";
    public const REQ_BY_SELLER = "Seller Requested";
    public const REQ_BY_SHOWROOM = "Showroom Requested";
    public const ACCEPTED = "Joined";
    public const STATES = [
        0 => self::REQ_BY_SELLER,
        1 => self::REQ_BY_SHOWROOM,
        2 => self::ACCEPTED,
    ];

    public function seller(){
        return $this->belongsTo(Seller::class, "JNRQ_SLLR_ID");
    }

    public function showroom(){
        return $this->belongsTo(Seller::class, "JNRQ_SHRM_ID");
    }

    public function acceptRequest(){
        $this->setStatus(JoinRequest::ACCEPTED);
        $this->load("seller");
        return $this->seller->setShowroom($this->JNRQ_SHRM_ID);
    }

    public function removeRequest(){
        $this->delete();
    }

    public function setStatus(string $status){
        if(in_array($status, JoinRequest::STATES))
        $this->JNRQ_STTS = $status;
        return $this->save();
    }
}
