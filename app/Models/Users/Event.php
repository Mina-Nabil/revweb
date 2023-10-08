<?php

namespace App\Models\Users;

use App\Models\Offers\Offer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $fillable = [
        'seller_id',
        'buyer_id',
        'showroom_id',
        'offer_id',
        'title',
        'note',
        'start',
        'end',
        'location',
        'notification_time'
    ];

    ///static functions
    public static function newEvent(
        $seller_id,
        $buyer_id,
        $showroom_id,
        $offer_id,
        $title,
        $note,
        $start,
        $end,
        $location,
        $notification_time
    ) {
        $newEvent = new self([
            'seller_id' => $seller_id,
            'buyer_id' => $buyer_id,
            'showroom_id' => $showroom_id,
            'offer_id' => $offer_id,
            'title' => $title,
            'note' => $note,
            'start' => $start,
            'end' => $end,
            'location' => $location,
            'notification_time' => $notification_time,
        ]);

        try {
            $newEvent->save();
            return $newEvent;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    ///model functions
    public function editInfo(
        $title,
        $note,
        $start,
        $end,
        $location,
        $notification_time,
    ) {
        $this->update([
            'title' => $title,
            'note' => $note,
            'start' => $start,
            'end' => $end,
            'location' => $location,
            'notification_time' => $notification_time,
        ]);

        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    ///scopes
    public function scopeByUser($query, $type, $user_id)
    {
        switch ($type) {
            case 'seller':
                return $query->where('seller_id', $user_id);
            case 'buyer':
                return $query->where('buyer_id', $user_id);
            default:
                return $query->where('buyer_id', 0);
        }
    }

    public function scopeFromTo($query, Carbon $from, Carbon $to)
    {
        return $query->where(function ($query) use ($from, $to) {
            $query->whereBetween('start', [
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s')
            ])
                ->orWhereBetween('end', [
                    $from->format('Y-m-d H:i:s'),
                    $to->format('Y-m-d H:i:s')
                ]);
        });
    }
    ///relations
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
