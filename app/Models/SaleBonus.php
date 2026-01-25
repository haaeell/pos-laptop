<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'purchase_price',
        'benefit',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'benefit'        => 'decimal:2',
    ];

    /* ================= RELATIONS ================= */

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
