<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'show_as_partner',
    ];

    protected $casts = [
        'show_as_partner' => 'boolean',
    ];

    /* ================= RELATIONS ================= */

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
