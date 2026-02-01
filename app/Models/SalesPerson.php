<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPerson extends Model
{
    protected $table = 'sales_peoples';

    protected $fillable = [
        'name',
        'phone',
        'active',
    ];
}
