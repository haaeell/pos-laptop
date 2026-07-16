<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'slug',
        'name',
        'category_id',
        'brand_id',
        'condition',
        'purchase_price',
        'selling_price',
        'strike_price',
        'is_active',
        'status',
        'notes',
        'image',
        'description',
        'stock',
        'weight',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'strike_price'   => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (blank($product->slug) || $product->isDirty('name')) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') || blank($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

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

    public function favoritedBy()
    {
        return $this->belongsToMany(Customer::class, 'product_favorites')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }


    /* ================= HELPERS ================= */

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->stock > 0;
    }

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $base = $base !== '' ? $base : 'produk';
        $slug = $base;
        $suffix = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
