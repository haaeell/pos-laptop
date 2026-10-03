<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
    protected $fillable = ['rental_id', 'product_id', 'product_name', 'product_code', 'qty', 'accessories', 'condition_out', 'condition_in', 'return_issue'];

    public function rental() { return $this->belongsTo(Rental::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
