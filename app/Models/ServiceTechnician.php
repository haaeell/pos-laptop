<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTechnician extends Model
{
    protected $fillable = ['service_id', 'employee_id', 'fee_share'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
