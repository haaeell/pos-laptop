<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $fillable = ['rental_number', 'user_id', 'renter_name', 'address', 'purpose', 'person_in_charge', 'phone', 'rental_dates', 'planned_return_date', 'total_qty', 'daily_rate', 'rental_total', 'fine_amount', 'return_notes', 'status', 'returned_at'];

    protected $casts = [
        'rental_dates' => 'array',
        'planned_return_date' => 'date',
        'returned_at' => 'datetime',
        'daily_rate' => 'decimal:2',
        'rental_total' => 'decimal:2',
        'fine_amount' => 'decimal:2',
    ];

    public function items() { return $this->hasMany(RentalItem::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function getGrandTotalAttribute(): float { return (float) $this->rental_total + (float) $this->fine_amount; }

    public static function generateNumber(): string
    {
        $prefix = 'SW-' . now()->format('Ymd') . '-';
        $last = static::where('rental_number', 'like', $prefix . '%')->orderByDesc('rental_number')->value('rental_number');
        return $prefix . str_pad($last ? (int) substr($last, -4) + 1 : 1, 4, '0', STR_PAD_LEFT);
    }
}
