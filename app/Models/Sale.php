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
        'paid_amount',
        'collateral_path',
        'due_date',
        'customer_name',
        'customer_phone',
        'sales_person_id',
        'fee_sales'
    ];

    protected $casts = [
        'discount'     => 'decimal:2',
        'grand_total'  => 'decimal:2',
        'benefit'      => 'decimal:2',
        'fee_sales'   => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date'    => 'date',
    ];

    /* ================= ACCESSORS ================= */

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->grand_total - $this->paid_amount);
    }

    public function getCollateralUrlAttribute()
    {
        return $this->collateral_path ? asset('storage/' . $this->collateral_path) : null;
    }

    /* ================= RELATIONS ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salesPerson()
    {
        return $this->belongsTo(SalesPerson::class);
    }


    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function bonuses()
    {
        return $this->hasMany(SaleBonus::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
