<?php

namespace App\Models\Users;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    protected $fillable = ["JNRQ_SHRM_ID", "JNRQ_SLLR_ID", "JNRQ_STTS"];

    public function seller()
    {
        return $this->belongsTo(Seller::class, "JNRQ_SLLR_ID");
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, "JNRQ_SHRM_ID");
    }

    public function acceptRequest()
    {
        try {
            DB::transaction(function () {
                $this->setStatus(JoinRequest::ACCEPTED);
                $this->load("seller");
                $this->seller->setShowroom($this->JNRQ_SHRM_ID);
                self::where('JNRQ_SLLR_ID', $this->seller->id)->where('id', '!=', $this->id)->delete();
            });
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function removeRequest()
    {
        $this->delete();
    }

    public function setStatus(string $status)
    {
        if (in_array($status, JoinRequest::STATES)) {
            $this->JNRQ_STTS = $status;
            return $this->save();
        }
    }
}
