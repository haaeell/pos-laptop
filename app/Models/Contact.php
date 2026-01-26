<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'label',
        'phone',
        'whatsapp_text',
        'is_active'
    ];
}
