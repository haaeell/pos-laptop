<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalRateTier extends Model
{
    protected $fillable = ['min_qty', 'daily_rate'];

    protected $casts = ['daily_rate' => 'decimal:2'];
}
