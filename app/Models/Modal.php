<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modal extends Model
{
    use SoftDeletes;

    protected $table = 'modals';

    protected $fillable = [
        'kode_pinjaman',
        'nama_pemberi_pinjaman',
        'jenis_pinjaman',
        'jumlah_pinjaman',
        'nominal_pencairan',
        'tenor',
        'satuan_tenor',
        'total_bunga',
        'total_kewajiban',
        'cicilan_per_periode',
        'tanggal_pinjaman',
        'tanggal_pencairan',
        'tanggal_jatuh_tempo',
        'cicilan_ke',
        'total_terbayar',
        'sisa_kewajiban',
        'status',
        'keterangan',
        'sudah_dicairkan',
    ];

    protected $casts = [
        'jumlah_pinjaman'     => 'decimal:2',
        'nominal_pencairan'   => 'decimal:2',
        'bunga_persen'        => 'decimal:4',
        'total_bunga'         => 'decimal:2',
        'total_kewajiban'     => 'decimal:2',
        'cicilan_per_periode' => 'decimal:2',
        'total_terbayar'      => 'decimal:2',
        'sisa_kewajiban'      => 'decimal:2',
        'tanggal_pinjaman'    => 'date',
        'tanggal_pencairan'   => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'sudah_dicairkan'     => 'boolean',
    ];

    public function cicilans(): HasMany
    {
        return $this->hasMany(ModalCicilan::class, 'modal_id');
    }

    public function getProgressPersen(): float
    {
        if ($this->tenor == 0) return 0;
        return round(($this->cicilan_ke / $this->tenor) * 100, 1);
    }

    public static function generateKode(): string
    {
        $prefix = 'MDL-' . date('Ym') . '-';
        $last = static::withTrashed()
            ->where('kode_pinjaman', 'like', $prefix . '%')
            ->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->kode_pinjaman, -4)) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /** Hitung jadwal cicilan berdasarkan jenis bunga */
    public static function hitungCicilan(array $data): array
    {
        $pokok        = $data['nominal_pencairan'];
        $bungaPersen  = $data['bunga_persen'] / 100;
        $tenor        = $data['tenor'];
        $jenisBunga   = $data['jenis_bunga'];

        $jadwal = [];

        if ($jenisBunga === 'flat') {
            $bungaPerPeriode = $pokok * $bungaPersen;
            $pokokPerPeriode = $pokok / $tenor;
            $cicilan         = $pokokPerPeriode + $bungaPerPeriode;

            for ($i = 1; $i <= $tenor; $i++) {
                $jadwal[] = [
                    'cicilan_ke'     => $i,
                    'nominal_pokok'  => round($pokokPerPeriode, 2),
                    'nominal_bunga'  => round($bungaPerPeriode, 2),
                    'nominal_cicilan' => round($cicilan, 2),
                ];
            }
        } elseif ($jenisBunga === 'efektif') {
            $sisaPokok = $pokok;
            for ($i = 1; $i <= $tenor; $i++) {
                $bunga   = $sisaPokok * $bungaPersen;
                $pokokBayar = $pokok / $tenor;
                $jadwal[] = [
                    'cicilan_ke'     => $i,
                    'nominal_pokok'  => round($pokokBayar, 2),
                    'nominal_bunga'  => round($bunga, 2),
                    'nominal_cicilan' => round($pokokBayar + $bunga, 2),
                ];
                $sisaPokok -= $pokokBayar;
            }
        } else { // anuitas
            if ($bungaPersen == 0) {
                $cicilan = $pokok / $tenor;
                for ($i = 1; $i <= $tenor; $i++) {
                    $jadwal[] = [
                        'cicilan_ke'     => $i,
                        'nominal_pokok'  => round($cicilan, 2),
                        'nominal_bunga'  => 0,
                        'nominal_cicilan' => round($cicilan, 2),
                    ];
                }
            } else {
                $cicilan   = $pokok * ($bungaPersen * pow(1 + $bungaPersen, $tenor)) / (pow(1 + $bungaPersen, $tenor) - 1);
                $sisaPokok = $pokok;
                for ($i = 1; $i <= $tenor; $i++) {
                    $bunga      = $sisaPokok * $bungaPersen;
                    $pokokBayar = $cicilan - $bunga;
                    $jadwal[] = [
                        'cicilan_ke'     => $i,
                        'nominal_pokok'  => round($pokokBayar, 2),
                        'nominal_bunga'  => round($bunga, 2),
                        'nominal_cicilan' => round($cicilan, 2),
                    ];
                    $sisaPokok -= $pokokBayar;
                }
            }
        }

        $totalBunga    = array_sum(array_column($jadwal, 'nominal_bunga'));
        $totalKewajiban = $pokok + $totalBunga;

        return [
            'jadwal'          => $jadwal,
            'total_bunga'     => round($totalBunga, 2),
            'total_kewajiban' => round($totalKewajiban, 2),
            'cicilan_per_periode' => round($jadwal[0]['nominal_cicilan'] ?? 0, 2),
        ];
    }
}
