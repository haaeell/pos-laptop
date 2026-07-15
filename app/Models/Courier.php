<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = ['code', 'name', 'logo', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
