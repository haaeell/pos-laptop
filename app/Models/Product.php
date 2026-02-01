<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'name',
        'category_id',
        'brand_id',
        'condition',
        'purchase_price',
        'selling_price',
        'status',
        'notes',
        'image',
        'description',
        'stock',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
    ];

    /* ================= RELATIONS ================= */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function saleItem()
    {
        return $this->hasOne(SaleItem::class);
    }

    public function saleBonus()
    {
        return $this->hasOne(SaleBonus::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


    /* ================= HELPERS ================= */

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
