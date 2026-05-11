<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'payroll_number',
        'period_year',
        'period_month',
        'release_date',
        'total_amount',
        'total_basic_salary',
        'total_sales_bonus',
        'total_technician_fee',
        'status',
        'notes',
        'released_by',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public static function generatePayrollNumber($year, $month)
    {
        $lastPayroll = self::where('period_year', $year)
            ->where('period_month', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastPayroll
            ? intval(substr($lastPayroll->payroll_number, -3)) + 1
            : 1;

        return 'PAY-'
            . $year
            . str_pad($month, 2, '0', STR_PAD_LEFT)
            . '-'
            . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
