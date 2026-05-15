<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_number',
        'customer_name',
        'customer_phone',
        'device_type',
        'device_brand',
        'device_sn',
        'complaint',
        'notes',
        'technician_notes',
        'spare_parts',
        'spare_part_cost',  // total harga jual seluruh sparepart
        'spare_part_hpp',   // total HPP seluruh sparepart
        'service_cost',     // biaya jasa
        'total_cost',       // spare_part_cost + service_cost
        'status',
        'estimated_done',
        'done_at',
        'taken_at',
        'created_by',
    ];

    protected $casts = [
        'spare_parts' => 'array',
        'done_at'     => 'datetime',
        'taken_at'    => 'datetime',
    ];
    public function technicians()
    {
        return $this->hasMany(ServiceTechnician::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSparePartProfitAttribute(): int
    {
        return (int) ($this->spare_part_cost - $this->spare_part_hpp);
    }

    public function getRevenueBreakdownAttribute(): array
    {
        return [
            'spare_part_revenue' => (int) $this->spare_part_cost,
            'spare_part_hpp'     => (int) $this->spare_part_hpp,
            'spare_part_profit'  => $this->spare_part_profit,
            'service_cost'       => (int) $this->service_cost,
            'total_in'           => (int) $this->total_cost,
        ];
    }


    public static function generateServiceNumber(): string
    {
        $prefix = 'SVC-' . date('Y');
        $last   = static::where('service_number', 'like', $prefix . '%')
            ->orderByDesc('service_number')
            ->value('service_number');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending'     => 'Menunggu Estimasi',
            'estimated'   => 'Estimasi Diberikan',
            'approved'    => 'Disetujui',
            'rejected'    => 'Dibatalkan',
            'in_progress' => 'Sedang Dikerjakan',
            'done'        => 'Selesai',
            'taken'       => 'Sudah Diambil',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'pending'     => 'bg-slate-100 text-slate-600',
            'estimated'   => 'bg-yellow-100 text-yellow-700',
            'approved'    => 'bg-blue-100 text-blue-700',
            'rejected'    => 'bg-red-100 text-red-600',
            'in_progress' => 'bg-indigo-100 text-indigo-700',
            'done'        => 'bg-green-100 text-green-700',
            'taken'       => 'bg-emerald-100 text-emerald-700',
        ][$this->status] ?? 'bg-slate-100 text-slate-600';
    }
}
