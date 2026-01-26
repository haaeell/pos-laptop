<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'discount',
        'grand_total',
        'benefit',
        'payment_method',
        'payment_status',
        'customer_name',
        'customer_phone',
    ];

    protected $casts = [
        'discount'     => 'decimal:2',
        'grand_total'  => 'decimal:2',
        'benefit'      => 'decimal:2',
    ];

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function bonuses()
    {
        return $this->hasMany(SaleBonus::class);
    }
}
