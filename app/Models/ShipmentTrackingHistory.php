<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentTrackingHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'status',
        'note',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (self $history) {
            $history->created_at ??= now();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
