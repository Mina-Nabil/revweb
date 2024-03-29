<?php

namespace App\Models\Users;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MailVerification extends Model
{

    protected $table = 'mail_verifications';

    ///static functions
    public static function newVerification($mailer, $mail, $code)
    {
        $newVerf = new self;
        $newVerf->code = $code;
        $newVerf->mail = $mail;
        $newVerf->expiry = (new Carbon())->addHours(7);
        $newVerf->mailer()->associate($mailer);

        try {
            $newVerf->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public static function getMailVerfication($mail) : self|null
    {
        return self::where("mail", $mail)->latest()->get()->first();
    }

    //scopes
    public function scopeByUser($query, $mailer)
    {
        return $query->where('mailer_type', $mailer->MORPH_TYPE)->where('mailer_id', $mailer->id);
    }


    ///relations
    public function mailer(): MorphTo
    {
        return $this->morphTo();
    }
}
