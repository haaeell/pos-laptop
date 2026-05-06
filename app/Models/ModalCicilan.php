<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModalCicilan extends Model
{
    protected $table = 'modal_cicilans';

    protected $fillable = [
        'modal_id',
        'cicilan_ke',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'nominal_pokok',
        'nominal_bunga',
        'nominal_cicilan',
        'denda',
        'total_bayar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar'       => 'date',
        'nominal_pokok'       => 'decimal:2',
        'nominal_bunga'       => 'decimal:2',
        'nominal_cicilan'     => 'decimal:2',
        'denda'               => 'decimal:2',
        'total_bayar'         => 'decimal:2',
    ];

    public function modal(): BelongsTo
    {
        return $this->belongsTo(Modal::class, 'modal_id');
    }
}
