<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_number',
        'sales_person_id',
        'full_name',
        'position',
        'join_date',
        'birth_date',
        'phone',
        'address',
        'bank_name',
        'account_number',
        'basic_salary',
        'is_active',
    ];

    public function salesPerson()
    {
        return $this->belongsTo(SalesPerson::class);
    }

    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function salesAsStaff()
    {
        return $this->hasMany(Sale::class, 'sales_person_id', 'sales_person_id');
    }

    public function salesAsTechnician()
    {
        return $this->hasMany(Sale::class, 'technician_id');
    }

    // Generate nomor pegawai otomatis
    public static function generateEmployeeNumber()
    {
        $lastEmployee = self::orderBy('id', 'desc')->first();
        $number = $lastEmployee ? intval(substr($lastEmployee->employee_number, 3)) + 1 : 1;
        return 'EMP' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
