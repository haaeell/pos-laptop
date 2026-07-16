<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalesPerson extends Model
{
    protected $table = 'sales_peoples';

    protected $fillable = [
        'name',
        'phone',
        'fee',
        'referral_code',
        'active',
        'employee_id',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'sales_person_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'sales_person_id');
    }

    public static function generateReferralCode(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::upper(Str::slug($name, '-'));
        $baseCode = 'SALES-' . ($slug !== '' ? $slug : 'MARKETING');
        $code = $baseCode;
        $suffix = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('referral_code', $code)
            ->exists()) {
            $code = $baseCode . '-' . $suffix;
            $suffix++;
        }

        return $code;
    }
}
