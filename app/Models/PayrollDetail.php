<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $fillable = [
        'payroll_id',
        'employee_id',
        'basic_salary',
        'sales_bonus',
        'sales_profit_share',
        'rental_share',
        'sales_transaction_count',
        'rental_transaction_count',
        'technician_fee',
        'other_allowance',
        'deduction',
        'net_salary',
        'total_transactions',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
