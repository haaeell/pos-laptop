<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'show_on_customer_site',
    ];

    protected $casts = [
        'show_on_customer_site' => 'boolean',
    ];

    /* ================= RELATIONS ================= */

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
