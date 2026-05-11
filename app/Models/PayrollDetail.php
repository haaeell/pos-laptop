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
