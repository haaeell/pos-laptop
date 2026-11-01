<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BookPeriod extends Model
{
    protected $fillable = ['starts_at', 'ends_at', 'opening_balance', 'snapshot'];

    protected $casts = ['starts_at' => 'date', 'ends_at' => 'date', 'opening_balance' => 'decimal:2', 'snapshot' => 'array'];

    public static function active(): ?self
    {
        return static::whereNull('ends_at')->first();
    }

    public static function closedOn($date): bool
    {
        return static::whereNotNull('ends_at')->whereDate('ends_at', '>=', Carbon::parse($date)->toDateString())->exists();
    }
}
